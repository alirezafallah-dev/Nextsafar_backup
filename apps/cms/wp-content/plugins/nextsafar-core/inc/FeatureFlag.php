<?php
/**
 * Feature Flag Manager
 * 
 * @since 1.0.0
 */

namespace NextSafar\Core;

class FeatureFlag
{
    private static ?array $flags = null;
    
    /**
     * Load flags from config file
     */
    private static function loadFlags(): array
    {
        if (self::$flags === null) {
            $configFile = __DIR__ . '/feature-flags.php';
            self::$flags = file_exists($configFile) ? require $configFile : [];
        }
        return self::$flags;
    }
    
    /**
     * Check if a feature is enabled
     */
    public static function isEnabled(string $flag): bool
    {
        $flags = self::loadFlags();
        return $flags[$flag] ?? false;
    }
    
    /**
     * Check if a feature is disabled
     */
    public static function isDisabled(string $flag): bool
    {
        return !self::isEnabled($flag);
    }
    
    /**
     * Get all flags
     */
    public static function getAll(): array
    {
        return self::loadFlags();
    }
    
    /**
     * Log feature access (for debugging)
     */
    public static function logAccess(string $flag, bool $enabled): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $status = $enabled ? 'ENABLED' : 'DISABLED';
            error_log("[NextSafar] Feature '{$flag}' is {$status}");
        }
    }
}
