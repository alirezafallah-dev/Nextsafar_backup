<?php
/**
 * NextSafar Core - Module Switch
 * 
 * این فایل کنترل می‌کند که کدام ماژول‌ها در وردپرس فعال باشند
 * و کدام به لاراول/Node.js منتقل شده‌اند.
 * 
 * مقدارها:
 * - 'enabled'  : در وردپرس فعال است (فقط محتوا)
 * - 'migrated' : به لاراول/Node.js منتقل شده (در وردپرس غیرفعال)
 * - 'disabled' : کلاً غیرفعال
 * 
 * @package NextSafar\Core
 * @since 3.0.0
 * @see Section 5 of architecture spec
 */

if (!defined('ABSPATH')) exit;

return [
    // ═══════════════════════════════════════════════════════
    // بخش‌هایی که در وردپرس می‌مانند (فقط محتوا و SEO - بخش ۵.۳)
    // ═══════════════════════════════════════════════════════
    'posttypes' => [
        'hotel'        => 'enabled',   // ✅ محتوا و سئو (بخش ۵.۳)
        'airport'      => 'enabled',   // ✅ محتوا
        'destination'  => 'enabled',   // ✅ محتوا
        'restaurant'   => 'enabled',   // ✅ محتوا
        'hospital'     => 'enabled',   // ✅ محتوا
        'tour'         => 'migrated',  // ❌ به لاراول منتقل شده (بخش ۹)
        'visa'         => 'enabled',   // ✅ راهنمای ویزا (محتوا)
        'travelguide'  => 'enabled',   // ✅ راهنمای سفر (محتوا)
        'travelnews'   => 'enabled',   // ✅ اخبار (محتوا)
    ],
    
    'taxonomies' => [
        'all' => 'enabled',  // ✅ همه دسته‌بندی‌ها و برچسب‌ها (بخش ۵.۳)
    ],
    
    'metaboxes' => [
        'all' => 'enabled',  // ✅ همه متاباکس‌های محتوا
    ],
    
    // ═══════════════════════════════════════════════════════
    // بخش‌هایی که به لاراول منتقل شده‌اند (منطق تجاری)
    // ═══════════════════════════════════════════════════════
    'booking' => [
        'booking_table'   => 'migrated',  // ❌ Laravel Order module (بخش ۱۳)
        'booking_service' => 'migrated',  // ❌ Laravel Order module
        'visa_booking'    => 'migrated',  // ❌ Laravel VisaCaseResource (بخش ۱۶)
    ],
    
    'payment' => [
        'payment_table'   => 'migrated',  // ❌ Laravel Payment module (بخش ۱۴)
        'zarinpal'        => 'migrated',  // ❌ Laravel Payment module
    ],
    
    'auth' => [
        'otp'             => 'migrated',  // ❌ Laravel Auth module (بخش ۱۱)
        'google'          => 'migrated',  // ❌ Laravel Auth module
        'session'         => 'migrated',  // ❌ Laravel Auth module
    ],
    
    // ═══════════════════════════════════════════════════════
    // بخش‌هایی که به Node.js منتقل خواهند شد (بخش ۵.۲ و ۲۱)
    // ═══════════════════════════════════════════════════════
    'search' => [
        'live_search'     => 'migrated',  // ❌ Node.js Search Service (Phase 9)
        'hotel_search'    => 'migrated',  // ❌ Node.js Search Service
        'flight_search'   => 'migrated',  // ❌ Node.js Search Service
        'hotel_matcher'   => 'migrated',  // ❌ Node.js Search Service
    ],
    
    'ai' => [
        'trip_planner'    => 'enabled',   // ⏸️ فعلاً فعال (در آینده به Node.js)
        'rewriter'        => 'enabled',   // ⏸️ فعلاً فعال
    ],
    
    // ═══════════════════════════════════════════════════════
    // بخش‌های فعال (محتوا و sync - بخش ۷)
    // ═══════════════════════════════════════════════════════
    'news' => [
        'sync'            => 'enabled',   // ✅ News aggregator (محتوا)
        'filter'          => 'enabled',   // ✅ News filter
    ],
    
    'sync' => [
        'hotel_sync'       => 'enabled',  // ✅ Hotel sync webhook (بخش ۷)
        'destination_sync' => 'enabled',  // ✅ Destination sync
        'restaurant_sync'  => 'enabled',  // ✅ Restaurant sync
    ],
];
