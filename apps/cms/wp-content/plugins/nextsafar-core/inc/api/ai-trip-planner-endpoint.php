<?php
/**
 * AI Trip Planner Endpoint - DEPRECATED STUB
 * 
 * @deprecated Will move to Node.js Search Service (Phase 9)
 * @see Section 5.2 of architecture spec
 */

namespace NextSafar\API;

if (!defined('ABSPATH')) exit;

class AITripPlannerEndpoint {
    public static function init() {
        // Stub - will be moved to Node.js
        error_log('[NextSafar] AI Trip Planner endpoint called but service is deprecated');
    }

    public static function generate_trip_plan($request) {
        return new \WP_Error(
            'service_deprecated',
            'AI Trip Planner will be moved to Node.js Search Service',
            ['status' => 503]
        );
    }
}
