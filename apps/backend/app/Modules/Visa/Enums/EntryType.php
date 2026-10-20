<?php

namespace App\Modules\Visa\Enums;

enum EntryType: string
{
    case SINGLE = 'single';
    case DOUBLE = 'double';
    case MULTIPLE = 'multiple';

    public function label(): string
    {
        return match ($this) {
            self::SINGLE => 'یک‌بار ورود',
            self::DOUBLE => 'دو بار ورود',
            self::MULTIPLE => 'چندبار ورود',
        };
    }
}
