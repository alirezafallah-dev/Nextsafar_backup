<?php
/**
 * NextSafar Sync Settings Page
 * 
 * Allows admin to configure Laravel webhook URL and secret.
 */

namespace NextSafar\Sync;

if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    add_submenu_page(
        'options-general.php',
        'NextSafar Sync',
        'NextSafar Sync',
        'manage_options',
        'nextsafar-sync',
        __NAMESPACE__ . '\\render_settings_page'
    );
});

add_action('admin_init', function () {
    register_setting('nextsafar_sync_settings', 'nextsafar_laravel_url', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => 'http://api.nextsafar.local',
    ]);
    
    register_setting('nextsafar_sync_settings', 'nextsafar_webhook_secret', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
});

function render_settings_page() {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1>⚙️ تنظیمات همگام‌سازی NextSafar</h1>
        
        <form method="post" action="options.php" dir="rtl">
            <?php settings_fields('nextsafar_sync_settings'); ?>
            
            <table class="form-table">
                <tr>
                    <th><label for="nextsafar_laravel_url">آدرس Laravel API</label></th>
                    <td>
                        <input type="url" 
                               id="nextsafar_laravel_url" 
                               name="nextsafar_laravel_url" 
                               value="<?php echo esc_attr(get_option('nextsafar_laravel_url')); ?>" 
                               class="regular-text" 
                               placeholder="http://api.nextsafar.local" />
                        <p class="description">آدرس پایه بک‌اند لاراول (بدون اسلش انتهایی)</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="nextsafar_webhook_secret">Webhook Secret</label></th>
                    <td>
                        <input type="text" 
                               id="nextsafar_webhook_secret" 
                               name="nextsafar_webhook_secret" 
                               value="<?php echo esc_attr(get_option('nextsafar_webhook_secret')); ?>" 
                               class="regular-text code" 
                               dir="ltr" />
                        <p class="description">رمز مشترک با لاراول (از فایل .env لاراول کپی شود)</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('ذخیره تنظیمات'); ?>
        </form>
        
        <hr>
        <h2>📊 وضعیت همگام‌سازی</h2>
        <?php
        $hotels = get_posts([
            'post_type' => 'hotel',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);
        
        $totalHotels = count($hotels);
        $syncedHotels = 0;
        $failedHotels = 0;
        
        foreach ($hotels as $hotel) {
            $lastSync = get_post_meta($hotel->ID, '_nextsafar_last_sync', true);
            if (!empty($lastSync) && !empty($lastSync['success'])) {
                $syncedHotels++;
            } elseif (!empty($lastSync)) {
                $failedHotels++;
            }
        }
        
        echo "<table class='widefat'>";
        echo "<tr><td>کل هتل‌ها</td><td><strong>{$totalHotels}</strong></td></tr>";
        echo "<tr><td>✅ همگام‌سازی موفق</td><td><strong style='color:green'>{$syncedHotels}</strong></td></tr>";
        echo "<tr><td>❌ همگام‌سازی ناموفق</td><td><strong style='color:red'>{$failedHotels}</strong></td></tr>";
        echo "<tr><td>⏸️ منتظر همگام‌سازی</td><td><strong>" . ($totalHotels - $syncedHotels - $failedHotels) . "</strong></td></tr>";
        echo "</table>";
        ?>
    </div>
    <?php
}
