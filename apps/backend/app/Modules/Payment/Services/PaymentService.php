<?php

namespace App\Modules\Payment\Services;

use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use App\Modules\Booking\Enums\BookingStatus;
use App\Modules\Booking\Models\Booking;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Enums\PaymentGateway;
use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Gateways\GatewayInterface;
use App\Modules\Payment\Gateways\ZarinpalGateway;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Events\PaymentSuccessful;
use App\Modules\Payment\Events\PaymentFailed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * سرویس اصلی پرداخت
 * 
 * قوانین پیاده‌سازی شده طبق بخش ۱۴.۳ سند:
 * 
 * ۱۴.۳.۱: پرداخت فقط وقتی موفق است که استعلام سمت سرور تأیید شود
 * ۱۴.۳.۲: به صفحه بازگشت کاربر اعتماد نشود
 * ۱۴.۳.۳: به پارامترهای سمت کلاینت اعتماد نشود
 * ۱۴.۳.۴: مبلغ پرداخت با مبلغ سفارش تطبیق داده شود
 * ۱۴.۳.۵: کالبک درگاه با امضا تأیید شود (اگر درگاه پشتیبانی کند)
 * ۱۴.۳.۶: پرداخت تکراری نباید سفارش تکراری بسازد (Idempotency)
 * ۱۴.۳.۷: هر پرداخت باید لاگ کامل داشته باشد
 */
class PaymentService
{
    private array $gateways = [];

    public function __construct()
    {
        $this->gateways[PaymentGateway::ZARINPAL->value] = new ZarinpalGateway();
        // در آینده: درگاه‌های دیگر
        // $this->gateways[PaymentGateway::IDPAY->value] = new IdpayGateway();
    }

    /**
     * دریافت درگاه پرداخت
     */
    public function getGateway(string $gatewayName): ?GatewayInterface
    {
        return $this->gateways[$gatewayName] ?? null;
    }

    // =====================================================================
    // ۱. شروع پرداخت با Idempotency (قانون ۱۴.۳.۶)
    // =====================================================================

