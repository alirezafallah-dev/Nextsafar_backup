<?php

namespace App\Modules\Tour\Services;

use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use App\Modules\Tour\Models\Tour;
use App\Modules\Tour\Models\TourDate;
use App\Modules\Tour\Models\TourItinerary;
use App\Modules\Tour\Models\TourService as TourServiceModel;
use App\Modules\Tour\Repositories\TourRepository;
use Illuminate\Support\Facades\DB;
use Throwable;

class TourService
{
    public function __construct(
        protected TourRepository $repository,
    ) {}

    /**
     * Create a new tour with all related data
     */
    public function createTour(array $data): Tour
    {
        return DB::transaction(function () use ($data) {
            // Generate unique code and slug
            $data['code'] = Tour::generateUniqueCode();
            $data['slug'] = Tour::generateUniqueSlug($data['title']);
            $data['created_by'] = auth()->id();
            $data['status'] = $data['status'] ?? Tour::STATUS_DRAFT;

            $tour = $this->repository->create($data);

            // Create dates
            if (!empty($data['dates'])) {
                $this->createDates($tour, $data['dates']);
            }

            // Create itineraries
            if (!empty($data['itineraries'])) {
                $this->createItineraries($tour, $data['itineraries']);
            }

            // Create services
            if (!empty($data['services'])) {
                $this->createServices($tour, $data['services']);
            }

            // Attach destinations
            if (!empty($data['destination_ids'])) {
                $tour->destinations()->sync($data['destination_ids']);
            }

            // Attach hotels
            if (!empty($data['hotel_ids'])) {
                $this->attachHotels($tour, $data['hotel_ids']);
            }

            // Calculate min price
            $tour->recalculateMinPrice();

            AppLogger::admin()->info('tour.created', [
                'tour_id' => $tour->id,
                'code' => $tour->code,
                'title' => $tour->title,
            ]);

            return $this->repository->findByIdWithDetails($tour->id);
        });
    }

    /**
     * Update an existing tour
     */
    public function updateTour(Tour $tour, array $data): Tour
    {
        return DB::transaction(function () use ($tour, $data) {
            // Handle slug change
            if (isset($data['title']) && $data['title'] !== $tour->title) {
                $data['slug'] = Tour::generateUniqueSlug($data['title'], $tour->id);
            }

            $data['updated_by'] = auth()->id();

            // Update main tour data
            $this->repository->update($tour, $data);

            // Update dates if provided
            if (isset($data['dates'])) {
                $tour->dates()->delete();
                $this->createDates($tour, $data['dates']);
            }

            // Update itineraries if provided
            if (isset($data['itineraries'])) {
                $tour->itineraries()->delete();
                $this->createItineraries($tour, $data['itineraries']);
            }

            // Update services if provided
            if (isset($data['services'])) {
                $tour->services()->delete();
                $this->createServices($tour, $data['services']);
            }

            // Update destinations
            if (isset($data['destination_ids'])) {
                $tour->destinations()->sync($data['destination_ids']);
            }

            // Update hotels
            if (isset($data['hotel_ids'])) {
                $tour->tourHotels()->delete();
                $this->attachHotels($tour, $data['hotel_ids']);
            }

            // Recalculate min price
            $tour->recalculateMinPrice();

            AppLogger::admin()->info('tour.updated', [
                'tour_id' => $tour->id,
                'code' => $tour->code,
            ]);

            return $this->repository->findByIdWithDetails($tour->id);
        });
    }

