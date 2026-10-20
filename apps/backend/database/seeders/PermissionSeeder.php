<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->getPermissionsList();

        foreach ($permissions as $group => $perms) {
            foreach ($perms as $slug => $name) {
                Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'group' => $group,
                        'is_sensitive' => $this->isSensitive($slug),
                    ]
                );
            }
        }

        $this->command?->info('✅ ' . count($permissions, COUNT_RECURSIVE) . " permissions created across " . count($permissions) . " groups.");
    }

    private function getPermissionsList(): array
    {
        return [
            // === Users ===
            'users' => [
                'users.view' => 'مشاهده کاربران',
                'users.create' => 'ایجاد کاربر',
                'users.edit' => 'ویرایش کاربر',
                'users.delete' => 'حذف کاربر',
                'users.assign_role' => 'اختصاص نقش به کاربر',
                'users.view_passengers' => 'مشاهده مسافران',
                'users.view_documents' => 'مشاهده مدارک کاربران',
            ],

            // === Roles ===
            'roles' => [
                'roles.view' => 'مشاهده نقش‌ها',
                'roles.create' => 'ایجاد نقش',
                'roles.edit' => 'ویرایش نقش',
                'roles.delete' => 'حذف نقش',
                'roles.assign_permissions' => 'اختصاص دسترسی‌ها',
            ],

            // === Hotels ===
            'hotels' => [
                'hotels.view' => 'مشاهده هتل‌ها',
                'hotels.create' => 'ایجاد هتل',
                'hotels.edit' => 'ویرایش هتل',
                'hotels.delete' => 'حذف هتل',
                'hotels.publish' => 'انتشار هتل',
                'hotels.manage_media' => 'مدیریت تصاویر هتل',
            ],

            // === Tours ===
            'tours' => [
                'tours.view' => 'مشاهده تورها',
                'tours.create' => 'ایجاد تور',
                'tours.edit' => 'ویرایش تور',
                'tours.delete' => 'حذف تور',
                'tours.publish' => 'انتشار تور',
                'tours.manage_dates' => 'مدیریت تاریخ‌های تور',
                'tours.manage_prices' => 'مدیریت قیمت‌های تور',
                'tours.manage_itinerary' => 'مدیریت برنامه سفر',
                'tours.manage_media' => 'مدیریت تصاویر تور',
                'tours.link_hotels' => 'اتصال تور به هتل',
            ],

            // === Bookings ===
            'bookings' => [
                'bookings.view' => 'مشاهده سفارش‌ها',
                'bookings.view_own' => 'مشاهده سفارش‌های خود',
                'bookings.create' => 'ایجاد سفارش',
                'bookings.edit' => 'ویرایش سفارش',
                'bookings.cancel' => 'لغو سفارش',
                'bookings.change_status' => 'تغییر وضعیت سفارش',
                'bookings.issue' => 'صدور سفارش',
            ],

            // === Payments ===
            'payments' => [
                'payments.view' => 'مشاهده پرداخت‌ها',
                'payments.view_own' => 'مشاهده پرداخت‌های خود',
                'payments.verify' => 'تأیید پرداخت',
                'payments.reconcile' => 'تطبیق پرداخت',
                'payments.export' => 'خروجی گزارش پرداخت‌ها',
            ],

            // === Refunds ===
            'refunds' => [
                'refunds.view' => 'مشاهده استردادها',
                'refunds.create' => 'ثبت درخواست استرداد',
                'refunds.approve' => 'تأیید استرداد',
                'refunds.reject' => 'رد استرداد',
                'refunds.process' => 'اجرای استرداد',
            ],

            // === Visa ===
            'visa' => [
                'visa.view' => 'مشاهده پرونده‌های ویزا',
                'visa.create' => 'ایجاد پرونده ویزا',
                'visa.edit' => 'ویرایش پرونده ویزا',
                'visa.change_status' => 'تغییر وضعیت پرونده',
                'visa.upload_documents' => 'آپلود مدارک ویزا',
                'visa.view_documents' => 'مشاهده مدارک ویزا',
                'visa.manage_appointments' => 'مدیریت وقت سفارت',
                'visa.assign_expert' => 'اختصاص کارشناس ویزا',
            ],

            // === Leads ===
            'leads' => [
                'leads.view' => 'مشاهده درخواست‌ها',
                'leads.view_own' => 'مشاهده درخواست‌های خود',
                'leads.create' => 'ثبت درخواست',
                'leads.edit' => 'ویرایش درخواست',
                'leads.assign' => 'اختصاص به کارشناس',
                'leads.change_status' => 'تغییر وضعیت درخواست',
                'leads.convert_to_booking' => 'تبدیل به سفارش',
                'leads.add_note' => 'افزودن یادداشت',
            ],

            // === Reports ===
            'reports' => [
                'reports.view_dashboard' => 'مشاهده داشبورد',
                'reports.view_sales' => 'گزارش فروش',
                'reports.view_leads' => 'گزارش درخواست‌ها',
                'reports.view_searches' => 'گزارش جستجوها',
                'reports.view_financial' => 'گزارش مالی',
                'reports.export' => 'خروجی گزارش‌ها',
            ],

            // === Settings ===
            'settings' => [
                'settings.view' => 'مشاهده تنظیمات',
                'settings.edit' => 'ویرایش تنظیمات',
                'settings.manage_gateways' => 'مدیریت درگاه‌های پرداخت',
                'settings.manage_providers' => 'مدیریت پروایدرها',
                'settings.manage_notifications' => 'مدیریت اطلاع‌رسانی‌ها',
                'settings.manage_discount_codes' => 'مدیریت کدهای تخفیف',
            ],

            // === Sync ===
            'sync' => [
                'sync.view_logs' => 'مشاهده لاگ‌های همگام‌سازی',
                'sync.trigger_manual' => 'اجرای همگام‌سازی دستی',
                'sync.retry_failed' => 'تلاش مجدد همگام‌سازی‌های ناموفق',
                'sync.view_wp_status' => 'مشاهده وضعیت وردپرس',
            ],

            // === Content ===
            'content' => [
                'content.view' => 'مشاهده محتوا',
                'content.edit_wp' => 'ویرایش در وردپرس',
                'content.manage_seo' => 'مدیریت سئو',
                'content.view_articles' => 'مشاهده مقالات',
            ],

            // === Logs ===
            'logs' => [
                'logs.view_admin' => 'مشاهده لاگ‌های ادمین',
                'logs.view_security' => 'مشاهده لاگ‌های امنیتی',
                'logs.view_system' => 'مشاهده لاگ‌های سیستمی',
                'logs.export' => 'خروجی لاگ‌ها',
            ],
        ];
    }

    private function isSensitive(string $slug): bool
    {
        $sensitivePatterns = [
            'users.delete',
            'users.assign_role',
            'roles.delete',
            'roles.assign_permissions',
            'payments.verify',
            'payments.reconcile',
            'refunds.approve',
            'refunds.process',
            'settings.edit',
            'settings.manage_gateways',
            'bookings.change_status',
        ];

        return in_array($slug, $sensitivePatterns);
    }
}
