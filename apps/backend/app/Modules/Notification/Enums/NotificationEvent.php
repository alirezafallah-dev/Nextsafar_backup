<?php

namespace App\Modules\Notification\Enums;

/**
 * ۱۰ رویداد اصلی طبق بخش ۱۸.۲ سند
 */
enum NotificationEvent: string
{
    // درخواست‌ها (Lead)
    case LEAD_CREATED = 'lead_created';
    case LEAD_FOLLOW_UP = 'lead_follow_up';
    
    // پرداخت‌ها
    case PAYMENT_SUCCESS = 'payment_success';
    case PAYMENT_FAILED = 'payment_failed';
    
    // سفارشات
    case ORDER_ISSUED = 'order_issued';
    case ORDER_CANCELLED = 'order_cancelled';
    case ORDER_REFUNDED = 'order_refunded';
    
    // ویزا
    case VISA_STATUS_CHANGED = 'visa_status_changed';
    case VISA_DOCUMENT_MISSING = 'visa_document_missing';
    
    // سفر
    case TRIP_REMINDER = 'trip_reminder';

    public function label(): string
    {
        return match ($this) {
            self::LEAD_CREATED => 'ثبت درخواست',
            self::LEAD_FOLLOW_UP => 'پیگیری درخواست',
            self::PAYMENT_SUCCESS => 'پرداخت موفق',
            self::PAYMENT_FAILED => 'پرداخت ناموفق',
            self::ORDER_ISSUED => 'صدور سفارش',
            self::ORDER_CANCELLED => 'لغو سفارش',
            self::ORDER_REFUNDED => 'استرداد',
            self::VISA_STATUS_CHANGED => 'تغییر وضعیت ویزا',
            self::VISA_DOCUMENT_MISSING => 'نقص مدارک',
            self::TRIP_REMINDER => 'یادآوری سفر',
        };
    }

    /**
     * کانال‌های پیش‌فرض هر رویداد (طبق بخش ۱۸.۲ سند)
     */
    public function defaultChannels(): array
    {
        return match ($this) {
            self::LEAD_CREATED => [NotificationChannel::SMS, NotificationChannel::INTERNAL],
            self::LEAD_FOLLOW_UP => [NotificationChannel::SMS, NotificationChannel::INTERNAL],
            self::PAYMENT_SUCCESS => [NotificationChannel::SMS, NotificationChannel::EMAIL, NotificationChannel::INTERNAL],
            self::PAYMENT_FAILED => [NotificationChannel::SMS, NotificationChannel::INTERNAL],
            self::ORDER_ISSUED => [NotificationChannel::SMS, NotificationChannel::EMAIL, NotificationChannel::INTERNAL],
            self::ORDER_CANCELLED => [NotificationChannel::SMS, NotificationChannel::EMAIL],
            self::ORDER_REFUNDED => [NotificationChannel::SMS, NotificationChannel::EMAIL, NotificationChannel::INTERNAL],
            self::VISA_STATUS_CHANGED => [NotificationChannel::SMS, NotificationChannel::INTERNAL],
            self::VISA_DOCUMENT_MISSING => [NotificationChannel::SMS, NotificationChannel::EMAIL],
            self::TRIP_REMINDER => [NotificationChannel::SMS, NotificationChannel::EMAIL],
        };
    }
}
