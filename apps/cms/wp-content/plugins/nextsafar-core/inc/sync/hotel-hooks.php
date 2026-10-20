<?php
/**
 * Hotel Webhook Hooks
 * 
 * Automatically triggers webhook when hotel posts are created/updated/deleted.
 * Part of Phase 1: WordPress → Laravel sync
 */

namespace NextSafar\Sync;

use NextSafar\Core\FeatureFlag;

if (!defined('ABSPATH')) exit;

// Check feature flag
if (!FeatureFlag::isEnabled('content.hotels')) {
    return;
}

/**
 * Helper to get sender instance
 */
function get_webhook_sender(): WebhookSender
{
    static $sender = null;
    if ($sender === null) {
        $sender = new WebhookSender();
    }
    return $sender;
}

/**
 * Check if post is a hotel post type
 */
function is_hotel_post($post): bool
{
    if (!$post) return false;
    $postType = is_object($post) ? $post->post_type : get_post_type($post);
    return $postType === 'hotel' || $postType === 'hotels';
}

/**
 * Hook: When a hotel post is saved (created or updated)
 */
add_action('save_post', function ($postId, $post, $update) {
    if (!is_hotel_post($post)) return;
    if (wp_is_post_revision($postId)) return;
    if (wp_is_post_autosave($postId)) return;
    
    // جلوگیری از loop
    if (defined('DOING_NEXTSAFAR_SYNC') && DOING_NEXTSAFAR_SYNC) return;
    
    $sender = get_webhook_sender();
    $data = $sender->extractHotelData($post);
    
    $event = $update ? 'updated' : 'created';
    
    // اگر وضعیت پیش‌نویس است، رویداد drafted
    if ($post->post_status === 'draft') {
        $event = 'drafted';
    }
    // اگر منتشر شد
    elseif ($post->post_status === 'publish') {
        $event = $update ? 'updated' : 'created';
    }
    
    $result = $sender->sendHotelEvent($event, $postId, $data);
    
    // Save result to post meta for debugging
    update_post_meta($postId, '_nextsafar_last_sync', [
        'event' => $event,
        'time' => current_time('mysql'),
        'success' => $result['success'],
        'message' => $result['message'] ?? '',
    ]);
    
}, 10, 3);

/**
 * Hook: When post status changes (publish, draft, trash)
 */
add_action('transition_post_status', function ($new_status, $old_status, $post) {
    if (!is_hotel_post($post)) return;
    if ($new_status === $old_status) return;
    
    $sender = get_webhook_sender();
    
    $eventMap = [
        'publish' => 'published',
        'draft' => 'drafted',
        'trash' => 'trashed',
    ];
    
    $event = $eventMap[$new_status] ?? null;
    if (!$event) return;
    
    $data = $sender->extractHotelData($post);
    $sender->sendHotelEvent($event, $post->ID, $data);
    
}, 10, 3);

/**
 * Hook: When a hotel post is deleted permanently
 */
add_action('before_delete_post', function ($postId) {
    $post = get_post($postId);
    if (!is_hotel_post($post)) return;
    
    $sender = get_webhook_sender();
    $sender->sendHotelEvent('deleted', $postId, [
        'slug' => $post->post_name,
        'name' => $post->post_title,
    ]);
});

/**
 * Admin notice: show sync status
 */
add_action('admin_notices', function () {
    global $post;
    if (!$post || !is_hotel_post($post)) return;
    
    $lastSync = get_post_meta($post->ID, '_nextsafar_last_sync', true);
    if (empty($lastSync)) return;
    
    $class = $lastSync['success'] ? 'notice-success' : 'notice-error';
    $message = $lastSync['success'] 
        ? "✅ آخرین همگام‌سازی با لاراول: {$lastSync['time']} ({$lastSync['event']})"
        : "❌ خطا در همگام‌سازی: {$lastSync['message']}";
    
    echo "<div class='notice {$class} is-dismissible'><p><strong>NextSafar:</strong> {$message}</p></div>";
});
