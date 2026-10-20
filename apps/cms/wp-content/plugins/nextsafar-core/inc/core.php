<?php
/**
 * NextSafar Core Class
 * Main system coordinator and orchestrator
 *
 * فقط بخش‌های مربوط به **محتوا** فعال هستند.
 * تور به لاراول منتقل شده (بخش ۹ سند).
 * جستجو به Node.js منتقل می‌شود (بخش ۲۱ سند).
 *
 * @version 3.0.0 - Cleanup release
 * @package NextSafar
 * @see Section 5.3 of architecture spec
 */

namespace NextSafar;

if (!defined('ABSPATH')) exit;

class Core {

    /**
     * Initialize the core plugin functionality
     * Registers all hooks, post types, taxonomies, and services
     */
    public static function init(): void {
        // Load RateLimiter first (required by other services)
        if (file_exists(__DIR__ . '/api/rate-limiter.php')) {
            require_once __DIR__ . '/api/rate-limiter.php';
        }

        // ═══════════════════════════════════════════════════
        // POST TYPES & TAXONOMIES (Section 5.3 - Content)
        // ═══════════════════════════════════════════════════
        add_action('init', [__CLASS__, 'register_post_types']);
        add_action('init', [__CLASS__, 'register_taxonomies']);
        add_action('add_meta_boxes', [__CLASS__, 'register_metaboxes']);

        // Initialize GeoTable upgrade check
        add_action('init', function () {
            if (class_exists('\\NextSafar\\Database\\GeoTable')) {
                \NextSafar\Database\GeoTable::maybe_upgrade();
            }
        });

        // Initialize featured hotel meta (content)
        if (class_exists(MetaBoxes\HotelFeaturedMeta::class)) {
            MetaBoxes\HotelFeaturedMeta::init();
        }

        // Initialize revalidation webhook (content sync)
        if (class_exists(API\RevalidationWebhook::class)) {
            API\RevalidationWebhook::init();
        }

        // ═══════════════════════════════════════════════════
        // METABOX HOOKS REGISTRATION (Content only)
        // ═══════════════════════════════════════════════════
        $metaboxClasses = [
            MetaBoxes\GeoMetaBox::class,
            MetaBoxes\HotelMetaBox::class,
            MetaBoxes\GalleryMetaBox::class,
            MetaBoxes\AirportMetaBox::class,
            MetaBoxes\AirportFacilities::class,
            MetaBoxes\AirportServices::class,
            MetaBoxes\AirportAirlines::class,
            MetaBoxes\FlagMetaBox::class,
            MetaBoxes\DestinationMetaBox::class,
            MetaBoxes\RestaurantMetaBox::class,
            MetaBoxes\RestaurantFacilities::class,
            MetaBoxes\HospitalMetaBox::class,
            MetaBoxes\VisaMetaBox::class,
            MetaBoxes\TravelGuideMetaBox::class,
        ];

        foreach ($metaboxClasses as $class) {
            if (class_exists($class) && method_exists($class, 'register_hooks')) {
                $class::register_hooks();
            }
        }

        // ═══════════════════════════════════════════════════
        // TOURISM SYSTEM (محتوا - نه تور تجاری)
        // ═══════════════════════════════════════════════════
        if (class_exists(Taxonomies\TourismMeta::class)) {
            Taxonomies\TourismMeta::init();
        }

        // ❌ REMOVED: TourCategoryMeta (tour migrated to Laravel - Section 9)
        // if (class_exists(Taxonomies\TourCategoryMeta::class)) {
        //     Taxonomies\TourCategoryMeta::init();
        // }

        // ═══════════════════════════════════════════════════
        // CRON JOBS (Content only)
        // ═══════════════════════════════════════════════════
        add_action('ns_enrich_locations_cron', [__CLASS__, 'run_enrich_locations']);

        // Initialize news sync cron hooks
        if (class_exists(API\NewsSync::class)) {
            API\NewsSync::init();
        }

        // Sync geo data when posts are published (Section 7.6)
        add_action('save_post', function ($post_id, $post) {
            $allowed_types = ['hotel', 'airport', 'destination', 'restaurant', 'hospital'];
            if (in_array($post->post_type, $allowed_types) && $post->post_status === 'publish') {
                if (class_exists('\\NextSafar\\Sync\\GeoSync')) {
                    \NextSafar\Sync\GeoSync::sync_to_custom_table($post_id);
                }
            }
        }, 20, 2);

        // ═══════════════════════════════════════════════════
        // ADMIN PANEL INITIALIZATION
        // ═══════════════════════════════════════════════════
        if (is_admin()) {
            if (class_exists(Admin\Menu::class)) Admin\Menu::init();
            if (class_exists(Admin\SyncPage::class)) Admin\SyncPage::init();
            if (class_exists(Taxonomies\TermImages::class)) Taxonomies\TermImages::init();
        }

        // ═══════════════════════════════════════════════════
        // API ENDPOINTS (Content only - Section 23.1)
        // ═══════════════════════════════════════════════════
        $apiEndpoints = [
            API\MenuEndpoint::class,
            API\StatsEndpoint::class,
            API\WorldEndpoint::class,
            API\CountryPostsEndpoint::class,
            API\FeaturedHotelsEndpoint::class,
        ];

        foreach ($apiEndpoints as $class) {
            if (class_exists($class) && method_exists($class, 'init')) {
                $class::init();
            }
        }

        // ❌ REMOVED: SearchEndpoint (moved to Node.js - Section 21)
        // if (class_exists(API\SearchEndpoint::class)) API\SearchEndpoint::init();

        // ═══════════════════════════════════════════════════
        // AI TRIP PLANNER (Content - can stay for now)
        // ═══════════════════════════════════════════════════
        if (class_exists(Database\AiTripTable::class)) Database\AiTripTable::init();
        if (class_exists(API\AiTripPlannerEndpoint::class)) API\AiTripPlannerEndpoint::init();
        if (class_exists(Admin\AiTripSettings::class)) Admin\AiTripSettings::init();
    }

