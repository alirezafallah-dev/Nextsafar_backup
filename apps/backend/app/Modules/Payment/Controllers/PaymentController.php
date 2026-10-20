<?php

namespace App\Modules\Payment\Controllers;

use App\Core\ApiResponse;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Payment Controller
 * 
 * Endpoints برای:
 * - شروع پرداخت (Order و Booking)
 * - دریافت Callback از درگاه (public)
 * - مشاهده وضعیت پرداخت
 * - مدیریت ادمین (reconcile, refund)
 */
class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {}

    // =====================================================================
    // PUBLIC ENDPOINTS
    // =====================================================================

    /**
     * GET|POST /api/v1/payments/callback
     * دریافت کالبک از درگاه (public - بدون auth)
     * 
     * قانون ۱۴.۳.۲: به صفحه بازگشت کاربر اعتماد نمی‌شود
     * این endpoint فقط callback را دریافت می‌کند و تأیید واقعی
     * در verifyFromServer() انجام می‌شود
     */
    public function callback(Request $request): JsonResponse
    {
        $gatewayName = $request->input('gateway', 'zarinpal');

        $result = $this->paymentService->handleCallback($request, $gatewayName);

        // اگر پرداخت موفق بود، URL صفحه success را برگردان
        if ($result['success'] ?? false) {
            $payment = Payment::where('code', $result['payment_code'] ?? '')->first();

            return ApiResponse::success([
                'status' => 'success',
                'payment_code' => $result['payment_code'],
                'reference_id' => $result['reference_id'] ?? null,
                'redirect_url' => $this->getSuccessRedirectUrl($payment),
            ], 'پرداخت با موفقیت تأیید شد.')->toResponse($request);
        }

        // اگر ناموفق بود
        return ApiResponse::success([
            'status' => 'failed',
            'error' => $result['error'] ?? 'unknown',
            'payment_code' => $result['payment_code'] ?? null,
            'redirect_url' => config('app.frontend_url') . '/payment/failed',
        ], $result['message'] ?? 'پرداخت ناموفق بود.')->toResponse($request);
    }

    /**
     * GET /api/v1/payments/verify/{code}
     * صفحه تأیید پرداخت (پس از بازگشت از درگاه)
     * 
     * این endpoint توسط فرانت‌اند فراخوانی می‌شود تا وضعیت نهایی را بگیرد
     */
    public function verify(string $code): JsonResponse
    {
        $payment = Payment::where('code', $code)->first();

        if (!$payment) {
            throw ApiException::notFound('پرداخت', $code);
        }

        // اگر هنوز تأیید نشده، از سرور تأیید کن
        if (!$payment->isVerified() && !$payment->isFinal()) {
            $this->paymentService->verifyFromServer($payment);
            $payment->refresh();
        }

        return ApiResponse::success([
            'payment' => $payment,
            'status' => $payment->status->value,
            'status_label' => $payment->status->label(),
            'is_successful' => $payment->isSuccessful(),
            'is_final' => $payment->isFinal(),
            'reference_id' => $payment->reference_id,
            'order_code' => $payment->order?->code,
            'booking_code' => $payment->booking?->booking_code ?? null,
            'paid_at' => $payment->paid_at,
            'amount' => $payment->formatted_amount,
            'verified_amount' => $payment->formatted_verified_amount,
            'verification_status' => $payment->verification_status,
            'verification_status_label' => $payment->verification_status_label,
        ])->toResponse(request());
    }

    // =====================================================================
    // USER ENDPOINTS (Auth required)
    // =====================================================================

    /**
     * POST /api/v1/orders/{order}/pay
     * شروع پرداخت برای یک سفارش
     */
    public function initiateForOrder(Request $request, Order $order): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        // فقط مالک سفارش می‌تواند پرداخت کند (یا ادمین)
        if ($order->user_id !== auth()->id() && !auth()->user()->hasPermission('bookings.change_status')) {
            throw ApiException::forbidden();
        }

        $validated = $request->validate([
            'gateway' => ['nullable', 'string', 'in:zarinpal'], // در آینده درگاه‌های دیگر
        ]);

        $result = $this->paymentService->initiateForOrder(
            $order,
            $validated['gateway'] ?? 'zarinpal'
        );

        return ApiResponse::success($result, $result['message'] ?? 'در حال انتقال به درگاه...')
            ->toResponse($request);
    }

    /**
     * GET /api/v1/payments
     * لیست پرداخت‌های کاربر جاری
     */
    public function index(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $query = Payment::where('user_id', auth()->id())
            ->with(['order:id,code,status', 'booking:id,booking_code,status'])
            ->latest();

        // فیلتر بر اساس وضعیت
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $payments = $query->paginate($request->get('per_page', 20));

        return ApiResponse::success($payments->items())
            ->withPagination($payments)
            ->toResponse($request);
    }

    /**
     * GET /api/v1/payments/{payment}
     * جزئیات پرداخت (فقط برای مالک یا ادمین)
     */
    public function show(Payment $payment): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        if ($payment->user_id !== auth()->id() && !auth()->user()->hasPermission('payments.view')) {
            throw ApiException::forbidden();
        }

        $payment->load(['order:id,code,status', 'booking:id,booking_code,status', 'user:id,name']);

        return ApiResponse::success($payment)->toResponse(request());
    }

    /**
     * GET /api/v1/payments/{payment}/status
     * استعلام وضعیت پرداخت (polling)
     */
    public function status(Payment $payment): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        if ($payment->user_id !== auth()->id() && !auth()->user()->hasPermission('payments.view')) {
            throw ApiException::forbidden();
        }

        $inquiry = $this->paymentService->inquireStatus($payment);

        return ApiResponse::success([
            'payment_id' => $payment->id,
            'payment_code' => $payment->code,
            'status' => $payment->status->value,
            'status_label' => $payment->status->label(),
            'is_final' => $inquiry['is_final'],
            'verification_status' => $payment->verification_status,
            'amount' => $payment->formatted_amount,
        ])->toResponse(request());
    }

    // =====================================================================
    // ADMIN ENDPOINTS (Auth + Permission)
    // =====================================================================

    /**
     * GET /api/v1/admin/payments
     * لیست همه پرداخت‌ها (ادمین)
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Payment::with(['user:id,name,phone', 'order:id,code', 'booking:id,booking_code'])
            ->latest();

        // فیلترها
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($verificationStatus = $request->get('verification_status')) {
            $query->where('verification_status', $verificationStatus);
        }

        if ($gateway = $request->get('gateway')) {
            $query->where('gateway', $gateway);
        }

        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('reference_id', 'ilike', "%{$search}%")
                    ->orWhere('authority_code', 'ilike', "%{$search}%");
            });
        }

        if ($from = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $payments = $query->paginate($request->get('per_page', 20));

        return ApiResponse::success($payments->items())
            ->withPagination($payments)
            ->withMeta([
                'statistics' => $this->paymentService->getStatistics(),
            ])
            ->toResponse($request);
    }

    /**
     * GET /api/v1/admin/payments/statistics
     * آمار پرداخت‌ها (داشبورد)
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->paymentService->getStatistics(
            $request->get('date_from'),
            $request->get('date_to')
        );

        return ApiResponse::success($stats)->toResponse($request);
    }

    /**
     * POST /api/v1/admin/payments/{payment}/reconcile
     * تطبیق دستی پرداخت (برای موارد requires_reconciliation)
     */
    public function reconcile(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:confirm,reject'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['action'] === 'confirm') {
            // تأیید دستی
            $payment->update([
                'status' => PaymentStatus::SUCCESSFUL,
                'verification_status' => Payment::VERIFICATION_VERIFIED,
                'verified_at' => now(),
                'paid_at' => now(),
            ]);

            // به‌روزرسانی Order/Booking
            $this->paymentService->verifyFromServer($payment);

            return ApiResponse::success($payment->fresh(), 'پرداخت به صورت دستی تأیید شد.')
                ->toResponse($request);
        }

        // رد پرداخت
        $payment->update([
            'status' => PaymentStatus::FAILED,
            'verification_status' => Payment::VERIFICATION_FAILED,
        ]);

        return ApiResponse::success($payment->fresh(), 'پرداخت رد شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/payments/{payment}/retry-verification
     * تلاش مجدد برای تأیید (از سمت سرور)
     */
    public function retryVerification(Payment $payment): JsonResponse
    {
        if ($payment->isVerified()) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'این پرداخت قبلاً تأیید شده است.',
                httpStatus: 422,
            );
        }

        $result = $this->paymentService->verifyFromServer($payment);

        return ApiResponse::success([
            'payment' => $payment->fresh(),
            'result' => $result,
        ], $result['message'] ?? 'تلاش مجدد انجام شد.')->toResponse(request());
    }

    // =====================================================================
    // LEGACY ENDPOINTS (برای سازگاری با Booking قدیمی)
    // =====================================================================

    /**
     * POST /api/v1/bookings/{bookingId}/pay
     * شروع پرداخت برای یک رزرو قدیمی (backward compatibility)
     */
    public function pay(Request $request, int $bookingId): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $booking = \App\Modules\Booking\Models\Booking::findOrFail($bookingId);

        if ($booking->user_id !== auth()->id() && !auth()->user()->hasPermission('bookings.change_status')) {
            throw ApiException::forbidden();
        }

        $result = $this->paymentService->initiatePayment($booking, 'zarinpal');

        if (!($result['success'] ?? false)) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: $result['message'] ?? 'خطا در شروع پرداخت',
                httpStatus: 422,
            );
        }

        return ApiResponse::success($result, $result['message'] ?? 'در حال انتقال به درگاه...')
            ->toResponse($request);
    }

    // =====================================================================
    // PRIVATE HELPERS
    // =====================================================================

    /**
     * دریافت URL ریدایرکت موفقیت
     */
    private function getSuccessRedirectUrl(?Payment $payment): string
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');

        if (!$payment) {
            return $frontendUrl . '/payment/success';
        }

        if ($payment->order) {
            return $frontendUrl . '/orders/' . $payment->order->code . '/payment-success';
        }

        if ($payment->booking) {
            return $frontendUrl . '/bookings/' . $payment->booking->booking_code . '/payment-success';
        }

        return $frontendUrl . '/payment/success?code=' . $payment->code;
    }
}
