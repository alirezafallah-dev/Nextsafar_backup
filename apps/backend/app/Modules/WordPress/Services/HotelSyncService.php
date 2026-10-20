<?php

namespace App\Modules\WordPress\Services;

use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use App\Models\Hotel;
use App\Models\SyncLog;
use App\Modules\WordPress\DTOs\HotelSyncDTO;
use App\Modules\WordPress\Repositories\HotelRepository;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Hotel Sync Service
 * 
 * مسئول همگام‌سازی هتل‌ها از وردپرس به لاراول.
 * بر اساس بخش ۷.۶ سند: قوانین همگام‌سازی
 */
class HotelSyncService
{
    public function __construct(
        protected HotelRepository $hotelRepository,
    ) {}

    /**
     * Handle hotel created event from WordPress
     */
    public function handleCreated(HotelSyncDTO $dto, SyncLog $syncLog): Hotel
    {
        return DB::transaction(function () use ($dto, $syncLog) {
            $syncLog->markAsProcessing();

            // قانون ۷.۶.۱: اگر پست هتل جدید بود، رکورد هتل در لاراول ساخته شود
            $existingHotel = $this->hotelRepository->findByWpPostId($dto->wpPostId);
            if ($existingHotel) {
                AppLogger::sync()->warning('hotel.sync.duplicate_create', [
                    'wp_post_id' => $dto->wpPostId,
                    'existing_hotel_id' => $existingHotel->id,
                ]);
                // اگر وجود دارد، به‌روزرسانی کن (احتمالاً رویداد duplicate)
                return $this->updateExistingHotel($existingHotel, $dto, $syncLog);
            }

            // بررسی تکراری نبودن اسلاگ
            $existingBySlug = $this->hotelRepository->findBySlug($dto->slug);
            if ($existingBySlug) {
                throw new ApiException(
                    errorCode: ApiErrorCode::VALIDATION_FAILED,
                    userMessage: "هتلی با اسلاگ {$dto->slug} از قبل وجود دارد.",
                    technicalMessage: "Duplicate slug: {$dto->slug}",
                    httpStatus: 409,
                );
            }

            $hotel = $this->hotelRepository->create($dto);

            $syncLog->laravel_record_id = $hotel->id;
            $syncLog->markAsSuccess([
                'hotel_id' => $hotel->id,
                'action' => 'created',
            ]);

            AppLogger::sync()->with(['wp_post_id' => $dto->wpPostId])->info('hotel.sync.created', [
                'hotel_id' => $hotel->id,
                'slug' => $dto->slug,
            ]);

            return $hotel;
        });
    }

    /**
     * Handle hotel updated event from WordPress
     */
    public function handleUpdated(HotelSyncDTO $dto, SyncLog $syncLog): Hotel
    {
        return DB::transaction(function () use ($dto, $syncLog) {
            $syncLog->markAsProcessing();

            $hotel = $this->hotelRepository->findByWpPostId($dto->wpPostId);
            
            if (! $hotel) {
                // قانون: اگر وجود نداشت، ایجاد کن (ممکن است رویداد update قبل از create رسیده باشد)
                AppLogger::sync()->warning('hotel.sync.update_without_create', [
                    'wp_post_id' => $dto->wpPostId,
                ]);
                return $this->handleCreated($dto, $syncLog);
            }

            return $this->updateExistingHotel($hotel, $dto, $syncLog);
        });
    }

