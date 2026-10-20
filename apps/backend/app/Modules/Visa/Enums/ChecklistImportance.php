<?php

namespace App\Modules\Visa\Enums;

enum ChecklistImportance: string
{
    case REQUIRED = 'required';
    case RECOMMENDED = 'recommended';
    case OPTIONAL = 'optional';

    public function label(): string
    {
        return match ($this) {
            self::REQUIRED => 'الزامی',
            self::RECOMMENDED => 'توصیه شده',
            self::OPTIONAL => 'اختیاری',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::REQUIRED => 'red',
            self::RECOMMENDED => 'yellow',
            self::OPTIONAL => 'gray',
        };
    }
}
