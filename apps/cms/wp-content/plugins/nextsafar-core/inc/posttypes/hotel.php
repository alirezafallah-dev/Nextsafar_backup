<?php
/**
 * Hotel Post Type - Content & SEO Only
 * 
 * این post type فقط برای **محتوا و سئو** است.
 * قیمت، ظرفیت و رزرو در لاراول مدیریت می‌شوند (بخش ۸ سند).
 * 
 * @package NextSafar\PostTypes
 * @since 2.5.0
 * @see Section 5.3, 7.4 & 8 of architecture spec
 */

namespace NextSafar\PostTypes;

if (!defined('ABSPATH')) exit;

class Hotel {
    const POST_TYPE = 'hotel';

    public static function register() {
        $labels = [
            'name'               => 'هتل‌ها',
            'singular_name'      => 'هتل',
            'menu_name'          => 'هتل‌ها',
            'name_admin_bar'     => 'hotel',
            'add_new'            => 'افزودن هتل جدید',
            'add_new_item'       => 'افزودن هتل جدید',
            'new_item'           => 'هتل جدید',
            'edit_item'          => 'ویرایش هتل',
            'view_item'          => 'مشاهده هتل',
            'all_items'          => 'همه هتل‌ها',
            'search_items'       => 'جستجوی هتل',
            'parent_item_colon'  => 'هتل والد:',
            'not_found'          => 'هتلی پیدا نشد.',
            'not_found_in_trash' => 'هتلی در زباله‌دان پیدا نشد.',
            'featured_image'     => 'تصویر شاخص',
            'set_featured_image' => 'تنظیم تصویر شاخص',
            'remove_featured_image'=> 'حذف تصویر شاخص',
            'archives'           => 'آرشیو هتل‌ها',
            'item_list'          => 'لیست هتل‌ها',
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true, // For Gutenberg & REST API
            'query_var'          => true,
            'rewrite'            => ['slug' => 'hotel', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-building',
            'supports'           => [
                'title',        // عنوان هتل (Section 7.4 item 3)
                'editor',       // محتوای کامل (Section 7.4 item 8)
                'author',       // نویسنده (Section 5.3 item 10)
                'thumbnail',    // تصویر شاخص (Section 7.4 item 9)
                'excerpt',      // خلاصه (Section 7.4 item 7)
                'comments',     // نظرات
                'revisions',    // تاریخچه تغییرات
                'custom-fields',// فیلدهای سفارشی
            ],
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    public static function register_hooks() {
        add_action('init', [__CLASS__, 'register']);
        
        /* Add custom columns to admin list */
        add_filter('manage_hotel_posts_columns', [__CLASS__, 'add_custom_columns']);
        add_action('manage_hotel_posts_custom_column', [__CLASS__, 'render_custom_columns'], 10, 2);
        
        /* Add sync status indicator */
        add_action('post_submitbox_misc_actions', [__CLASS__, 'show_sync_status']);
    }

    /**
     * Add custom columns to hotel list
     */
    public static function add_custom_columns($columns) {
        $new_columns = [];
        
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            
            if ($key === 'title') {
                $new_columns['city'] = 'شهر';
                $new_columns['country'] = 'کشور';
                $new_columns['rating'] = 'امتیاز';
                $new_columns['sync_status'] = 'وضعیت Sync';
            }
        }
        
        return $new_columns;
    }

    /**
     * Render custom columns
     */
    public static function render_custom_columns($column, $post_id) {
        switch ($column) {
            case 'city':
                $city = get_post_meta($post_id, '_hotel_city', true);
                echo $city ? esc_html($city) : '—';
                break;
                
            case 'country':
                $country = get_post_meta($post_id, '_hotel_country', true);
                echo $country ? esc_html($country) : '—';
                break;
                
            case 'rating':
                $rating = get_post_meta($post_id, '_hotel_rating', true);
                echo $rating ? '⭐ ' . esc_html($rating) : '—';
                break;
                
            case 'sync_status':
                $lastSync = get_post_meta($post_id, '_nextsafar_last_sync', true);
                if ($lastSync && is_array($lastSync)) {
                    $status = $lastSync['success'] ? '✅' : '❌';
                    $time = $lastSync['time'] ?? '';
                    echo $status . ' ' . esc_html($time);
                } else {
                    echo '⏳ منتظر sync';
                }
                break;
        }
    }

    /**
     * Show sync status in post editor
     */
    public static function show_sync_status($post) {
        if ($post->post_type !== self::POST_TYPE) return;
        
        $lastSync = get_post_meta($post->ID, '_nextsafar_last_sync', true);
        
        if ($lastSync && is_array($lastSync)) {
            $status = $lastSync['success'] ? '✅ موفق' : '❌ ناموفق';
            $time = $lastSync['time'] ?? 'نامشخص';
            $event = $lastSync['event'] ?? 'نامشخص';
            
            echo '<div class="misc-pub-section misc-pub-sync-status">';
            echo '<strong>وضعیت Sync:</strong> ' . $status . '<br>';
            echo '<strong>زمان:</strong> ' . esc_html($time) . '<br>';
            echo '<strong>رویداد:</strong> ' . esc_html($event);
            echo '</div>';
        } else {
            echo '<div class="misc-pub-section misc-pub-sync-status">';
            echo '<strong>وضعیت Sync:</strong> ⏳ هنوز sync نشده';
            echo '</div>';
        }
    }
}
