<?php

namespace App\Modules\Notification\Enums;

enum NotificationStatus: string
{
    case PENDING = 'pending';
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
    case RETRYING = 'retrying';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'در انتظار',
            self::QUEUED => 'در صف',
            self::PROCESSING => 'در حال پردازش',
            self::SENT => 'ارسال شد',
            self::DELIVERED => 'تحویل شد',
            self::FAILED => 'ناموفق',
            self::RETRYING => 'تلاش مجدد',
            self::CANCELLED => 'لغو شده',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::QUEUED => 'blue',
            self::PROCESSING => 'yellow',
            self::SENT => 'indigo',
            self::DELIVERED => 'green',
            self::FAILED => 'red',
            self::RETRYING => 'orange',
            self::CANCELLED => 'slate',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::DELIVERED, self::FAILED, self::CANCELLED]);
    }

    public function isSuccessful(): bool
    {
        return in_array($this, [self::SENT, self::DELIVERED]);
    }
}
