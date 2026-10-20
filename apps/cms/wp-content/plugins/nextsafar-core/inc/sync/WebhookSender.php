<?php
/**
 * Webhook Sender for NextSafar
 * 
 * ارسال رویدادهای هتل به لاراول (بخش ۷.۳ سند).
 * 
 * رویدادهای پشتیبانی شده:
 * - created: پست هتل ایجاد شد
 * - updated: پست هتل بروزرسانی شد
 * - published: پست هتل منتشر شد
 * - drafted: پست هتل به پیش‌نویس رفت
 * - trashed: پست هتل به زباله رفت
 * - deleted: پست هتل حذف شد
 * 
 * @package NextSafar\Sync
 * @since 2.5.0
 * @see Section 7.3 of architecture spec
 */

namespace NextSafar\Sync;

if (!defined('ABSPATH')) exit;

class WebhookSender
{
    private string $laravelUrl;
    private string $secret;
    private string $webhookEndpoint;

    public function __construct()
    {
        $this->laravelUrl = get_option('nextsafar_laravel_url', 'http://api.nextsafar.local');
        $this->secret = get_option('nextsafar_webhook_secret', '');
        $this->webhookEndpoint = '/api/v1/webhooks/wordpress/hotel';
    }

    /**
     * Send hotel event to Laravel
     *
     * @param string $event Event type: created, updated, deleted, trashed, published, drafted
     * @param int $postId WordPress post ID
     * @param array $data Hotel data (Section 7.4)
     * @return array Result ['success' => bool, 'message' => string]
     */
    public function sendHotelEvent(string $event, int $postId, array $data = []): array
    {
        if (empty($this->secret)) {
            $this->log('Webhook secret not configured', 'error');
            return ['success' => false, 'message' => 'Webhook secret not configured'];
        }

        $payload = array_merge([
            'event' => $event,
            'wp_post_id' => $postId,
            'content_version' => $this->generateContentVersion($postId),
            'sent_at' => current_time('c'),
        ], $data);

        $url = rtrim($this->laravelUrl, '/') . $this->webhookEndpoint;
        $jsonPayload = json_encode($payload);
        $signature = hash_hmac('sha256', $jsonPayload, $this->secret);

        $response = wp_remote_post($url, [
            'method' => 'POST',
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Webhook-Signature' => $signature,
                'X-Webhook-Source' => 'wordpress',
                'X-Webhook-Event' => 'hotel.' . $event,
            ],
            'body' => $jsonPayload,
        ]);

        if (is_wp_error($response)) {
            $this->log('Webhook request failed: ' . $response->get_error_message(), 'error', [
                'event' => $event,
                'post_id' => $postId,
            ]);
            return ['success' => false, 'message' => $response->get_error_message()];
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        $this->log("Webhook sent: {$event} for post {$postId}", 'info', [
            'status_code' => $statusCode,
            'response' => $body,
        ]);

        if ($statusCode >= 200 && $statusCode < 300) {
            return ['success' => true, 'message' => 'Webhook sent successfully', 'response' => $body];
        }

        return [
            'success' => false, 
            'message' => "Laravel returned HTTP {$statusCode}",
            'status_code' => $statusCode,
            'response' => $body,
        ];
    }