    /**
     * شروع فرآیند پرداخت (برای Order جدید)
     */
    public function initiateForOrder(Order $order, string $gatewayName = 'zarinpal'): array
    {
        // بررسی اینکه سفارش پرداخت نشده
        if ($order->isPaid()) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'این سفارش قبلاً پرداخت شده است.',
                httpStatus: 422,
            );
        }

        // بررسی مبلغ
        if ($order->total_amount <= 0) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'مبلغ سفارش نامعتبر است.',
                httpStatus: 422,
            );
        }

        $gateway = $this->getGateway($gatewayName);
        if (!$gateway) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'درگاه پرداخت نامعتبر است.',
                httpStatus: 422,
            );
        }

        return DB::transaction(function () use ($order, $gateway, $gatewayName) {
            // قانون ۱۴.۳.۶: بررسی Idempotency (جلوگیری از پرداخت تکراری)
            $idempotencyKey = Payment::generateIdempotencyKey(
                $order->user_id,
                $order->id,
                null
            );

            $existingPayment = $this->findActivePaymentByIdempotencyKey($idempotencyKey);
            if ($existingPayment) {
                // اگر پرداخت فعال وجود دارد، همان را برگردان
                AppLogger::payment()->info('payment.idempotent_return', [
                    'payment_id' => $existingPayment->id,
                    'code' => $existingPayment->code,
                    'idempotency_key' => $idempotencyKey,
                ]);

                return $this->getPaymentUrl($existingPayment, $gateway);
            }

            // ایجاد رکورد Payment
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'gateway' => PaymentGateway::from($gatewayName),
                'amount' => $order->total_amount,
                'currency' => $order->currency,
                'status' => PaymentStatus::INITIATED,
                'verification_status' => Payment::VERIFICATION_NOT_VERIFIED,
                'idempotency_key' => $idempotencyKey,
                'ip_address' => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
                'initiated_at' => now(),
                'description' => "پرداخت سفارش {$order->code}",
            ]);

            // فراخوانی درگاه
            $metadata = [
                'order_id' => $order->id,
                'order_code' => $order->code,
                'user_id' => $order->user_id,
                'mobile' => $order->user?->phone ?? '',
                'email' => $order->user?->email ?? '',
            ];

            $result = $gateway->purchase(
                (float) $order->total_amount,
                "پرداخت سفارش {$order->code}",
                $metadata
            );

            // قانون ۱۴.۳.۷: لاگ کامل
            $payment->update([
                'gateway_request' => $result,
            ]);

            if (!$result['success']) {
                $payment->update(['status' => PaymentStatus::FAILED]);

                AppLogger::payment()->error('payment.gateway_failed', [
                    'payment_id' => $payment->id,
                    'gateway' => $gatewayName,
                    'error' => $result['error'] ?? 'unknown',
                ]);

                throw new ApiException(
                    errorCode: ApiErrorCode::PROVIDER_ERROR,
                    userMessage: $result['message'] ?? 'خطا در ارتباط با درگاه پرداخت',
                    httpStatus: 502,
                );
            }

            // ذخیره authority
            $payment->update([
                'authority_code' => $result['authority'],
                'status' => PaymentStatus::PENDING_GATEWAY,
            ]);

            AppLogger::payment()->info('payment.initiated', [
                'payment_id' => $payment->id,
                'code' => $payment->code,
                'order_id' => $order->id,
                'order_code' => $order->code,
                'amount' => $order->total_amount,
                'gateway' => $gatewayName,
                'authority' => $result['authority'],
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'payment_code' => $payment->code,
                'authority' => $result['authority'],
                'payment_url' => $result['payment_url'],
                'message' => 'در حال انتقال به درگاه پرداخت...',
            ];
        });
    }

    /**
     * شروع فرآیند پرداخت (برای Booking قدیمی - سازگاری)
     */
    public function initiatePayment(Booking $booking, string $gatewayName = 'zarinpal'): array
    {
        if ($booking->isPaid()) {
            return [
                'success' => false,
                'error' => 'already_paid',
                'message' => 'این رزرو قبلاً پرداخت شده است',
            ];
        }

        if ($booking->total_amount <= 0) {
            return [
                'success' => false,
                'error' => 'invalid_amount',
                'message' => 'مبلغ رزرو نامعتبر است',
            ];
        }

        $gateway = $this->getGateway($gatewayName);
        if (!$gateway) {
            return [
                'success' => false,
                'error' => 'invalid_gateway',
                'message' => 'درگاه پرداخت نامعتبر است',
            ];
        }

        // قانون ۱۴.۳.۶: بررسی Idempotency
        $idempotencyKey = Payment::generateIdempotencyKey(
            $booking->user_id,
            null,
            $booking->id
        );

        $existingPayment = $this->findActivePaymentByIdempotencyKey($idempotencyKey);
        if ($existingPayment) {
            return $this->getPaymentUrl($existingPayment, $gateway);
        }

        // ایجاد رکورد Payment
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'gateway' => PaymentGateway::from($gatewayName),
            'amount' => $booking->total_amount,
            'currency' => $booking->currency,
            'status' => PaymentStatus::INITIATED,
            'verification_status' => Payment::VERIFICATION_NOT_VERIFIED,
            'idempotency_key' => $idempotencyKey,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'initiated_at' => now(),
        ]);

        // فراخوانی درگاه
        $description = "پرداخت رزرو {$booking->booking_code} - {$booking->booking_type->label()}";
        $metadata = [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'user_id' => $booking->user_id,
            'mobile' => $booking->user->phone ?? '',
            'email' => $booking->user->email ?? '',
        ];

        $result = $gateway->purchase(
            (float) $booking->total_amount,
            $description,
            $metadata
        );

        $payment->update(['gateway_request' => $result]);

        if (!$result['success']) {
            $payment->update(['status' => PaymentStatus::FAILED]);
            return $result;
        }

        // ذخیره authority
        $payment->update([
            'authority_code' => $result['authority'],
            'status' => PaymentStatus::PENDING_GATEWAY,
        ]);

        return [
            'success' => true,
            'payment_id' => $payment->id,
            'payment_code' => $payment->code,
            'authority' => $result['authority'],
            'payment_url' => $result['payment_url'],
            'message' => 'در حال انتقال به درگاه پرداخت...',
        ];
    }

    // =====================================================================
    // ۲. مدیریت Callback (قوانین ۱۴.۳.۲ و ۱۴.۳.۵)
    // =====================================================================

    /**
     * مدیریت کالبک از درگاه
     * 
     * قانون ۱۴.۳.۲: به صفحه بازگشت کاربر اعتماد نشود
     * این متد فقط callback را دریافت می‌کند و وضعیت را ثبت می‌کند
     * تأیید واقعی در verifyFromServer() انجام می‌شود
     */
    public function handleCallback(Request $request, string $gatewayName = 'zarinpal'): array
    {
        $gateway = $this->getGateway($gatewayName);
        if (!$gateway) {
            return [
                'success' => false,
                'error' => 'invalid_gateway',
                'message' => 'درگاه نامعتبر',
            ];
        }

        // استخراج authority از request
        $authority = $request->input('Authority') ?? $request->input('authority');
        $status = $request->input('Status') ?? $request->input('status');

        if (empty($authority)) {
            return [
                'success' => false,
                'error' => 'missing_authority',
                'message' => 'شناسه تراکنش یافت نشد',
            ];
        }

        // یافتن payment
        $payment = Payment::where('authority_code', $authority)->first();
        if (!$payment) {
            AppLogger::payment()->warning('payment.callback_unknown_authority', [
                'authority' => $authority,
                'gateway' => $gatewayName,
            ]);

            return [
                'success' => false,
                'error' => 'payment_not_found',
                'message' => 'تراکنش یافت نشد',
            ];
        }

        // قانون ۱۴.۳.۵: بررسی امضای callback (اگر درگاه پشتیبانی کند)
        $callbackData = $request->all();
        $signature = $request->header('X-Signature') ?? $request->input('signature');

        if (!$gateway->verifyCallbackSignature($callbackData, $signature)) {
            AppLogger::security()->error('payment.invalid_callback_signature', [
                'payment_id' => $payment->id,
                'authority' => $authority,
                'ip' => $request->ip(),
            ]);

            $payment->update([
                'callback_data' => $callbackData,
                'status' => PaymentStatus::FAILED,
                'verification_status' => Payment::VERIFICATION_FAILED,
            ]);

            return [
                'success' => false,
                'error' => 'invalid_signature',
                'message' => 'امضای callback نامعتبر است',
            ];
        }

        // ذخیره callback data (قانون ۱۴.۳.۷: لاگ کامل)
        $payment->update([
            'callback_data' => $callbackData,
        ]);

        // قانون ۱۴.۳.۲: اگر درگاه گفته شکست خورده، سریع fail کن
        // اما اگر گفته موفق بوده، باید از سمت سرور تأیید شود
        if ($status === 'NOK' || $status === 'failed' || $status === 'canceled') {
            $payment->update(['status' => PaymentStatus::FAILED]);

            AppLogger::payment()->info('payment.callback_failed', [
                'payment_id' => $payment->id,
                'authority' => $authority,
                'status_from_gateway' => $status,
            ]);

            return [
                'success' => false,
                'error' => 'payment_failed',
                'message' => 'پرداخت ناموفق بود یا توسط کاربر لغو شد',
                'payment_code' => $payment->code,
            ];
        }

        // قانون ۱۴.۳.۲: اینجا به callback اعتماد نمی‌کنیم
        // فقط وضعیت را به "نیازمند تأیید" تغییر می‌دهیم
        $payment->update(['status' => PaymentStatus::REQUIRES_RECONCILIATION]);

        // حالا تأیید واقعی از سمت سرور
        return $this->verifyFromServer($payment);
    }

    // =====================================================================
    // ۳. تأیید سمت سرور (قانون ۱۴.۳.۱ - مهم‌ترین قانون)
    // =====================================================================

    /**
     * تأیید پرداخت از سمت سرور (مهم‌ترین متد!)
     * 
     * قانون ۱۴.۳.۱: پرداخت فقط وقتی موفق است که استعلام سمت سرور تأیید شود
     * قانون ۱۴.۳.۴: مبلغ پرداخت با مبلغ سفارش تطبیق داده شود
     */
    public function verifyFromServer(Payment $payment): array
    {
        // اگر قبلاً تأیید شده، دوباره تأیید نکن
        if ($payment->isVerified()) {
            return [
                'success' => true,
                'message' => 'پرداخت قبلاً تأیید شده است',
                'payment_code' => $payment->code,
                'reference_id' => $payment->reference_id,
            ];
        }

        // اگر در وضعیت پایانی نیست، نمی‌توان تأیید کرد
        if (in_array($payment->status, [PaymentStatus::REFUNDED, PaymentStatus::CANCELLED])) {
            return [
                'success' => false,
                'error' => 'payment_finalized',
                'message' => 'این پرداخت در وضعیت پایانی است',
            ];
        }

        $gateway = $this->getGateway($payment->gateway->value);
        if (!$gateway) {
            return [
                'success' => false,
                'error' => 'gateway_not_available',
                'message' => 'درگاه پرداخت در دسترس نیست',
            ];
        }

        // افزایش تعداد تلاش‌ها
        $payment->increment('verification_attempts');

        try {
            // فراخوانی verify از سمت سرور
            $result = $gateway->verify(
                $payment->authority_code,
                (float) $payment->amount
            );

            // قانون ۱۴.۳.۷: لاگ کامل پاسخ درگاه
            $payment->update([
                'gateway_response' => $result,
                'verification_log' => [
                    'attempts' => $payment->verification_attempts,
                    'last_attempt_at' => now()->toIso8601String(),
                    'result' => $result,
                ],
            ]);

            if (!$result['success']) {
                // بررسی خطای عدم تطابق مبلغ (قانون ۱۴.۳.۴)
                if (($result['error'] ?? '') === 'amount_mismatch') {
                    $payment->update([
                        'status' => PaymentStatus::REQUIRES_RECONCILIATION,
                        'verification_status' => Payment::VERIFICATION_MISMATCH,
                        'verified_amount' => $result['verified_amount'] ?? null,
                    ]);

                    AppLogger::security()->critical('payment.amount_mismatch', [
                        'payment_id' => $payment->id,
                        'code' => $payment->code,
                        'expected' => $payment->amount,
                        'verified' => $result['verified_amount'] ?? null,
                        'reference_id' => $result['reference_id'] ?? null,
                    ]);

                    return [
                        'success' => false,
                        'error' => 'amount_mismatch',
                        'message' => 'عدم تطابق مبلغ پرداختی - نیازمند بررسی دستی',
                        'payment_code' => $payment->code,
                    ];
                }

                $payment->update([
                    'status' => PaymentStatus::FAILED,
                    'verification_status' => Payment::VERIFICATION_FAILED,
                ]);

                AppLogger::payment()->warning('payment.verification_failed', [
                    'payment_id' => $payment->id,
                    'error' => $result['error'] ?? 'unknown',
                ]);

                return [
                    'success' => false,
                    'error' => 'verification_failed',
                    'message' => $result['message'] ?? 'تأیید پرداخت ناموفق بود',
                    'payment_code' => $payment->code,
                ];
            }

            // تأیید موفق!
            return DB::transaction(function () use ($payment, $result) {
                $payment->update([
                    'status' => PaymentStatus::SUCCESSFUL,
                    'verification_status' => Payment::VERIFICATION_VERIFIED,
                    'verified_amount' => $result['amount'] ?? $payment->amount,
                    'reference_id' => $result['reference_id'],
                    'card_number' => $result['card_pan'] ?? null,
                    'card_hash' => $result['card_hash'] ?? null,
                    'paid_at' => now(),
                    'verified_at' => now(),
                ]);

                // به‌روزرسانی وضعیت سفارش/رزرو
                $this->handleSuccessfulPayment($payment);

                // Dispatch event برای اطلاع‌رسانی
                event(new PaymentSuccessful($payment));

                AppLogger::payment()->info('payment.verified_successfully', [
                    'payment_id' => $payment->id,
                    'code' => $payment->code,
                    'amount' => $payment->amount,
                    'verified_amount' => $result['amount'],
                    'reference_id' => $result['reference_id'],
                ]);

                return [
                    'success' => true,
                    'message' => 'پرداخت با موفقیت تأیید شد',
                    'payment_code' => $payment->code,
                    'reference_id' => $result['reference_id'],
                    'amount' => $result['amount'],
                ];
            });

        } catch (Throwable $e) {
            AppLogger::payment()->error('payment.verification_exception', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'verification_error',
                'message' => 'خطا در تأیید پرداخت',
            ];
        }
    }

    // =====================================================================
    // ۴. مدیریت پرداخت موفق
    // =====================================================================

    /**
     * مدیریت پرداخت موفق - به‌روزرسانی وضعیت Order/Booking
     */
    private function handleSuccessfulPayment(Payment $payment): void
    {
        // اگر مربوط به Order است
        if ($payment->order_id) {
            $order = $payment->order;
            if ($order) {
                // تغییر وضعیت سفارش به paid
                $order->update([
                    'status' => Order::STATUS_PAID,
                    'paid_at' => now(),
                ]);

                // ثبت تاریخچه
                $order->statusHistory()->create([
                    'old_status' => $order->getOriginal('status'),
                    'new_status' => Order::STATUS_PAID,
                    'reason' => "پرداخت موفق - کد پیگیری: {$payment->reference_id}",
                    'changed_by' => null,
                    'changed_by_name' => 'System (Payment Gateway)',
                    'change_source' => 'payment_callback',
                    'ip_address' => request()->ip(),
                    'changed_at' => now(),
                ]);

                AppLogger::booking()->info('order.paid_via_payment', [
                    'order_id' => $order->id,
                    'order_code' => $order->code,
                    'payment_id' => $payment->id,
                    'payment_code' => $payment->code,
                ]);
            }
        }

        // اگر مربوط به Booking قدیمی است
        if ($payment->booking_id && !$payment->order_id) {
            $booking = $payment->booking;
            if ($booking) {
                $booking->update([
                    'status' => BookingStatus::CONFIRMED,
                    'payment_status' => 'paid',
                ]);
            }
        }
    }

    // =====================================================================
    // ۵. متدهای کمکی
    // =====================================================================

    /**
     * یافتن پرداخت فعال با Idempotency Key
     */
    private function findActivePaymentByIdempotencyKey(string $key): ?Payment
    {
        return Payment::where('idempotency_key', $key)
            ->whereIn('status', [
                PaymentStatus::INITIATED,
                PaymentStatus::PENDING_GATEWAY,
                PaymentStatus::PENDING,
                PaymentStatus::REDIRECTING,
            ])
            ->where('created_at', '>=', now()->subHour())
            ->first();
    }

    /**
     * دریافت URL پرداخت
     */
    private function getPaymentUrl(Payment $payment, GatewayInterface $gateway): array
    {
        // اگر قبلاً authority دارد، از همان استفاده کن
        if ($payment->authority_code) {
            return [
                'success' => true,
                'payment_id' => $payment->id,
                'payment_code' => $payment->code,
                'authority' => $payment->authority_code,
                'payment_url' => match ($gateway->getName()) {
                    'zarinpal' => 'https://www.zarinpal.com/pg/StartPay/' . $payment->authority_code,
                    default => null,
                },
                'message' => 'پرداخت فعال قبلی یافت شد',
                'idempotent' => true,
            ];
        }

        return [
            'success' => false,
            'error' => 'no_authority',
            'message' => 'شناسه تراکنش یافت نشد',
        ];
    }

    /**
     * استعلام وضعیت پرداخت (برای Cron job یا polling)
     */
    public function inquireStatus(Payment $payment): array
    {
        if ($payment->isFinal()) {
            return [
                'status' => $payment->status->value,
                'is_final' => true,
                'message' => 'پرداخت در وضعیت پایانی است',
            ];
        }

        // اگر پرداخت قدیمی شده (بیش از ۱ ساعت) آن را منقضی کن
        if ($payment->initiated_at && $payment->initiated_at->diffInHours(now()) > 1) {
            $payment->update(['status' => PaymentStatus::EXPIRED]);
            return [
                'status' => PaymentStatus::EXPIRED->value,
                'is_final' => true,
                'message' => 'پرداخت منقضی شده است',
            ];
        }

        return [
            'status' => $payment->status->value,
            'is_final' => false,
            'message' => 'پرداخت در جریان است',
        ];
    }

    /**
     * دریافت آمار پرداخت‌ها
     */
    public function getStatistics(?string $from = null, ?string $to = null): array
    {
        $query = Payment::query();

        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);

        $baseQuery = clone $query;

        return [
            'total_payments' => $query->count(),
            'successful_count' => (clone $baseQuery)
                ->whereIn('status', [PaymentStatus::SUCCESSFUL, PaymentStatus::SUCCESS])
                ->count(),
            'successful_amount' => (clone $baseQuery)
                ->whereIn('status', [PaymentStatus::SUCCESSFUL, PaymentStatus::SUCCESS])
                ->sum('amount'),
            'pending_count' => (clone $baseQuery)
                ->whereIn('status', PaymentStatus::activeStatuses())
                ->count(),
            'failed_count' => (clone $baseQuery)
                ->where('status', PaymentStatus::FAILED)
                ->count(),
            'refunded_count' => (clone $baseQuery)
                ->where('status', PaymentStatus::REFUNDED)
                ->count(),
            'requires_reconciliation' => (clone $baseQuery)
                ->where('status', PaymentStatus::REQUIRES_RECONCILIATION)
                ->count(),
            'by_gateway' => (clone $baseQuery)
                ->selectRaw('gateway, count(*) as count, sum(amount) as total_amount')
                ->groupBy('gateway')
                ->get()
                ->keyBy('gateway')
                ->toArray(),
            'by_status' => (clone $baseQuery)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'today_count' => (clone $baseQuery)
                ->whereDate('created_at', today())
                ->count(),
            'today_amount' => (clone $baseQuery)
                ->whereDate('created_at', today())
                ->whereIn('status', [PaymentStatus::SUCCESSFUL, PaymentStatus::SUCCESS])
                ->sum('amount'),
        ];
    }
}
