<?php
/**
 * Feature Flags for NextSafar Core Plugin
 * 
 * This file controls which features are enabled/disabled.
 * Based on architecture spec: WordPress should only handle content and SEO.
 * 
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Feature Flags Configuration
 * 
 * Set to 'false' to disable features that should be in Laravel.
 * Set to 'true' to keep them active (temporary, during migration).
 */
return [
    
    // === Authentication (Should be in Laravel) ===
    'auth.otp' => false,           // ❌ DISABLED - Laravel handles OTP
    'auth.google_oauth' => false,  // ❌ DISABLED - Laravel handles OAuth
    'auth.session' => false,       // ❌ DISABLED - Laravel handles sessions
    
    // === Account Management (Should be in Laravel) ===
    'account.endpoints' => false,  // ❌ DISABLED - Laravel handles user accounts
    'account.favorites' => false,  // ❌ DISABLED - Laravel handles favorites
    
    // === Search Engine (Should be in Node.js Service) ===
    'search.live_search' => true,  // ⏸️ ACTIVE - Waiting for Node.js service
    'search.serpapi' => true,      // ⏸️ ACTIVE - Waiting for Node.js service
    'search.searchapi' => true,    // ⏸️ ACTIVE - Waiting for Node.js service
    
    // === Booking & Payment (Should be in Laravel) ===
    'booking.price_in_wp' => true, // ⏸️ ACTIVE - Waiting for Laravel Booking Module
    'booking.capacity_in_wp' => true, // ⏸️ ACTIVE - Waiting for Laravel Booking Module
    
    // === Content & SEO (Should stay in WordPress) ===
    'content.hotels' => true,      // ✅ ACTIVE - WordPress core function
    'content.articles' => true,    // ✅ ACTIVE - WordPress core function
    'content.seo' => true,         // ✅ ACTIVE - WordPress core function
    
];
