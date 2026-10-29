<?php

namespace App\Modules\Order\Controllers;

use App\Core\ApiResponse;
use App\Exceptions\ApiException;
use App\Modules\Lead\Models\Lead;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use App\Modules\Order\Models\OrderPassenger;
use App\Modules\Order\Repositories\OrderRepository;
use App\Modules\Order\Requests\AddItemRequest;
use App\Modules\Order\Requests\AddPassengerRequest;
use App\Modules\Order\Requests\ChangeStatusRequest;
use App\Modules\Order\Requests\ConvertLeadRequest;
use App\Modules\Order\Requests\FilterOrdersRequest;
use App\Modules\Order\Requests\StoreOrderRequest;
use App\Modules\Order\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected OrderRepository $repository,
    ) {}

    // =====================================================================
    // PUBLIC ENDPOINTS (برای کاربر لاگین‌کرده)
    // =====================================================================

    /**
     * GET /api/v1/orders
     * لیست سفارش‌های کاربر جاری
     */
    public function myOrders(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $orders = $this->repository->getForUser(
            auth()->id(),
            $request->get('per_page', 20)
        );

        return ApiResponse::success($orders->items())
            ->withPagination($orders)
            ->toResponse($request);
    }

    /**
     * GET /api/v1/orders/{order}
     * جزئیات سفارش کاربر جاری
     */
    public function myShow(Order $order): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        // بررسی مالکیت
        if ($order->user_id !== auth()->id() && !auth()->user()->hasPermission('bookings.view')) {
            throw ApiException::forbidden();
        }

        $order = $this->repository->findWithDetails($order->id);
        return ApiResponse::success($order)->toResponse(request());
    }

    /**
     * POST /api/v1/orders/{order}/refund-request
     * درخواست استرداد توسط کاربر
     */
    public function requestRefund(Request $request, Order $order): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        if ($order->user_id !== auth()->id()) {
            throw ApiException::forbidden();
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $order = $this->orderService->requestRefund(
            $order,
            $validated['reason'],
            auth()->user()
        );

        return ApiResponse::success($order, 'درخواست استرداد شما ثبت شد.')
            ->toResponse($request);
    }

    // =====================================================================
    // ADMIN ENDPOINTS
    // =====================================================================

    /**
     * GET /api/v1/admin/orders
     * لیست همه سفارش‌ها با فیلتر
     */
    public function index(FilterOrdersRequest $request): JsonResponse
    {
        $orders = $this->repository->filter(
            $request->validated(),
            $request->get('per_page', 20)
        );

        return ApiResponse::success($orders->items())
            ->withPagination($orders)
            ->withMeta([
                'statistics' => $this->repository->getStatistics(),
            ])
            ->toResponse($request);
    }

    /**
     * GET /api/v1/admin/orders/statistics
     * آمار داشبورد
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->repository->getStatistics(
            $request->get('date_from'),
            $request->get('date_to')
        );

        return ApiResponse::success($stats)->toResponse($request);
    }

    /**
     * GET /api/v1/admin/orders/{order}
     * جزئیات کامل سفارش (ادمین)
     */
    public function show(Order $order): JsonResponse
    {
        $order = $this->repository->findWithDetails($order->id);
        return ApiResponse::success($order)->toResponse(request());
    }

    /**
     * POST /api/v1/admin/orders
     * ایجاد سفارش جدید (ادمین)
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->validated(),
            auth()->user()
        );

        return ApiResponse::created($order, 'سفارش با موفقیت ایجاد شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/orders/convert-lead/{lead}
     * تبدیل لید به سفارش
     */
    public function convertLead(ConvertLeadRequest $request, Lead $lead): JsonResponse
    {
        $order = $this->orderService->convertLeadToOrder(
            $lead,
            $request->validated(),
            auth()->user()
        );

        return ApiResponse::created($order, 'درخواست با موفقیت به سفارش تبدیل شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/orders/{order}/status
     * تغییر وضعیت سفارش
     */
    public function changeStatus(ChangeStatusRequest $request, Order $order): JsonResponse
    {
        $order = $this->orderService->changeStatus(
            $order,
            $request->validated()['status'],
            $request->validated()['reason'] ?? null,
            auth()->user(),
            'manual'
        );

        return ApiResponse::success($order, 'وضعیت سفارش تغییر کرد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/orders/{order}/cancel
     * لغو سفارش
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $order = $this->orderService->cancelOrder(
            $order,
            $validated['reason'],
            auth()->user()
        );

        return ApiResponse::success($order, 'سفارش لغو شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/orders/{order}/items
     * افزودن آیتم به سفارش
     */
    public function addItem(AddItemRequest $request, Order $order): JsonResponse
    {
        $item = $this->orderService->addItem($order, $request->validated());

        return ApiResponse::created($item, 'آیتم به سفارش اضافه شد.')
            ->toResponse($request);
    }

    /**
     * DELETE /api/v1/admin/orders/items/{item}
     * حذف آیتم از سفارش
     */
    public function removeItem(OrderItem $item): JsonResponse
    {
        $this->orderService->removeItem($item);
        return ApiResponse::noContent()->toResponse(request());
    }

    /**
     * POST /api/v1/admin/orders/{order}/passengers
     * افزودن مسافر
     */
    public function addPassenger(AddPassengerRequest $request, Order $order): JsonResponse
    {
        $passenger = $this->orderService->addPassenger($order, $request->validated());

        return ApiResponse::created($passenger, 'مسافر به سفارش اضافه شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/orders/{order}/passengers/bulk
     * افزودن چند مسافر یکجا
     */
    public function addPassengersBulk(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.order_item_id' => ['nullable', 'integer', 'exists:order_items,id'],
            'passengers.*.type' => ['required', 'in:adult,child,infant'],
            'passengers.*.first_name' => ['required', 'string', 'max:100'],
            'passengers.*.last_name' => ['required', 'string', 'max:100'],
            'passengers.*.first_name_en' => ['nullable', 'string', 'max:100'],
            'passengers.*.last_name_en' => ['nullable', 'string', 'max:100'],
            'passengers.*.national_id' => ['nullable', 'string', 'max:20'],
            'passengers.*.passport_number' => ['nullable', 'string', 'max:50'],
            'passengers.*.birth_date' => ['nullable', 'date', 'before:today'],
            'passengers.*.gender' => ['nullable', 'in:male,female,other'],
            'passengers.*.nationality' => ['nullable', 'string', 'max:100'],
            'passengers.*.phone' => ['nullable', 'string', 'max:20'],
            'passengers.*.email' => ['nullable', 'email', 'max:150'],
            'passengers.*.price' => ['nullable', 'integer', 'min:0'],
        ]);

        $passengers = $this->orderService->addPassengers($order, $validated['passengers']);

        return ApiResponse::created($passengers, count($passengers) . ' مسافر به سفارش اضافه شد.')
            ->toResponse($request);
    }

    /**
     * DELETE /api/v1/admin/orders/passengers/{passenger}
     * حذف مسافر
     */
    public function removePassenger(OrderPassenger $passenger): JsonResponse
    {
        $this->repository->removePassenger($passenger);
        return ApiResponse::noContent()->toResponse(request());
    }

    /**
     * POST /api/v1/admin/orders/{order}/recalculate
     * محاسبه مجدد مبالغ
     */
    public function recalculate(Order $order): JsonResponse
    {
        $order = $this->orderService->recalculate($order);
        return ApiResponse::success($order, 'مبالغ سفارش مجدداً محاسبه شد.')
            ->toResponse(request());
    }

    /**
     * POST /api/v1/admin/orders/{order}/assign
     * اختصاص سفارش به کارشناس
     */
    public function assign(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'assigned_to' => ['required', 'integer', 'exists:users,id'],
        ]);

        $order = $this->repository->update($order, [
            'assigned_to' => $validated['assigned_to'],
        ]);

        return ApiResponse::success($order, 'سفارش اختصاص داده شد.')
            ->toResponse($request);
    }

    /**
     * DELETE /api/v1/admin/orders/{order}
     * حذف سفارش (فقط draft)
     */
    public function destroy(Order $order): JsonResponse
    {
        if (!$this->repository->delete($order)) {
            throw new ApiException(
                errorCode: \App\Core\ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'فقط سفارش‌های پیش‌نویس قابل حذف هستند.',
                httpStatus: 422,
            );
        }

        return ApiResponse::noContent()->toResponse(request());
    }

    /**
     * POST /api/v1/admin/orders/{order}/initiate-payment
     * شروع فرآیند پرداخت (placeholder برای فاز بعدی)
     */
    public function initiatePayment(Order $order): JsonResponse
    {
        $result = $this->orderService->initiatePayment($order);
        return ApiResponse::success($result)->toResponse(request());
    }
}
