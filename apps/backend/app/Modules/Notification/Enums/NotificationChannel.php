<?php

namespace App\Modules\Notification\Enums;

enum NotificationChannel: string
{
    case SMS = 'sms';
    case EMAIL = 'email';
    case INTERNAL = 'internal';
    case PUSH = 'push';

    public function label(): string
    {
        return match ($this) {
            self::SMS => 'پیامک',
            self::EMAIL => 'ایمیل',
            self::INTERNAL => 'اعلان داخلی',
            self::PUSH => 'Push Notification',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SMS => 'heroicon-o-chat-bubble-left',
            self::EMAIL => 'heroicon-o-envelope',
            self::INTERNAL => 'heroicon-o-bell',
            self::PUSH => 'heroicon-o-device-phone-mobile',
        };
    }
}