    /**
     * Handle hotel deleted event from WordPress
     * قانون ۷.۶.۳: حذف کامل ممنوع، فقط غیرفعال شود
     * قانون ۷.۶.۴: اگر تور یا سفارشی متصل بود، حذف کامل ممنوع
     */
    public function handleDeleted(int $wpPostId, SyncLog $syncLog): ?Hotel
    {
        return DB::transaction(function () use ($wpPostId, $syncLog) {
            $syncLog->markAsProcessing();

            $hotel = $this->hotelRepository->findByWpPostId($wpPostId);
            
            if (! $hotel) {
                AppLogger::sync()->warning('hotel.sync.delete_not_found', [
                    'wp_post_id' => $wpPostId,
                ]);
                $syncLog->markAsSuccess([
                    'action' => 'not_found',
                    'message' => 'Hotel not found in Laravel',
                ]);
                return null;
            }

            // بررسی وابستگی‌ها (تورها، سفارش‌ها)
            // TODO: در فاز ۲ با اضافه شدن ماژول تور، این چک کامل می‌شود
            $hasActiveTours = $this->hasActiveTours($hotel);
            $hasActiveBookings = $this->hasActiveBookings($hotel);

            if ($hasActiveTours || $hasActiveBookings) {
                // فقط غیرفعال کن، حذف نرم نکن
                $hotel->update([
                    'publish_status' => 'trash',
                    'display_status' => 'inactive',
                    'sync_status' => 'synced',
                ]);

                AppLogger::sync()->warning('hotel.sync.soft_disabled_due_to_relations', [
                    'hotel_id' => $hotel->id,
                    'wp_post_id' => $wpPostId,
                    'has_tours' => $hasActiveTours,
                    'has_bookings' => $hasActiveBookings,
                ]);
            } else {
                // حذف نرم (soft delete)
                $this->hotelRepository->softDelete($hotel);

                AppLogger::sync()->info('hotel.sync.soft_deleted', [
                    'hotel_id' => $hotel->id,
                    'wp_post_id' => $wpPostId,
                ]);
            }

            $syncLog->markAsSuccess([
                'hotel_id' => $hotel->id,
                'action' => 'deleted',
            ]);

            return $hotel;
        });
    }

    /**
     * Handle hotel trashed/draft event
     */
    public function handleStatusChange(int $wpPostId, string $newStatus, SyncLog $syncLog): ?Hotel
    {
        $hotel = $this->hotelRepository->findByWpPostId($wpPostId);
        
        if (! $hotel) {
            return null;
        }

        $syncLog->markAsProcessing();

        $hotel->update([
            'publish_status' => $newStatus,
            'display_status' => $newStatus === 'published' ? 'active' : 'inactive',
            'sync_status' => 'synced',
            'wp_last_synced_at' => now(),
        ]);

        $syncLog->markAsSuccess([
            'hotel_id' => $hotel->id,
            'new_status' => $newStatus,
        ]);

        AppLogger::sync()->info('hotel.sync.status_changed', [
            'hotel_id' => $hotel->id,
            'new_status' => $newStatus,
        ]);

        return $hotel->fresh();
    }

    /**
     * Update existing hotel
     */
    protected function updateExistingHotel(Hotel $hotel, HotelSyncDTO $dto, SyncLog $syncLog): Hotel
    {
        // بررسی نسخه محتوا (جلوگیری از همگام‌سازی تکراری - قانون ۷.۶.۷)
        if ($hotel->wp_content_version && $dto->contentVersion && $hotel->wp_content_version === $dto->contentVersion) {
            AppLogger::sync()->info('hotel.sync.skipped_same_version', [
                'hotel_id' => $hotel->id,
                'version' => $dto->contentVersion,
            ]);
            
            $syncLog->markAsSuccess([
                'hotel_id' => $hotel->id,
                'action' => 'skipped',
                'reason' => 'same_version',
            ]);
            
            return $hotel;
        }

        $updatedHotel = $this->hotelRepository->update($hotel, $dto);

        $syncLog->laravel_record_id = $updatedHotel->id;
        $syncLog->markAsSuccess([
            'hotel_id' => $updatedHotel->id,
            'action' => 'updated',
        ]);

        AppLogger::sync()->info('hotel.sync.updated', [
            'hotel_id' => $updatedHotel->id,
            'wp_post_id' => $dto->wpPostId,
        ]);

        return $updatedHotel;
    }

    /**
     * Check if hotel has active tours (TODO: implement in Phase 2)
     */
    protected function hasActiveTours(Hotel $hotel): bool
    {
        // TODO: بعد از ساخت ماژول تور، این متد کامل می‌شود
        return false;
    }

    /**
     * Check if hotel has active bookings (TODO: implement in Phase 4)
     */
    protected function hasActiveBookings(Hotel $hotel): bool
    {
        // TODO: بعد از ساخت ماژول سفارش، این متد کامل می‌شود
        return false;
    }
}
