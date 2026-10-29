<?php

namespace App\Modules\Tour\Controllers;

use App\Core\ApiResponse;
use App\Exceptions\ApiException;
use App\Modules\Tour\Models\Tour;
use App\Modules\Tour\Requests\FilterToursRequest;
use App\Modules\Tour\Requests\StoreTourRequest;
use App\Modules\Tour\Requests\UpdateTourRequest;
use App\Modules\Tour\Repositories\TourRepository;
use App\Modules\Tour\Services\TourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class TourController extends Controller
{
    public function __construct(
        protected TourService $tourService,
        protected TourRepository $repository,
    ) {}

    /**
     * GET /api/v1/tours
     * List published tours with filters
     */
    public function index(FilterToursRequest $request): JsonResponse
    {
        $filters = $request->getFilters();
        $perPage = $request->input('per_page', 12);

        $tours = $this->repository->filter($filters, $perPage);

        return ApiResponse::success($tours->items())
            ->withPagination($tours)
            ->withMeta([
                'filters_applied' => $filters,
                'categories' => $this->repository->getCategoriesWithCount(),
                'countries' => $this->repository->getUniqueCountries(),
            ])
            ->toResponse($request);
    }

    /**
     * GET /api/v1/tours/featured
     * Get featured tours
     */
    public function featured(): JsonResponse
    {
        $tours = $this->repository->getFeatured(6);
        return ApiResponse::success($tours)->toResponse(request());
    }

    /**
     * GET /api/v1/tours/{slug}
     * Get tour details by slug
     */
    public function show(string $slug): JsonResponse
    {
        $tour = $this->tourService->getPublicTour($slug);

        if (!$tour) {
            throw ApiException::notFound('تور', $slug);
        }

        return ApiResponse::success($tour)->toResponse(request());
    }

    /**
     * GET /api/v1/tours/{tour}/dates
     * Get available dates for a tour
     */
    public function dates(Tour $tour): JsonResponse
    {
        if (!$tour->isPublished()) {
            throw ApiException::notFound('تور');
        }

        $dates = $this->tourService->getAvailableDates($tour);
        return ApiResponse::success($dates)->toResponse(request());
    }

    /**
     * POST /api/v1/admin/tours
     * Create new tour (admin)
     */
    public function store(StoreTourRequest $request): JsonResponse
    {
        $tour = $this->tourService->createTour($request->validated());
        return ApiResponse::created($tour, 'تور با موفقیت ایجاد شد.')->toResponse($request);
    }

    /**
     * PUT /api/v1/admin/tours/{tour}
     * Update tour (admin)
     */
    public function update(UpdateTourRequest $request, Tour $tour): JsonResponse
    {
        $tour = $this->tourService->updateTour($tour, $request->validated());
        return ApiResponse::success($tour, 'تور با موفقیت بروزرسانی شد.')->toResponse($request);
    }

    /**
     * DELETE /api/v1/admin/tours/{tour}
     * Delete tour (admin)
     */
    public function destroy(Tour $tour): JsonResponse
    {
        $this->tourService->deleteTour($tour);
        return ApiResponse::noContent()->toResponse(request());
    }

    /**
     * POST /api/v1/admin/tours/{tour}/publish
     * Publish tour (admin)
     */
    public function publish(Tour $tour): JsonResponse
    {
        $tour = $this->tourService->publishTour($tour);
        return ApiResponse::success($tour, 'تور با موفقیت منتشر شد.')->toResponse(request());
    }

    /**
     * POST /api/v1/admin/tours/{tour}/unpublish
     * Unpublish tour (admin)
     */
    public function unpublish(Tour $tour): JsonResponse
    {
        $tour = $this->tourService->unpublishTour($tour);
        return ApiResponse::success($tour, 'تور از حالت انتشار خارج شد.')->toResponse($request);
    }

    /**
     * POST /api/v1/admin/tours/{tour}/dates
     * Add date to tour (admin)
     */
    public function addDate(Tour $tour, \Illuminate\Http\Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departure_date' => ['required', 'date', 'after:today'],
            'return_date' => ['required', 'date', 'after:departure_date'],
            'total_capacity' => ['required', 'integer', 'min:1'],
            'adult_price' => ['required', 'integer', 'min:0'],
            'child_price' => ['nullable', 'integer', 'min:0'],
            'infant_price' => ['nullable', 'integer', 'min:0'],
            'single_supplement' => ['nullable', 'integer', 'min:0'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_guaranteed' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $date = $this->tourService->addDate($tour, $validated);
        return ApiResponse::created($date, 'تاریخ جدید اضافه شد.')->toResponse($request);
    }

    /**
     * PUT /api/v1/admin/tours/dates/{date}
     * Update tour date (admin)
     */
    public function updateDate(\App\Modules\Tour\Models\TourDate $date, \Illuminate\Http\Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departure_date' => ['sometimes', 'date'],
            'return_date' => ['sometimes', 'date', 'after:departure_date'],
            'total_capacity' => ['sometimes', 'integer', 'min:1'],
            'adult_price' => ['sometimes', 'integer', 'min:0'],
            'child_price' => ['nullable', 'integer', 'min:0'],
            'infant_price' => ['nullable', 'integer', 'min:0'],
            'single_supplement' => ['nullable', 'integer', 'min:0'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'status' => ['nullable', 'in:available,limited,sold_out,cancelled,expired'],
            'is_guaranteed' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $date = $this->tourService->updateDate($date, $validated);
        return ApiResponse::success($date, 'تاریخ بروزرسانی شد.')->toResponse($request);
    }

    /**
     * DELETE /api/v1/admin/tours/dates/{date}
     * Delete tour date (admin)
     */
    public function deleteDate(\App\Modules\Tour\Models\TourDate $date): JsonResponse
    {
        $this->tourService->deleteDate($date);
        return ApiResponse::noContent()->toResponse(request());
    }

    /**
     * GET /api/v1/admin/tours/statistics
     * Get tour statistics (admin)
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->repository->getStatistics();
        return ApiResponse::success($stats)->toResponse(request());
    }
}
