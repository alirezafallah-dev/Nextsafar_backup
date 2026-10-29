<?php

namespace App\Modules\Document\Enums;

enum DocumentCategory: string
{
    case GENERAL = 'general';
    case VISA = 'visa';
    case HOTEL = 'hotel';
    case TOUR = 'tour';
    case ARTICLE = 'article';
    case INVOICE = 'invoice';
    case VOUCHER = 'voucher';
    case PASSPORT = 'passport';
    case ID_CARD = 'id_card';
    case PHOTO = 'photo';
    case BANK_STATEMENT = 'bank_statement';
    case INSURANCE = 'insurance';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::GENERAL => 'عمومی',
            self::VISA => 'مدارک ویزا',
            self::HOTEL => 'تصاویر هتل',
            self::TOUR => 'تصاویر تور',
            self::ARTICLE => 'تصاویر مقاله',
            self::INVOICE => 'فاکتور',
            self::VOUCHER => 'ووچر',
            self::PASSPORT => 'پاسپورت',
            self::ID_CARD => 'کارت ملی',
            self::PHOTO => 'عکس شخصی',
            self::BANK_STATEMENT => 'پرینت حساب',
            self::INSURANCE => 'بیمه',
            self::OTHER => 'سایر',
        };
    }

    public function isVisaRelated(): bool
    {
        return in_array($this, [
            self::VISA, self::PASSPORT, self::ID_CARD,
            self::PHOTO, self::BANK_STATEMENT, self::INSURANCE,
        ]);
    }

    public function isPublic(): bool
    {
        return in_array($this, [
            self::HOTEL, self::TOUR, self::ARTICLE,
        ]);
    }
}
