<?php

namespace App\Modules\Payment\Controllers;

use App\Core\ApiResponse;
use App\Exceptions\ApiException;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Models\Refund;
use App\Modules\Payment\Services\RefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RefundController extends Controller
{
    public function __construct(
        protected RefundService $refundService,
    ) {}

    // =====================================================================
    // USER ENDPOINTS
    // =====================================================================

    /**
     * POST /api/v1/orders/{order}/refund-request
     * درخواست استرداد توسط کاربر
     */
    public function requestForOrder(Request $request, Order $order): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        // فقط مالک سفارش
        if ($order->user_id !== auth()->id() && !auth()->user()->hasPermission('refunds.create')) {
            throw ApiException::forbidden();
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
            'amount' => ['nullable', 'integer', 'min:1'],
            'type' => ['nullable', 'in:full,partial'],
        ]);

        $refund = $this->refundService->requestRefundForOrder(
            $order,
            $validated['reason'],
            $validated['amount'] ?? null
        );

        return ApiResponse::created($refund, 'درخواست استرداد شما ثبت شد. به زودی بررسی خواهد شد.')
            ->toResponse($request);
    }

    /**
     * GET /api/v1/refunds
     * لیست استرداد‌های کاربر
     */
    public function index(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $refunds = Refund::forUser(auth()->id())
            ->with(['order:id,code', 'payment:id,code'])
            ->latest('requested_at')
            ->paginate($request->get('per_page', 20));

        return ApiResponse::success($refunds->items())
            ->withPagination($refunds)
            ->toResponse($request);
    }

    /**
     * GET /api/v1/refunds/{refund}
     * جزئیات استرداد (فقط برای مالک)
     */
    public function show(Refund $refund): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        if ($refund->user_id !== auth()->id() && !auth()->user()->hasPermission('refunds.view')) {
            throw ApiException::forbidden();
        }

        $refund->load(['order:id,code', 'payment:id,code', 'user:id,name']);

        return ApiResponse::success($refund)->toResponse(request());
    }

    /**
     * POST /api/v1/refunds/{refund}/cancel
     * لغو درخواست توسط کاربر
     */
    public function cancel(Refund $refund): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $refund = $this->refundService->cancelRefund($refund);

        return ApiResponse::success($refund, 'درخواست استرداد لغو شد.')
            ->toResponse(request());
    }

    // =====================================================================
    // ADMIN ENDPOINTS
    // =====================================================================

    /**
     * GET /api/v1/admin/refunds
     * لیست همه استرداد‌ها
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Refund::with(['user:id,name,phone', 'order:id,code', 'payment:id,code'])
            ->latest('requested_at');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($userId = $request->get('user_id')) {
            $query->forUser((int) $userId);
        }

        if ($orderId = $request->get('order_id')) {
            $query->forOrder((int) $orderId);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('reason', 'ilike', "%{$search}%");
            });
        }

        $refunds = $query->paginate($request->get('per_page', 20));

        return ApiResponse::success($refunds->items())
            ->withPagination($refunds)
            ->withMeta([
                'statistics' => $this->refundService->getStatistics(),
            ])
            ->toResponse($request);
    }

    /**
     * GET /api/v1/admin/refunds/statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->refundService->getStatistics(
            $request->get('date_from'),
            $request->get('date_to')
        );

        return ApiResponse::success($stats)->toResponse($request);
    }

    /**
     * POST /api/v1/admin/refunds/{refund}/start-review
     */
    public function startReview(Refund $refund): JsonResponse
    {
        $refund = $this->refundService->startReview($refund);
        return ApiResponse::success($refund, 'بررسی استرداد شروع شد.')
            ->toResponse(request());
    }

    /**
     * POST /api/v1/admin/refunds/{refund}/approve
     */
    public function approve(Request $request, Refund $refund): JsonResponse
    {
        $validated = $request->validate([
            'penalty_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'penalty_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $refund = $this->refundService->approveRefund(
            $refund,
            $validated['penalty_percentage'] ?? null,
            $validated['penalty_reason'] ?? null
        );

        return ApiResponse::success($refund, 'استرداد تأیید شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/refunds/{refund}/reject
     */
    public function reject(Request $request, Refund $refund): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $refund = $this->refundService->rejectRefund($refund, $validated['reason']);

        return ApiResponse::success($refund, 'درخواست استرداد رد شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/refunds/{refund}/process
     */
    public function process(Refund $refund): JsonResponse
    {
        $refund = $this->refundService->processRefund($refund);
        return ApiResponse::success($refund, 'پردازش بازگشت وجه شروع شد.')
            ->toResponse(request());
    }

    /**
     * POST /api/v1/admin/refunds/{refund}/complete
     */
    public function complete(Request $request, Refund $refund): JsonResponse
    {
        $validated = $request->validate([
            'gateway_ref_id' => ['nullable', 'string', 'max:100'],
        ]);

        $refund = $this->refundService->completeRefund(
            $refund,
            $validated['gateway_ref_id'] ?? null
        );

        return ApiResponse::success($refund, 'بازگشت وجه تکمیل شد.')
            ->toResponse($request);
    }
}
