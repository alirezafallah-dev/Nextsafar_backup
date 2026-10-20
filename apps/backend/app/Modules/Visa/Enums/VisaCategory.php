<?php

namespace App\Modules\Visa\Enums;

enum VisaCategory: string
{
    case TOURIST = 'tourist';
    case BUSINESS = 'business';
    case STUDENT = 'student';
    case WORK = 'work';
    case MEDICAL = 'medical';
    case TRANSIT = 'transit';
    case FAMILY = 'family';
    case INVESTMENT = 'investment';
    case DIGITAL_NOMAD = 'digital_nomad';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::TOURIST => 'توریستی',
            self::BUSINESS => 'تجاری',
            self::STUDENT => 'تحصیلی',
            self::WORK => 'کاری',
            self::MEDICAL => 'درمانی',
            self::TRANSIT => 'ترانزیت',
            self::FAMILY => 'خانوادگی',
            self::INVESTMENT => 'سرمایه‌گذاری',
            self::DIGITAL_NOMAD => 'دیجیتال نومد',
            self::OTHER => 'سایر',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TOURIST => 'heroicon-o-globe-alt',
            self::BUSINESS => 'heroicon-o-briefcase',
            self::STUDENT => 'heroicon-o-academic-cap',
            self::WORK => 'heroicon-o-building-office',
            self::MEDICAL => 'heroicon-o-heart',
            self::TRANSIT => 'heroicon-o-paper-airplane',
            self::FAMILY => 'heroicon-o-users',
            self::INVESTMENT => 'heroicon-o-banknotes',
            self::DIGITAL_NOMAD => 'heroicon-o-computer-desktop',
            self::OTHER => 'heroicon-o-document-text',
        };
    }
}