    /**
     * Delete a tour (soft delete)
     */
    public function deleteTour(Tour $tour): bool
    {
        // Check if tour has active bookings
        // TODO: بعد از ساخت ماژول سفارش، این چک کامل می‌شود
        $hasBookings = false; // $tour->bookings()->whereIn('status', ['pending', 'confirmed'])->exists();

        if ($hasBookings) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'این تور دارای سفارش فعال است و قابل حذف نیست.',
                httpStatus: 422,
            );
        }

        $result = $this->repository->delete($tour);

        AppLogger::admin()->info('tour.deleted', [
            'tour_id' => $tour->id,
            'code' => $tour->code,
        ]);

        return $result;
    }

    /**
     * Publish a tour
     */
    public function publishTour(Tour $tour): Tour
    {
        // Validation: must have at least one date
        if ($tour->dates()->count() === 0) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'برای انتشار تور حداقل باید یک تاریخ حرکت تعریف شده باشد.',
                httpStatus: 422,
            );
        }

        // Validation: must have at least one available date
        if (!$tour->hasAvailableDates()) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'هیچ تاریخ موجودی برای این تور وجود ندارد.',
                httpStatus: 422,
            );
        }

        $tour->publish();

        AppLogger::admin()->info('tour.published', [
            'tour_id' => $tour->id,
            'code' => $tour->code,
        ]);

        return $tour->fresh();
    }

    /**
     * Unpublish a tour
     */
    public function unpublishTour(Tour $tour): Tour
    {
        $tour->unpublish();

        AppLogger::admin()->info('tour.unpublished', [
            'tour_id' => $tour->id,
            'code' => $tour->code,
        ]);

        return $tour->fresh();
    }

    /**
     * Add a new date to tour
     */
    public function addDate(Tour $tour, array $dateData): TourDate
    {
        $dateData['tour_id'] = $tour->id;
        
        // Validate capacity
        if ($dateData['remaining_capacity'] > $dateData['total_capacity']) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'ظرفیت باقی‌مانده نمی‌تواند بیشتر از ظرفیت کل باشد.',
                httpStatus: 422,
            );
        }

        $date = TourDate::create($dateData);

        // Recalculate tour min price
        $tour->recalculateMinPrice();
        $tour->autoUpdateStatus();

        return $date;
    }

    /**
     * Update a tour date
     */
    public function updateDate(TourDate $date, array $data): TourDate
    {
        // Don't allow capacity changes if there are bookings
        if (isset($data['total_capacity']) && $date->reserved_count > 0) {
            $minCapacity = $date->reserved_count;
            if ($data['total_capacity'] < $minCapacity) {
                throw new ApiException(
                    errorCode: ApiErrorCode::VALIDATION_FAILED,
                    userMessage: "ظرفیت کل نمی‌تواند کمتر از تعداد رزرو شده ({$minCapacity}) باشد.",
                    httpStatus: 422,
                );
            }
            // Auto-adjust remaining
            $data['remaining_capacity'] = $data['total_capacity'] - $date->reserved_count;
        }

        $date->update($data);

        // Recalculate tour min price
        $date->tour->recalculateMinPrice();
        $date->tour->autoUpdateStatus();

        return $date->fresh();
    }

    /**
     * Delete a tour date
     */
    public function deleteDate(TourDate $date): bool
    {
        if ($date->reserved_count > 0) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'این تاریخ دارای رزرو فعال است و قابل حذف نیست.',
                httpStatus: 422,
            );
        }

        $tour = $date->tour;
        $result = $date->delete();

        // Recalculate tour
        $tour->recalculateMinPrice();
        $tour->autoUpdateStatus();

        return $result;
    }

    /**
     * Get tour with full details for public view
     */
    public function getPublicTour(string $slug): ?Tour
    {
        $tour = $this->repository->findBySlug($slug);

        if (!$tour || !$tour->isPublished()) {
            return null;
        }

        return $this->repository->findByIdWithDetails($tour->id);
    }

    /**
     * Get available dates for a tour
     */
    public function getAvailableDates(Tour $tour): \Illuminate\Database\Eloquent\Collection
    {
        return $tour->getAvailableDates();
    }

    /**
     * Calculate total price for a booking
     */
    public function calculateBookingPrice(TourDate $date, array $passengers): array
    {
        $adults = $passengers['adults'] ?? 0;
        $children = $passengers['children'] ?? 0;
        $infants = $passengers['infants'] ?? 0;
        $singleRooms = $passengers['single_rooms'] ?? 0;

        $adultPrice = $date->getFinalAdultPrice() * $adults;
        $childPrice = $date->getFinalChildPrice() * $children;
        $infantPrice = $date->getFinalInfantPrice() * $infants;
        $singleSupplement = $date->single_supplement * $singleRooms;

        $subtotal = $adultPrice + $childPrice + $infantPrice + $singleSupplement;
        $discount = $date->hasActiveDiscount() ? ($adultPrice + $childPrice) - ($adultPrice + $childPrice) : 0; // TODO: real discount calculation
        $total = $subtotal;

        return [
            'adults' => $adults,
            'adult_price' => $date->getFinalAdultPrice(),
            'adult_subtotal' => $adultPrice,
            'children' => $children,
            'child_price' => $date->getFinalChildPrice(),
            'child_subtotal' => $childPrice,
            'infants' => $infants,
            'infant_price' => $date->getFinalInfantPrice(),
            'infant_subtotal' => $infantPrice,
            'single_rooms' => $singleRooms,
            'single_supplement' => $date->single_supplement,
            'single_supplement_subtotal' => $singleSupplement,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'currency' => $date->tour->currency,
        ];
    }

    // === Private Helper Methods ===

    private function createDates(Tour $tour, array $dates): void
    {
        foreach ($dates as $dateData) {
            $dateData['tour_id'] = $tour->id;
            $dateData['remaining_capacity'] = $dateData['remaining_capacity'] ?? $dateData['total_capacity'];
            TourDate::create($dateData);
        }
    }

    private function createItineraries(Tour $tour, array $itineraries): void
    {
        foreach ($itineraries as $itinData) {
            $itinData['tour_id'] = $tour->id;
            TourItinerary::create($itinData);
        }
    }

    private function createServices(Tour $tour, array $services): void
    {
        foreach ($services as $serviceData) {
            $serviceData['tour_id'] = $tour->id;
            TourServiceModel::create($serviceData);
        }
    }

    private function attachHotels(Tour $tour, array $hotels): void
    {
        foreach ($hotels as $index => $hotelData) {
            if (is_numeric($hotelData)) {
                // Simple hotel ID
                $tour->tourHotels()->create([
                    'hotel_id' => $hotelData,
                    'relation_type' => $index === 0 ? 'main' : 'alternative',
                    'priority' => $index + 1,
                ]);
            } else {
                // Full hotel data
                $tour->tourHotels()->create(array_merge($hotelData, [
                    'priority' => $hotelData['priority'] ?? $index + 1,
                    'relation_type' => $hotelData['relation_type'] ?? ($index === 0 ? 'main' : 'alternative'),
                ]));
            }
        }
    }
}
