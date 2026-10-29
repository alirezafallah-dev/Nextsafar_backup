<?php

namespace App\Modules\Notification\Enums;

enum NotificationPriority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'کم',
            self::NORMAL => 'عادی',
            self::HIGH => 'بالا',
            self::URGENT => 'فوری',
        };
    }

    public function queueDelay(): int
    {
        return match ($this) {
            self::LOW => 60,       // 1 دقیقه
            self::NORMAL => 0,     // فوری
            self::HIGH => 0,       // فوری
            self::URGENT => 0,     // فوری
        };
    }
}
