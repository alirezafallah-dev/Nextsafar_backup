<?php

namespace App\Modules\Document\Enums;

enum ScanStatus: string
{
    case PENDING = 'pending';
    case CLEAN = 'clean';
    case INFECTED = 'infected';
    case INVALID = 'invalid';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'در انتظار بررسی',
            self::CLEAN => 'سالم',
            self::INFECTED => 'آلوده',
            self::INVALID => 'نامعتبر',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::CLEAN => 'green',
            self::INFECTED => 'red',
            self::INVALID => 'gray',
        };
    }
}
