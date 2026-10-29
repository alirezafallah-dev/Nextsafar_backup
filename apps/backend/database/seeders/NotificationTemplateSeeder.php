<?php

namespace Database\Seeders;

use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

/**
 * Notification Template Seeder
 * 
 * طبق بخش ۱۸.۲ سند، ۱۰ رویداد اصلی با قالب‌های SMS و Email و Internal
 * 
 * متغیرهای قابل استفاده:
 * - {{user.name}} - نام کاربر
 * - {{user.phone}} - شماره کاربر
 * - {{user.email}} - ایمیل کاربر
 * - {{order.code}} - کد سفارش
 * - {{order.amount}} - مبلغ سفارش
 * - {{payment.reference}} - شماره پیگیری پرداخت
 * - {{visa.country}} - کشور ویزا
 * - {{visa.status}} - وضعیت ویزا
 * - {{lead.type}} - نوع درخواست
 * - {{lead.code}} - کد درخواست
 */
class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ============================================================
            // ۱. ثبت درخواست (Lead Created)
            // ============================================================
            [
                'code' => 'lead_created_sms',
                'name' => 'ثبت درخواست - پیامک',
                'event_type' => NotificationEvent::LEAD_CREATED->value,
                'channel' => NotificationChannel::SMS,
                'subject' => null,
                'content' => "{{user.name}} عزیز، درخواست {{lead.type}} شما با کد {{lead.code}} ثبت شد. کارشناسان ما به زودی با شما تماس می‌گیرند.",
                'variables' => ['user.name', 'lead.type', 'lead.code'],
                'description' => 'پیامک تأیید ثبت درخواست',
            ],
            [
                'code' => 'lead_created_internal',
                'name' => 'ثبت درخواست - داخلی',
                'event_type' => NotificationEvent::LEAD_CREATED->value,
                'channel' => NotificationChannel::INTERNAL,
                'subject' => 'درخواست جدید ثبت شد',
                'content' => "درخواست {{lead.type}} شما با کد {{lead.code}} ثبت شد. کارشناسان ما در اسرع وقت با شما تماس خواهند گرفت.",
                'variables' => ['lead.type', 'lead.code'],
                'description' => 'اعلان داخلی ثبت درخواست',
            ],

            // ============================================================
            // ۲. پیگیری درخواست (Lead Follow-up)
            // ============================================================
            [
                'code' => 'lead_follow_up_sms',
                'name' => 'پیگیری درخواست - پیامک',
                'event_type' => NotificationEvent::LEAD_FOLLOW_UP->value,
                'channel' => NotificationChannel::SMS,
                'subject' => null,
                'content' => "{{user.name}} عزیز، وضعیت درخواست {{lead.code}} شما تغییر کرد. برای جزئیات به پنل کاربری مراجعه کنید.",
                'variables' => ['user.name', 'lead.code'],
                'description' => 'پیامک اطلاع‌رسانی تغییر وضعیت درخواست',
            ],
            [
                'code' => 'lead_follow_up_internal',
                'name' => 'پیگیری درخواست - داخلی',
                'event_type' => NotificationEvent::LEAD_FOLLOW_UP->value,
                'channel' => NotificationChannel::INTERNAL,
                'subject' => 'وضعیت درخواست تغییر کرد',
                'content' => "وضعیت درخواست {{lead.code}} شما تغییر کرد. برای مشاهده جزئیات روی این اعلان کلیک کنید.",
                'variables' => ['lead.code'],
                'description' => 'اعلان داخلی تغییر وضعیت درخواست',
            ],

            // ============================================================
            // ۳. پرداخت موفق (Payment Success)
            // ============================================================
            [
                'code' => 'payment_success_sms',
                'name' => 'پرداخت موفق - پیامک',
                'event_type' => NotificationEvent::PAYMENT_SUCCESS->value,
                'channel' => NotificationChannel::SMS,
                'subject' => null,
                'content' => "{{user.name}} عزیز، پرداخت شما به مبلغ {{order.amount}} ریال برای سفارش {{order.code}} با موفقیت انجام شد. شماره پیگیری: {{payment.reference}}",
                'variables' => ['user.name', 'order.amount', 'order.code', 'payment.reference'],
                'description' => 'پیامک تأیید پرداخت موفق',
            ],
            [
                'code' => 'payment_success_email',
                'name' => 'پرداخت موفق - ایمیل',
                'event_type' => NotificationEvent::PAYMENT_SUCCESS->value,
                'channel' => NotificationChannel::EMAIL,
                'subject' => 'پرداخت موفق - سفارش {{order.code}}',
                'content' => "سلام {{user.name}}،\n\nپرداخت شما برای سفارش {{order.code}} با موفقیت انجام شد.\n\nمبلغ: {{order.amount}} ریال\nشماره پیگیری: {{payment.reference}}\n\nاز خرید شما سپاسگزاریم.\nتیم NextSafar",
                'variables' => ['user.name', 'order.code', 'order.amount', 'payment.reference'],
                'description' => 'ایمیل تأیید پرداخت موفق',
            ],
            [
                'code' => 'payment_success_internal',
                'name' => 'پرداخت موفق - داخلی',
                'event_type' => NotificationEvent::PAYMENT_SUCCESS->value,
                'channel' => NotificationChannel::INTERNAL,
                'subject' => 'پرداخت موفق',
                'content' => "پرداخت شما برای سفارش {{order.code}} به مبلغ {{order.amount}} ریال با موفقیت انجام شد.",
                'variables' => ['order.code', 'order.amount'],
                'description' => 'اعلان داخلی پرداخت موفق',
            ],

            // ============================================================
            // ۴. پرداخت ناموفق (Payment Failed)
            // ============================================================
            [
                'code' => 'payment_failed_sms',
                'name' => 'پرداخت ناموفق - پیامک',
                'event_type' => NotificationEvent::PAYMENT_FAILED->value,
                'channel' => NotificationChannel::SMS,
                'subject' => null,
                'content' => "{{user.name}} عزیز، پرداخت سفارش {{order.code}} ناموفق بود. برای تلاش مجدد به پنل کاربری مراجعه کنید.",
                'variables' => ['user.name', 'order.code'],
                'description' => 'پیامک اطلاع‌رسانی پرداخت ناموفق',
            ],
            [
                'code' => 'payment_failed_internal',
                'name' => 'پرداخت ناموفق - داخلی',
                'event_type' => NotificationEvent::PAYMENT_FAILED->value,
                'channel' => NotificationChannel::INTERNAL,
                'subject' => 'پرداخت ناموفق',
                'content' => "پرداخت سفارش {{order.code}} ناموفق بود. لطفاً مجدداً تلاش کنید یا با پشتیبانی تماس بگیرید.",
                'variables' => ['order.code'],
                'description' => 'اعلان داخلی پرداخت ناموفق',
            ],

            // ============================================================
            // ۵. صدور سفارش (Order Issued)
            // ============================================================
            [
                'code' => 'order_issued_sms',
                'name' => 'صدور سفارش - پیامک',
                'event_type' => NotificationEvent::ORDER_ISSUED->value,
                'channel' => NotificationChannel::SMS,
                'subject' => null,
                'content' => "{{user.name}} عزیز، سفارش {{order.code}} شما صادر شد. ووچر به ایمیل شما ارسال شد. سفر خوبی داشته باشید!",
                'variables' => ['user.name', 'order.code'],
                'description' => 'پیامک اطلاع‌رسانی صدور سفارش',
            ],
            [
                'code' => 'order_issued_email',
                'name' => 'صدور سفارش - ایمیل',
                'event_type' => NotificationEvent::ORDER_ISSUED->value,
                'channel' => NotificationChannel::EMAIL,
                'subject' => 'سفارش {{order.code}} صادر شد',
                'content' => "سلام {{user.name}}،\n\nسفارش {{order.code}} شما با موفقیت صادر شد.\n\nجزئیات ووچر به پیوست این ایمیل ارسال شده است.\n\nاز اعتماد شما سپاسگزاریم.\nتیم NextSafar",
                'variables' => ['user.name', 'order.code'],
                'description' => 'ایمیل اطلاع‌رسانی صدور سفارش با ووچر',
            ],
            [
                'code' => 'order_issued_internal',
                'name' => 'صدور سفارش - داخلی',
                'event_type' => NotificationEvent::ORDER_ISSUED->value,
                'channel' => NotificationChannel::INTERNAL,
                'subject' => 'سفارش شما صادر شد',
                'content' => "سفارش {{order.code}} شما با موفقیت صادر شد. برای دانلود ووچر روی این اعلان کلیک کنید.",
                'variables' => ['order.code'],
                'description' => 'اعلان داخلی صدور سفارش',
            ],

            // ============================================================
            // ۶. لغو سفارش (Order Cancelled)
            // ============================================================
            [
                'code' => 'order_cancelled_sms',
                'name' => 'لغو سفارش - پیامک',
                'event_type' => NotificationEvent::ORDER_CANCELLED->value,
                'channel' => NotificationChannel::SMS,
                'subject' => null,
                'content' => "{{user.name}} عزیز، سفارش {{order.code}} شما لغو شد. برای اطلاعات بیشتر با پشتیبانی تماس بگیرید.",
                'variables' => ['user.name', 'order.code'],
                'description' => 'پیامک اطلاع‌رسانی لغو سفارش',
            ],
            [
                'code' => 'order_cancelled_email',
                'name' => 'لغو سفارش - ایمیل',
                'event_type' => NotificationEvent::ORDER_CANCELLED->value,
                'channel' => NotificationChannel::EMAIL,
                'subject' => 'لغو سفارش {{order.code}}',
                'content' => "سلام {{user.name}}،\n\nسفارش {{order.code}} شما لغو شد.\n\nاگر سوالی دارید، با پشتیبانی تماس بگیرید.\n\nتیم NextSafar",
                'variables' => ['user.name', 'order.code'],
                'description' => 'ایمیل اطلاع‌رسانی لغو سفارش',
            ],

            // ============================================================
            // ۷. استرداد (Order Refunded)
            // ============================================================
            [
                'code' => 'order_refunded_sms',
                'name' => 'استرداد - پیامک',
                'event_type' => NotificationEvent::ORDER_REFUNDED->value,
                'channel' => NotificationChannel::SMS,
                'subject' => null,
                'content' => "{{user.name}} عزیز، مبلغ {{order.amount}} ریال برای سفارش {{order.code}} به حساب شما بازگردانده شد. زمان واریز: ۲۴ تا ۷۲ ساعت.",
                'variables' => ['user.name', 'order.amount', 'order.code'],
                'description' => 'پیامک اطلاع‌رسانی استرداد',
            ],
            [
                'code' => 'order_refunded_email',
                'name' => 'استرداد - ایمیل',
                'event_type' => NotificationEvent::ORDER_REFUNDED->value,
                'channel' => NotificationChannel::EMAIL,
                'subject' => 'استرداد وجه سفارش {{order.code}}',
                'content' => "سلام {{user.name}}،\n\nمبلغ {{order.amount}} ریال برای سفارش {{order.code}} به حساب شما بازگردانده شد.\n\nزمان واریز: ۲۴ تا ۷۲ ساعت کاری\n\nاز شکیبایی شما سپاسگزاریم.\nتیم NextSafar",
                'variables' => ['user.name', 'order.amount', 'order.code'],
                'description' => 'ایمیل اطلاع‌رسانی استرداد',
            ],
            [
                'code' => 'order_refunded_internal',
                'name' => 'استرداد - داخلی',
                'event_type' => NotificationEvent::ORDER_REFUNDED->value,
                'channel' => NotificationChannel::INTERNAL,
                'subject' => 'استرداد وجه',
                'content' => "مبلغ {{order.amount}} ریال برای سفارش {{order.code}} به حساب شما بازگردانده شد.",
                'variables' => ['order.amount', 'order.code'],
                'description' => 'اعلان داخلی استرداد',
            ],

            // ============================================================
            // ۸. تغییر وضعیت ویزا (Visa Status Changed)
            // ============================================================
            [
                'code' => 'visa_status_changed_sms',
                'name' => 'تغییر وضعیت ویزا - پیامک',
                'event_type' => NotificationEvent::VISA_STATUS_CHANGED->value,
                'channel' => NotificationChannel::SMS,
                'subject' => null,
                'content' => "{{user.name}} عزیز، وضعیت پرونده ویزا {{visa.country}} شما به «{{visa.status}}» تغییر کرد.",
                'variables' => ['user.name', 'visa.country', 'visa.status'],
                'description' => 'پیامک اطلاع‌رسانی تغییر وضعیت ویزا',
            ],
            [
                'code' => 'visa_status_changed_internal',
                'name' => 'تغییر وضعیت ویزا - داخلی',
                'event_type' => NotificationEvent::VISA_STATUS_CHANGED->value,
                'channel' => NotificationChannel::INTERNAL,
                'subject' => 'وضعیت ویزا تغییر کرد',
                'content' => "وضعیت پرونده ویزا {{visa.country}} شما به «{{visa.status}}» تغییر کرد. برای مشاهده جزئیات به پنل کاربری مراجعه کنید.",
                'variables' => ['visa.country', 'visa.status'],
                'description' => 'اعلان داخلی تغییر وضعیت ویزا',
            ],

            // ============================================================
            // ۹. نقص مدارک (Visa Document Missing)
            // ============================================================
            [
                'code' => 'visa_document_missing_sms',
                'name' => 'نقص مدارک ویزا - پیامک',
                'event_type' => NotificationEvent::VISA_DOCUMENT_MISSING->value,
                'channel' => NotificationChannel::SMS,
                'subject' => null,
                'content' => "{{user.name}} عزیز، پرونده ویزا {{visa.country}} شما نیاز به تکمیل مدارک دارد. لطفاً به پنل کاربری مراجعه کنید.",
                'variables' => ['user.name', 'visa.country'],
                'description' => 'پیامک اطلاع‌رسانی نقص مدارک ویزا',
            ],
            [
                'code' => 'visa_document_missing_email',
                'name' => 'نقص مدارک ویزا - ایمیل',
                'event_type' => NotificationEvent::VISA_DOCUMENT_MISSING->value,
                'channel' => NotificationChannel::EMAIL,
                'subject' => 'نیاز به تکمیل مدارک ویزا {{visa.country}}',
                'content' => "سلام {{user.name}}،\n\nپرونده ویزا {{visa.country}} شما نیاز به تکمیل مدارک دارد.\n\nلطفاً به پنل کاربری مراجعه کرده و مدارک درخواستی را آپلود کنید.\n\nدر صورت سوال با کارشناس ویزا تماس بگیرید.\n\nتیم NextSafar",
                'variables' => ['user.name', 'visa.country'],
                'description' => 'ایمیل اطلاع‌رسانی نقص مدارک ویزا',
            ],

            // ============================================================
            // ۱۰. یادآوری سفر (Trip Reminder)
            // ============================================================
            [
                'code' => 'trip_reminder_sms',
                'name' => 'یادآوری سفر - پیامک',
                'event_type' => NotificationEvent::TRIP_REMINDER->value,
                'channel' => NotificationChannel::SMS,
                'subject' => null,
                'content' => "{{user.name}} عزیز، سفر شما {{trip.days}} روز دیگر آغاز می‌شود. سفارش: {{order.code}}. سفر خوبی داشته باشید!",
                'variables' => ['user.name', 'trip.days', 'order.code'],
                'description' => 'پیامک یادآوری سفر',
            ],
            [
                'code' => 'trip_reminder_email',
                'name' => 'یادآوری سفر - ایمیل',
                'event_type' => NotificationEvent::TRIP_REMINDER->value,
                'channel' => NotificationChannel::EMAIL,
                'subject' => 'یادآوری سفر - {{order.code}}',
                'content' => "سلام {{user.name}}،\n\nسفر شما {{trip.days}} روز دیگر آغاز می‌شود.\n\nشماره سفارش: {{order.code}}\n\nلطفاً مدارک سفر خود را چک کنید.\n\nسفر خوبی داشته باشید!\nتیم NextSafar",
                'variables' => ['user.name', 'trip.days', 'order.code'],
                'description' => 'ایمیل یادآوری سفر',
            ],
        ];

        $count = 0;
        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                [
                    'code' => $template['code'],
                    'locale' => 'fa',
                ],
                array_merge($template, [
                    'locale' => 'fa',
                    'is_active' => true,
                    'is_system' => true,
                ])
            );
            $count++;
        }

        $this->command?->info("✅ {$count} notification templates seeded");
    }
}