    /**
     * Extract hotel data from WordPress post (Section 7.4)
     */
    public function extractHotelData(\WP_Post $post): array
    {
        // SEO fields (Yoast or similar)
        $seoTitle = '';
        $seoDescription = '';
        if (class_exists('WPSEO_Meta')) {
            $seoTitle = \WPSEO_Meta::get_value('title', $post->ID);
            $seoDescription = \WPSEO_Meta::get_value('metadesc', $post->ID);
        }

        // Featured image (Section 7.4 item 9)
        $featuredImageUrl = '';
        if (has_post_thumbnail($post->ID)) {
            $featuredImageUrl = get_the_post_thumbnail_url($post->ID, 'full');
        }

        // Gallery images (Section 7.4 item 10)
        $galleryImages = [];
        if (function_exists('get_field')) {
            $gallery = get_field('hotel_gallery', $post->ID);
            if (is_array($gallery)) {
                foreach ($gallery as $img) {
                    $galleryImages[] = is_array($img) ? ($img['url'] ?? '') : $img;
                }
            }
        }

        // Coordinates (Section 7.4 item 13)
        $latitude = get_post_meta($post->ID, '_hotel_latitude', true);
        $longitude = get_post_meta($post->ID, '_hotel_longitude', true);

        // Facilities (Section 7.4 item 14)
        $facilities = get_post_meta($post->ID, '_hotel_amenities', true);
        if (is_string($facilities)) {
            $facilities = json_decode($facilities, true) ?: [];
        }

        // FAQ (Section 7.4 item 22)
        $faq = get_post_meta($post->ID, '_hotel_faq', true);
        if (is_string($faq)) {
            $faq = json_decode($faq, true) ?: [];
        }

        // Tags and Categories (Section 5.3 items 8-9)
        $tags = array_map(fn($t) => $t->name, wp_get_post_tags($post->ID));
        $categories = array_map(fn($c) => $c->name, wp_get_post_categories($post->ID, ['fields' => 'all']));

        return [
            'slug' => $post->post_name,                          // Section 7.4 item 2
            'name' => $post->post_title,                         // Section 7.4 item 3
            'publish_status' => $this->mapPostStatus($post->post_status), // Section 7.4 item 4
            'city' => get_post_meta($post->ID, '_hotel_city', true) ?: null, // Section 7.4 item 11
            'country' => get_post_meta($post->ID, '_hotel_country', true) ?: null, // Section 7.4 item 12
            'address' => get_post_meta($post->ID, '_hotel_address', true) ?: null,
            'short_description' => $post->post_excerpt ?: null, // Section 7.4 item 7
            'full_description' => $post->post_content ?: null,  // Section 7.4 item 8
            'faq' => !empty($faq) ? $faq : null,                 // Section 7.4 item 22
            'tags' => !empty($tags) ? $tags : null,              // Section 5.3 item 9
            'categories' => !empty($categories) ? $categories : null, // Section 5.3 item 8
            'featured_image_url' => $featuredImageUrl ?: null,   // Section 7.4 item 9
            'gallery_images' => !empty($galleryImages) ? $galleryImages : null, // Section 7.4 item 10
            'latitude' => $latitude ? (float) $latitude : null,  // Section 7.4 item 13
            'longitude' => $longitude ? (float) $longitude : null, // Section 7.4 item 13
            'facilities' => !empty($facilities) ? $facilities : null, // Section 7.4 item 14
            'rating' => ($r = get_post_meta($post->ID, '_hotel_rating', true)) ? (float) $r : null, // Section 7.4 item 15
            'review_count' => ($rc = get_post_meta($post->ID, '_hotel_review_count', true)) ? (int) $rc : null,
            'seo_title' => $seoTitle ?: null,                    // Section 7.4 item 17
            'seo_description' => $seoDescription ?: null,        // Section 7.4 item 18
            'seo_keyword' => get_post_meta($post->ID, '_meta_keyword', true) ?: null, // Section 7.4 item 19
            'seo_image_url' => null,                             // Section 7.4 item 20
            'canonical_url' => null,                             // Section 7.4 item 21
            'metadata' => [],
            'published_at' => $post->post_status === 'publish' ? $post->post_date_gmt : null, // Section 7.4 item 5
            'updated_at' => $post->post_modified_gmt,            // Section 7.4 item 6
        ];
    }

    /**
     * Map WordPress post status to our status
     */
    private function mapPostStatus(string $wpStatus): string
    {
        return match ($wpStatus) {
            'publish' => 'published',
            'draft' => 'draft',
            'private' => 'private',
            'trash' => 'trash',
            default => 'draft',
        };
    }

    /**
     * Generate content version (used to avoid duplicate syncs - Section 7.6 item 7)
     */
    private function generateContentVersion(int $postId): string
    {
        $post = get_post($postId);
        return md5($post->post_modified_gmt . $postId);
    }

    /**
     * Log message
     */
    private function log(string $message, string $level = 'info', array $context = []): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("[NextSafar Webhook] [{$level}] {$message} " . json_encode($context));
        }
    }
}
