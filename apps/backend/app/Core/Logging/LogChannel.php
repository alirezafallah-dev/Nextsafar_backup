<?php

namespace App\Core\Logging;

/**
 * Standard Log Channels based on project spec Section 28.3
 */
enum LogChannel: string
{
    case API = 'api';           // درخواست‌های فرانت و عمومی
    case ADMIN = 'admin';       // عملیات ادمین
    case PAYMENT = 'payment';   // درگاه پرداخت و تراکنش‌ها
    case PROVIDER = 'provider'; // ارتباط با پروایدرها
    case SYNC = 'sync';         // همگام‌سازی وردپرس
    case SEARCH = 'search';     // جستجوها
    case BOOKING = 'booking';   // تغییرات سفارش و رزرو
    case SECURITY = 'security'; // رویدادهای امنیتی
    case VISA = 'visa';         // پرونده‌های ویزا
    case NOTIFICATION = 'notification'; // ارسال پیامک/ایمیل
    case FINANCE = 'finance';   // تراکنش‌های لجر و مالی
}
