<?php

namespace App\Modules\Payment\Enums;

/**
 * ۸ وضعیت پرداخت طبق بخش ۱۴.۲ سند
 */
enum PaymentStatus: string
{
    case INITIATED = 'initiated';                 // شروع شده
    case PENDING_GATEWAY = 'pending_gateway';     // در انتظار پاسخ درگاه
    case SUCCESSFUL = 'successful';               // موفق
    case FAILED = 'failed';                       // ناموفق
    case EXPIRED = 'expired';                     // منقضی شده
    case REFUNDED = 'refunded';                   // مسترد شده
    case REQUIRES_RECONCILIATION = 'requires_reconciliation'; // نیازمند تطبیق
    case CANCELLED = 'cancelled';                 // لغو شده

    // حفظ سازگاری با کد قدیمی
    case PENDING = 'pending';
    case REDIRECTING = 'redirecting';
    case SUCCESS = 'success';

    public function label(): string
    {
        return match($this) {
            self::INITIATED => 'شروع شده',
            self::PENDING_GATEWAY => 'در انتظار درگاه',
            self::SUCCESSFUL, self::SUCCESS => 'موفق',
            self::FAILED => 'ناموفق',
            self::EXPIRED => 'منقضی شده',
            self::REFUNDED => 'مسترد شده',
            self::REQUIRES_RECONCILIATION => 'نیازمند تطبیق',
            self::CANCELLED => 'لغو شده',
            self::PENDING => 'در انتظار',
            self::REDIRECTING => 'در حال انتقال',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::INITIATED => 'blue',
            self::PENDING_GATEWAY, self::PENDING => 'yellow',
            self::REDIRECTING => 'orange',
            self::SUCCESSFUL, self::SUCCESS => 'green',
            self::FAILED => 'red',
            self::EXPIRED => 'gray',
            self::REFUNDED => 'purple',
            self::REQUIRES_RECONCILIATION => 'amber',
            self::CANCELLED => 'slate',
        };
    }

    public function isSuccessful(): bool
    {
        return in_array($this, [self::SUCCESSFUL, self::SUCCESS]);
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::SUCCESSFUL, self::SUCCESS,
            self::FAILED,
            self::EXPIRED,
            self::REFUNDED,
            self::CANCELLED,
        ]);
    }

    public function canRetry(): bool
    {
        return in_array($this, [self::FAILED, self::EXPIRED, self::CANCELLED]);
    }

    /**
     * وضعیت‌های فعال (در جریان)
     */
    public static function activeStatuses(): array
    {
        return [
            self::INITIATED,
            self::PENDING_GATEWAY,
            self::PENDING,
            self::REDIRECTING,
            self::REQUIRES_RECONCILIATION,
        ];
    }
}
