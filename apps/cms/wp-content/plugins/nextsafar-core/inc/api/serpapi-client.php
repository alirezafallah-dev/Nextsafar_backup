<?php
/**
 * SerpApi Client - DEPRECATED STUB
 * 
 * @deprecated Search functionality moved to Node.js Search Service (Phase 9)
 * @see Section 5.2 & 21 of architecture spec
 */

namespace NextSafar\API;

if (!defined('ABSPATH')) exit;

class SerpApiClient extends BaseClient {
    protected $base_url = 'https://serpapi.com/search';

    public function search_hotels($location, $options = []) {
        $this->last_error = 'Hotel search moved to Node.js Search Service';
        error_log('[NextSafar] SerpApi search_hotels called but service is deprecated');
        return [];
    }

    public function search_flights($params = []) {
        $this->last_error = 'Flight search moved to Node.js Search Service';
        error_log('[NextSafar] SerpApi search_flights called but service is deprecated');
        return [];
    }

    public function get_hotel_details($property_token) {
        $this->last_error = 'Hotel details moved to Node.js Search Service';
        return null;
    }
}
