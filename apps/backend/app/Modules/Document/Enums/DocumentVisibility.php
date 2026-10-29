<?php

namespace App\Modules\Document\Enums;

enum DocumentVisibility: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';
    case INTERNAL = 'internal';

    public function label(): string
    {
        return match ($this) {
            self::PUBLIC => 'عمومی',
            self::PRIVATE => 'خصوصی',
            self::INTERNAL => 'داخلی',
        };
    }
}
