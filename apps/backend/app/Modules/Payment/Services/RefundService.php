<?php

namespace App\Modules\Payment\Services;

use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use App\Modules\Ledger\Services\LedgerService;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Events\RefundCompleted;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Models\Refund;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Refund Service
 * 
 * طبق بخش ۱۵ سند:
 * - ۱۵.۳.۱: کاربر می‌تواند درخواست استرداد دهد
 * - ۱۵.۳.۲: ادمین تأیید یا رد می‌کند
 * - ۱۵.۳.۳: جریمه محاسبه می‌شود
 * - ۱۵.۳.۴: در Ledger ثبت می‌شود
 * - ۱۵.۳.۵: اطلاع‌رسانی به کاربر
 */
class RefundService
{
    public function __construct(
        protected LedgerService $ledgerService,
    ) {}

    // =====================================================================
    // ۱. درخواست استرداد (قانون ۱۵.۳.۱)
    // =====================================================================

    /**
     * ثبت درخواست استرداد توسط کاربر
     */
    public function requestRefund(
        Payment $payment,
        string $reason,
        ?int $requestedAmount = null,
        ?string $type = Refund::TYPE_FULL,
        ?int $userId = null
    ): Refund {
        // بررسی: آیا سفارش پرداخت شده؟
        if (!$payment->isSuccessful()) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'فقط سفارش‌های پرداخت شده قابل استرداد هستند.',
                httpStatus: 422,
            );
        }

        // بررسی: آیا قبلاً استرداد فعال وجود دارد؟
        $existingRefund = Refund::where('payment_id', $payment->id)
            ->active()
            ->first();

        if ($existingRefund) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'این سفارش قبلاً درخواست استرداد فعال دارد.',
                httpStatus: 422,
                details: ['existing_refund_code' => $existingRefund->code],
            );
        }

        // محاسبه مبالغ
        $originalAmount = (int) ($payment->verified_amount ?? $payment->amount);
        $requestedAmount = $requestedAmount ?? $originalAmount;

        if ($requestedAmount > $originalAmount) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'مبلغ درخواستی نمی‌تواند بیشتر از مبلغ پرداختی باشد.',
                httpStatus: 422,
            );
        }

        return DB::transaction(function () use ($payment, $reason, $requestedAmount, $originalAmount, $type, $userId) {
            $refund = Refund::create([
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'booking_id' => $payment->booking_id,
                'user_id' => $userId ?? $payment->user_id,
                'type' => $type,
                'amount' => $requestedAmount,
                'original_amount' => $originalAmount,
                'penalty_amount' => 0, // بعد از تأیید محاسبه می‌شود
                'refund_amount' => $requestedAmount, // پیش‌فرض
                'currency' => $payment->currency,
                'reason' => $reason,
                'status' => Refund::STATUS_REGISTERED,
                'requested_at' => now(),
            ]);

            AppLogger::finance()->info('refund.requested', [
                'refund_code' => $refund->code,
                'payment_code' => $payment->code,
                'amount' => $requestedAmount,
                'user_id' => $refund->user_id,
            ]);

            // TODO: dispatch notification event
            // event(new RefundRequested($refund));

            return $refund;
        });
    }

    /**
     * درخواست استرداد برای یک سفارش (shortcut)
     */
    public function requestRefundForOrder(Order $order, string $reason, ?int $amount = null): Refund
    {
        // پیدا کردن پرداخت موفق این سفارش
        $payment = $order->payments()
            ->where('status', 'successful')
            ->latest()
            ->first();

        if (!$payment) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'هیچ پرداخت موفقی برای این سفارش یافت نشد.',
                httpStatus: 422,
            );
        }

        return $this->requestRefund($payment, $reason, $amount);
    }

    // =====================================================================
    // ۲. بررسی توسط ادمین (قانون ۱۵.۳.۲)
    // =====================================================================

    /**
     * شروع بررسی (توسط ادمین)
     */
    public function startReview(Refund $refund): Refund
    {
        $this->validateTransition($refund, Refund::STATUS_REVIEWING);

        $refund->update(['status' => Refund::STATUS_REVIEWING]);

        AppLogger::admin()->info('refund.review_started', [
            'refund_code' => $refund->code,
            'reviewed_by' => auth()->id(),
        ]);

        return $refund->fresh();
    }

    // =====================================================================
    // ۳. محاسبه جریمه (قانون ۱۵.۳.۳)
    // =====================================================================

    /**
     * محاسبه جریمه بر اساس قوانین
     * 
     * TODO: در آینده قوانین پیچیده‌تر (بر اساس نوع تور، فاصله تا تاریخ حرکت و...) اضافه شود
     * فعلاً درصد ثابت از metadata سفارش یا تنظیمات گرفته می‌شود
     */
    public function calculatePenalty(Refund $refund, ?float $penaltyPercentage = null): array
    {
        $originalAmount = $refund->original_amount;

        // اگر درصد مشخص نشده، از تنظیمات یا پیش‌فرض استفاده کن
        if ($penaltyPercentage === null) {
            // TODO: از جدول تنظیمات بخوان
            // $penaltyPercentage = Setting::get('refund_default_penalty_percentage', 0);
            $penaltyPercentage = 0; // پیش‌فرض فعلی: بدون جریمه
        }

        // محدود کردن درصد بین 0 تا 100
        $penaltyPercentage = max(0, min(100, $penaltyPercentage));

        $penaltyAmount = (int) round($originalAmount * ($penaltyPercentage / 100));
        $refundAmount = $originalAmount - $penaltyAmount;

        return [
            'original_amount' => $originalAmount,
            'penalty_percentage' => $penaltyPercentage,
            'penalty_amount' => $penaltyAmount,
            'refund_amount' => $refundAmount,
        ];
    }

    /**
     * تأیید استرداد با محاسبه جریمه
     */
    public function approveRefund(
        Refund $refund,
        ?float $penaltyPercentage = null,
        ?string $penaltyReason = null
    ): Refund {
        $this->validateTransition($refund, Refund::STATUS_APPROVED);

        return DB::transaction(function () use ($refund, $penaltyPercentage, $penaltyReason) {
            // محاسبه جریمه
            $calculation = $this->calculatePenalty($refund, $penaltyPercentage);

            $refund->update([
                'status' => Refund::STATUS_APPROVED,
                'penalty_amount' => $calculation['penalty_amount'],
                'refund_amount' => $calculation['refund_amount'],
                'penalty_reason' => $penaltyReason,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            AppLogger::finance()->info('refund.approved', [
                'refund_code' => $refund->code,
                'approved_by' => auth()->id(),
                'penalty_amount' => $calculation['penalty_amount'],
                'refund_amount' => $calculation['refund_amount'],
            ]);

            // TODO: dispatch notification event
            // event(new RefundApproved($refund));

            return $refund->fresh();
        });
    }

    /**
     * رد درخواست استرداد
     */
    public function rejectRefund(Refund $refund, string $reason): Refund
    {
        $this->validateTransition($refund, Refund::STATUS_REJECTED);

        $refund->update([
            'status' => Refund::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
        ]);

        AppLogger::finance()->warning('refund.rejected', [
            'refund_code' => $refund->code,
            'rejected_by' => auth()->id(),
            'reason' => $reason,
        ]);

        // TODO: dispatch notification event
        // event(new RefundRejected($refund, $reason));

        return $refund->fresh();
    }

    // =====================================================================
    // ۴. پردازش بازگشت وجه
    // =====================================================================

    /**
     * شروع پردازش بازگشت وجه (ارسال به درگاه)
     */
    public function processRefund(Refund $refund): Refund
    {
        $this->validateTransition($refund, Refund::STATUS_PROCESSING);

        if (!$refund->isApproved()) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'فقط استرداد‌های تأیید شده قابل پردازش هستند.',
                httpStatus: 422,
            );
        }

        return DB::transaction(function () use ($refund) {
            $refund->update([
                'status' => Refund::STATUS_PROCESSING,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // TODO: در آینده، فراخوانی API استرداد درگاه
            // $gateway = $this->getGateway($refund->payment->gateway);
            // $result = $gateway->refund($refund->payment->reference_id, $refund->refund_amount);
            // $refund->update(['gateway_ref_id' => $result['reference_id']]);

            AppLogger::finance()->info('refund.processing_started', [
                'refund_code' => $refund->code,
                'refund_amount' => $refund->refund_amount,
            ]);

            return $refund->fresh();
        });
    }

    /**
     * تکمیل بازگشت وجه (بعد از تأیید درگاه)
     */
    public function completeRefund(Refund $refund, ?string $gatewayRefId = null): Refund
    {
        $this->validateTransition($refund, Refund::STATUS_COMPLETED);

        return DB::transaction(function () use ($refund, $gatewayRefId) {
            $refund->update([
                'status' => Refund::STATUS_COMPLETED,
                'gateway_ref_id' => $gatewayRefId ?? $refund->gateway_ref_id,
            ]);

            // به‌روزرسانی وضعیت Order
            if ($refund->order) {
                $refund->order->update(['status' => Order::STATUS_REFUNDED]);
            }

            // ثبت در Ledger (قانون ۱۵.۳.۴)
            try {
                $this->ledgerService->recordRefund($refund);
            } catch (Throwable $e) {
                AppLogger::finance()->error('refund.ledger_failed', [
                    'refund_code' => $refund->code,
                    'error' => $e->getMessage(),
                ]);
            }

            AppLogger::finance()->info('refund.completed', [
                'refund_code' => $refund->code,
                'refund_amount' => $refund->refund_amount,
                'gateway_ref_id' => $gatewayRefId,
            ]);

            // Dispatch event
            event(new RefundCompleted($refund));

            return $refund->fresh();
        });
    }

    /**
     * لغو درخواست استرداد (توسط کاربر)
     */
    public function cancelRefund(Refund $refund): Refund
    {
        $this->validateTransition($refund, Refund::STATUS_CANCELLED);

        // فقط کاربر مالک یا ادمین
        if (auth()->id() !== $refund->user_id && !auth()->user()->hasPermission('refunds.cancel')) {
            throw ApiException::forbidden();
        }

        $refund->update(['status' => Refund::STATUS_CANCELLED]);

        AppLogger::finance()->info('refund.cancelled', [
            'refund_code' => $refund->code,
            'cancelled_by' => auth()->id(),
        ]);

        return $refund->fresh();
    }

    // =====================================================================
    // ۵. متدهای کمکی
    // =====================================================================

    /**
     * اعتبارسنجی انتقال وضعیت
     */
    private function validateTransition(Refund $refund, string $newStatus): void
    {
        if (!$refund->canTransitionTo($newStatus)) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: "تغییر وضعیت از '{$refund->status_label}' به وضعیت جدید مجاز نیست.",
                httpStatus: 422,
                details: [
                    'current_status' => $refund->status,
                    'requested_status' => $newStatus,
                    'allowed' => Refund::VALID_TRANSITIONS[$refund->status] ?? [],
                ],
            );
        }
    }

    /**
     * دریافت آمار استردادها
     */
    public function getStatistics(?string $from = null, ?string $to = null): array
    {
        $query = Refund::query();

        if ($from) $query->whereDate('requested_at', '>=', $from);
        if ($to) $query->whereDate('requested_at', '<=', $to);

        $baseQuery = clone $query;

        return [
            'total_refunds' => $query->count(),
            'total_amount' => (clone $baseQuery)->completed()->sum('refund_amount'),
            'total_penalty' => (clone $baseQuery)->completed()->sum('penalty_amount'),
            'pending_count' => (clone $baseQuery)->pending()->count(),
            'approved_count' => (clone $baseQuery)->where('status', Refund::STATUS_APPROVED)->count(),
            'processing_count' => (clone $baseQuery)->where('status', Refund::STATUS_PROCESSING)->count(),
            'completed_count' => (clone $baseQuery)->completed()->count(),
            'rejected_count' => (clone $baseQuery)->where('status', Refund::STATUS_REJECTED)->count(),
            'by_status' => (clone $baseQuery)
                ->selectRaw('status, count(*) as count, sum(refund_amount) as total_amount')
                ->groupBy('status')
                ->get()
                ->keyBy('status')
                ->toArray(),
            'today_count' => (clone $baseQuery)
                ->whereDate('requested_at', today())
                ->count(),
            'avg_processing_days' => (clone $baseQuery)
                ->completed()
                ->selectRaw('AVG(EXTRACT(EPOCH FROM (processed_at - requested_at)) / 86400) as avg_days')
                ->value('avg_days') ?? 0,
        ];
    }
}
