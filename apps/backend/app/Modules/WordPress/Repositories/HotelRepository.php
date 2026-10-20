<?php

namespace App\Modules\WordPress\Repositories;

use App\Models\Hotel;
use App\Models\HotelSlug;
use App\Modules\WordPress\DTOs\HotelSyncDTO;
use Illuminate\Database\Eloquent\Collection;

class HotelRepository
{
    /**
     * Find hotel by WordPress post ID
     */
    public function findByWpPostId(int $wpPostId): ?Hotel
    {
        return Hotel::where('wp_post_id', $wpPostId)->first();
    }

    /**
     * Find hotel by slug
     */
    public function findBySlug(string $slug): ?Hotel
    {
        return Hotel::where('slug', $slug)->first();
    }

    /**
     * Find hotel by old slug (for redirects)
     */
    public function findByOldSlug(string $slug): ?Hotel
    {
        $hotelSlug = HotelSlug::where('slug', $slug)->first();
        return $hotelSlug?->hotel;
    }

    /**
     * Create new hotel from DTO
     */
    public function create(HotelSyncDTO $dto): Hotel
    {
        return Hotel::create(array_merge(
            $dto->toHotelArray(),
            [
                'display_status' => 'pending_review',
                'sync_status' => 'synced',
                'content_source' => 'wordpress',
                'wp_last_synced_at' => now(),
            ]
        ));
    }

    /**
     * Update existing hotel from DTO
     */
    public function update(Hotel $hotel, HotelSyncDTO $dto): Hotel
    {
        // Check if slug changed
        if ($hotel->slug !== $dto->slug) {
            $this->saveOldSlug($hotel);
        }

        $hotel->update(array_merge(
            $dto->toHotelArray(),
            [
                'sync_status' => 'synced',
                'wp_last_synced_at' => now(),
            ]
        ));

        return $hotel->fresh();
    }

    /**
     * Save old slug before changing
     */
    protected function saveOldSlug(Hotel $hotel): void
    {
        HotelSlug::create([
            'hotel_id' => $hotel->id,
            'slug' => $hotel->slug,
            'changed_at' => now(),
        ]);
    }

    /**
     * Soft delete hotel (don't hard delete if has relations)
     */
    public function softDelete(Hotel $hotel): void
    {
        $hotel->update([
            'publish_status' => 'deleted',
            'display_status' => 'inactive',
            'sync_status' => 'synced',
        ]);

        $hotel->delete(); // soft delete
    }

    /**
     * Get all published hotels
     */
    public function getPublished(): Collection
    {
        return Hotel::published()->get();
    }

    /**
     * Get hotels that need update
     */
    public function getNeedsUpdate(): Collection
    {
        return Hotel::where('display_status', 'needs_update')
            ->orWhere('sync_status', 'out_of_sync')
            ->get();
    }

    /**
     * Count hotels by status
     */
    public function countByStatus(): array
    {
        return [
            'total' => Hotel::count(),
            'published' => Hotel::published()->count(),
            'draft' => Hotel::where('publish_status', 'draft')->count(),
            'deleted' => Hotel::onlyTrashed()->count(),
            'needs_update' => Hotel::where('display_status', 'needs_update')->count(),
        ];
    }
}