    /**
     * Register all custom post types (Section 5.3 - Content)
     */
    public static function register_post_types(): void {
        $postTypes = [
            PostTypes\Hotel::class,
            PostTypes\Airport::class,
            PostTypes\Destination::class,
            PostTypes\Restaurant::class,
            PostTypes\Hospital::class,
            PostTypes\Visa::class,
            PostTypes\TravelGuide::class,
            PostTypes\TravelNews::class,
        ];

        // ❌ REMOVED: PostTypes\Tour (migrated to Laravel - Section 9)

        foreach ($postTypes as $class) {
            if (class_exists($class) && method_exists($class, 'register')) {
                $class::register();
            }
        }
    }

    /**
     * Register all custom taxonomies (Section 5.3 - Content)
     */
    public static function register_taxonomies(): void {
        $taxonomies = [
            Taxonomies\HotelCategory::class,
            Taxonomies\HotelTag::class,
            Taxonomies\AirportCategory::class,
            Taxonomies\AirportTag::class,
            Taxonomies\DestinationCategory::class,
            Taxonomies\DestinationTag::class,
            Taxonomies\RestaurantCategory::class,
            Taxonomies\RestaurantTag::class,
            Taxonomies\HospitalCategory::class,
            Taxonomies\HospitalTag::class,
            Taxonomies\VisaCategory::class,
            Taxonomies\VisaTag::class,
            Taxonomies\TravelGuideCategory::class,
            Taxonomies\TravelGuideTag::class,
            Taxonomies\TravelNewsCategory::class,
            Taxonomies\TravelNewsTag::class,
            Taxonomies\Tourism::class,
        ];

        // ❌ REMOVED: Tour-related taxonomies (migrated to Laravel - Section 9)

        foreach ($taxonomies as $class) {
            if (class_exists($class) && method_exists($class, 'register')) {
                $class::register();
            }
        }
    }

    /**
     * Register all metaboxes for custom post types (Content only)
     */
    public static function register_metaboxes(): void {
        $metaboxes = [
            MetaBoxes\HotelMetaBox::class,
            MetaBoxes\GalleryMetaBox::class,
            MetaBoxes\AirportMetaBox::class,
            MetaBoxes\AirportFacilities::class,
            MetaBoxes\AirportServices::class,
            MetaBoxes\AirportAirlines::class,
            MetaBoxes\FlagMetaBox::class,
            MetaBoxes\DestinationMetaBox::class,
            MetaBoxes\RestaurantMetaBox::class,
            MetaBoxes\RestaurantFacilities::class,
            MetaBoxes\HospitalMetaBox::class,
            MetaBoxes\VisaMetaBox::class,
            MetaBoxes\TravelGuideMetaBox::class,
            MetaBoxes\TravelNewsMetaBox::class,
        ];

        foreach ($metaboxes as $class) {
            if (class_exists($class) && method_exists($class, 'register')) {
                $class::register();
            }
        }
    }

    /**
     * Execute location enrichment cron job
     */
    public static function run_enrich_locations(): void {
        error_log('[NextSafar] Location enrichment cron started');
        if (class_exists('\\NextSafar\\Sync\\GeoSync')) {
            // GeoSync handles the enrichment
        }
    }
}
