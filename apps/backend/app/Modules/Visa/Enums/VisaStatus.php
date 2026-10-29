<?php

namespace App\Modules\Visa\Enums;

/**
 * وضعیت انتشار نوع ویزا (نه پرونده!)
 */
enum VisaStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case INACTIVE = 'inactive';
    case COMING_SOON = 'coming_soon';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'پیش‌نویس',
            self::PUBLISHED => 'منتشر شده',
            self::INACTIVE => 'غیرفعال',
            self::COMING_SOON => 'به زودی',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PUBLISHED => 'green',
            self::INACTIVE => 'red',
            self::COMING_SOON => 'blue',
        };
    }

    public function isAvailable(): bool
    {
        return $this === self::PUBLISHED;
    }
}
