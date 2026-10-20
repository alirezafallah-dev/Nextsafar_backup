<?php
/**
 * Feature Flags for NextSafar Core Plugin
 * 
 * این فایل کنترل می‌کند که کدام ویژگی‌ها فعال/غیرفعال باشند.
 * بر اساس بخش ۵.۳ سند: وردپرس فقط برای محتوا و سئو است.
 * 
 * @since 1.0.0
 * @see Section 5.3 of architecture spec
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Feature Flags Configuration
 * 
 * Set to 'false' to disable features that should be in Laravel/Node.js.
 * Set to 'true' to keep them active (only for content features).
 */
return [
    
    // ═══════════════════════════════════════════════════════
    // ❌ Authentication (به لاراول منتقل شده - بخش ۱۱ سند)
    // ═══════════════════════════════════════════════════════
    'auth.otp' => false,           // ❌ DISABLED - Laravel handles OTP
    'auth.google_oauth' => false,  // ❌ DISABLED - Laravel handles OAuth
    'auth.session' => false,       // ❌ DISABLED - Laravel handles sessions
    
    // ═══════════════════════════════════════════════════════
    // ❌ Account Management (به لاراول منتقل شده - بخش ۱۱ سند)
    // ═══════════════════════════════════════════════════════
    'account.endpoints' => false,  // ❌ DISABLED - Laravel handles user accounts
    'account.favorites' => false,  // ❌ DISABLED - Laravel handles favorites
    
    // ═══════════════════════════════════════════════════════
    // ❌ Search Engine (به Node.js منتقل می‌شود - بخش ۵.۲ و ۲۱ سند)
    // ═══════════════════════════════════════════════════════
    'search.live_search' => false,  // ❌ DISABLED - Node.js Search Service (Phase 9)
    'search.serpapi' => false,      // ❌ DISABLED - Node.js Search Service
    'search.searchapi' => false,    // ❌ DISABLED - Node.js Search Service
    
    // ═══════════════════════════════════════════════════════
    // ❌ Booking & Payment (به لاراول منتقل شده - بخش ۱۳-۱۴ سند)
    // ═══════════════════════════════════════════════════════
    'booking.price_in_wp' => false,    // ❌ DISABLED - Laravel Order module
    'booking.capacity_in_wp' => false, // ❌ DISABLED - Laravel Order module
    
    // ═══════════════════════════════════════════════════════
    // ✅ Content & SEO (در وردپرس می‌ماند - بخش ۵.۳ سند)
    // ═══════════════════════════════════════════════════════
    'content.hotels' => true,      // ✅ ACTIVE - WordPress core function (content only)
    'content.articles' => true,    // ✅ ACTIVE - WordPress core function
    'content.seo' => true,         // ✅ ACTIVE - WordPress core function
    'content.destinations' => true, // ✅ ACTIVE - WordPress core function
    'content.visa_guides' => true,  // ✅ ACTIVE - WordPress core function
    
];
