<?php

namespace App\Modules\Visa\Enums;

enum VisaStatusForIranians: string
{
    case REQUIRED = 'required';
    case VISA_ON_ARRIVAL = 'visa_on_arrival';
    case EVISA = 'evisa';
    case VISA_FREE = 'visa_free';
    case RESTRICTED = 'restricted';

    public function label(): string
    {
        return match ($this) {
            self::REQUIRED => 'ویزا لازم',
            self::VISA_ON_ARRIVAL => 'ویزا در فرودگاه',
            self::EVISA => 'ویزای الکترونیکی',
            self::VISA_FREE => 'بدون ویزا',
            self::RESTRICTED => 'محدود',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::REQUIRED => 'red',
            self::VISA_ON_ARRIVAL => 'yellow',
            self::EVISA => 'blue',
            self::VISA_FREE => 'green',
            self::RESTRICTED => 'gray',
        };
    }
}
