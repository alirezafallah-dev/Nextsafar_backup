<?php

namespace App\Modules\Tour\Repositories;

use App\Modules\Tour\Models\Tour;
use App\Modules\Tour\Models\TourCategory;
use App\Modules\Tour\Models\TourDate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TourRepository
{
    /**
     * Get all published tours with pagination
     */
    public function getPublished(int $perPage = 12): LengthAwarePaginator
    {
        return Tour::published()
            ->with(['category', 'coverImage', 'mainHotel.hotel'])
            ->withMinPrice()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    /**
     * Get featured tours
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return Tour::published()
            ->featured()
            ->with(['category', 'coverImage'])
            ->withMinPrice()
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Find tour by slug (with redirect support)
     */
    public function findBySlug(string $slug): ?Tour
    {
        return Tour::findBySlugOrRedirect($slug);
    }

    /**
     * Find tour by ID with full details
     */
    public function findByIdWithDetails(int $id): ?Tour
    {
        return Tour::with([
            'category',
            'dates' => fn($q) => $q->available()->orderBy('departure_date'),
            'itineraries',
            'includedServices',
            'excludedServices',
            'tourHotels.hotel',
            'destinations',
            'media',
            'relatedTours.relatedTour.coverImage',
        ])->find($id);
    }

    /**
     * Get tours by category
     */
    public function getByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator
    {
        return Tour::published()
            ->byCategory($categoryId)
            ->with(['category', 'coverImage'])
            ->withMinPrice()
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    /**
     * Get tours by country
     */
    public function getByCountry(string $country, int $perPage = 12): LengthAwarePaginator
    {
        return Tour::published()
            ->byCountry($country)
            ->with(['category', 'coverImage'])
            ->withMinPrice()
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    /**
     * Search tours
     */
    public function search(string $term, int $perPage = 12): LengthAwarePaginator
    {
        return Tour::published()
            ->search($term)
            ->with(['category', 'coverImage'])
            ->withMinPrice()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    /**
     * Advanced filter for tours
     */
    public function filter(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = Tour::published()
            ->with(['category', 'coverImage'])
            ->withMinPrice();

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->byCategory($filters['category_id']);
        }

        // Country filter
        if (!empty($filters['country'])) {
            $query->byCountry($filters['country']);
        }

        // City filter
        if (!empty($filters['city'])) {
            $query->byCity($filters['city']);
        }

        // Price range filter
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->whereHas('dates', function ($q) use ($filters) {
                $q->available();
                if (isset($filters['min_price'])) {
                    $q->where('adult_price', '>=', $filters['min_price']);
                }
                if (isset($filters['max_price'])) {
                    $q->where('adult_price', '<=', $filters['max_price']);
                }
            });
        }

        // Duration filter
        if (!empty($filters['min_duration'])) {
            $query->where('duration_days', '>=', $filters['min_duration']);
        }
        if (!empty($filters['max_duration'])) {
            $query->where('duration_days', '<=', $filters['max_duration']);
        }

        // Date range filter
        if (!empty($filters['departure_from']) || !empty($filters['departure_to'])) {
            $query->whereHas('dates', function ($q) use ($filters) {
                $q->available();
                if (!empty($filters['departure_from'])) {
                    $q->where('departure_date', '>=', $filters['departure_from']);
                }
                if (!empty($filters['departure_to'])) {
                    $q->where('departure_date', '<=', $filters['departure_to']);
                }
            });
        }

        // Featured only
        if (!empty($filters['featured_only'])) {
            $query->featured();
        }

        // Guaranteed only
        if (!empty($filters['guaranteed_only'])) {
            $query->whereHas('dates', function ($q) {
                $q->available()->guaranteed();
            });
        }

        // Sort
        $sort = $filters['sort'] ?? 'newest';
        $query = $this->applySort($query, $sort);

        return $query->paginate($perPage);
    }

    /**
     * Apply sorting to query
     */
    protected function applySort(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'newest' => $query->orderByDesc('published_at'),
            'oldest' => $query->orderBy('published_at'),
            'price_asc' => $query->orderBy('min_price'),
            'price_desc' => $query->orderByDesc('min_price'),
            'duration_asc' => $query->orderBy('duration_days'),
            'duration_desc' => $query->orderByDesc('duration_days'),
            'popular' => $query->orderByDesc('is_featured')->orderByDesc('published_at'),
            default => $query->orderByDesc('published_at'),
        };
    }

    /**
     * Get all active categories with tour count
     */
    public function getCategoriesWithCount(): Collection
    {
        return TourCategory::active()
            ->root()
            ->withCount(['tours' => function ($q) {
                $q->published();
            }])
            ->ordered()
            ->get();
    }

    /**
     * Get unique countries from published tours
     */
    public function getUniqueCountries(): array
    {
        return Tour::published()
            ->distinct()
            ->pluck('destination_country')
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Get unique cities for a country
     */
    public function getCitiesByCountry(string $country): array
    {
        return Tour::published()
            ->byCountry($country)
            ->distinct()
            ->pluck('destination_city')
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Get dashboard statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Tour::count(),
            'published' => Tour::published()->count(),
            'draft' => Tour::draft()->count(),
            'featured' => Tour::featured()->count(),
            'categories' => TourCategory::active()->count(),
            'upcoming_dates' => TourDate::available()->where('departure_date', '<=', now()->addMonth())->count(),
            'sold_out' => Tour::byStatus(Tour::STATUS_SOLD_OUT)->count(),
        ];
    }

    /**
     * Create a new tour
     */
    public function create(array $data): Tour
    {
        return Tour::create($data);
    }

    /**
     * Update an existing tour
     */
    public function update(Tour $tour, array $data): Tour
    {
        $tour->update($data);
        return $tour->fresh();
    }

    /**
     * Delete a tour (soft delete)
     */
    public function delete(Tour $tour): bool
    {
        return $tour->delete();
    }
}
