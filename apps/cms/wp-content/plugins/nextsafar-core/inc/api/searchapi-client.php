<?php
/**
 * SearchApi Client - DEPRECATED STUB
 * 
 * @deprecated Search functionality moved to Node.js Search Service (Phase 9)
 * @see Section 5.2 & 21 of architecture spec
 */

namespace NextSafar\API;

if (!defined('ABSPATH')) exit;

class SearchApiClient extends BaseClient {
    protected $base_url = 'https://www.searchapi.io/api/v1/search';

    public function search_hotels($location, $options = []) {
        $this->last_error = 'Hotel search moved to Node.js Search Service';
        error_log('[NextSafar] SearchApi search_hotels called but service is deprecated');
        return [];
    }

    public function search_flights($params = []) {
        $this->last_error = 'Flight search moved to Node.js Search Service';
        return [];
    }
}
