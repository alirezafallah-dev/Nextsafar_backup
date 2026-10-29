<?php

namespace App\Modules\Visa\Controllers;

use App\Core\ApiResponse;
use App\Exceptions\ApiException;
use App\Modules\Visa\Models\VisaType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VisaTypeController extends Controller
{
    /**
     * GET /api/v1/visa-types
     * لیست انواع ویزا (عمومی)
     */
    public function index(Request $request): JsonResponse
    {
        $query = VisaType::available()
            ->with(['country:id,name,flag_emoji,code'])
            ->withCount('checklist');

        // فیلتر بر اساس کشور
        if ($countrySlug = $request->get('country')) {
            $query->byCountrySlug($countrySlug);
        }

        // فیلتر بر اساس دسته‌بندی
        if ($category = $request->get('category')) {
            $query->ofCategory(\App\Modules\Visa\Enums\VisaCategory::from($category));
        }

        // فقط ویژه‌ها
        if ($request->boolean('featured')) {
            $query->featured();
        }

        $visaTypes = $query->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return ApiResponse::success($visaTypes->items())
            ->withPagination($visaTypes)
            ->toResponse($request);
    }

    /**
     * GET /api/v1/visa-types/{slug}
     * جزئیات نوع ویزا (عمومی)
     */
    public function show(string $slug): JsonResponse
    {
        $visaType = VisaType::available()
            ->where('slug', $slug)
            ->with(['country:id,name,flag_emoji,code', 'checklist', 'rules'])
            ->first();

        if (!$visaType) {
            throw ApiException::notFound('نوع ویزا');
        }

        return ApiResponse::success($visaType)->toResponse(request());
    }
}
