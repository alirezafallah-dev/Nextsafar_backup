<?php

namespace NextSafar\API;

/**
 * SearchApi Client - DEPRECATED STUB
 * 
 * This functionality is being moved to Node.js Search Service.
 * This class exists only as a placeholder to prevent fatal errors.
 * 
 * @deprecated Use Node.js Search Service instead
 */
class SearchApiClient extends BaseClient {
    protected $base_url = 'https://www.searchapi.io/api/v1/search';

    public function search_hotels($location, $options = []) {
        // Stub - search moved to Node.js
        $this->last_error = 'SearchApi functionality moved to Node.js Search Service';
        return [];
    }

    public function search_flights($params = []) {
        // Stub - search moved to Node.js
        $this->last_error = 'SearchApi functionality moved to Node.js Search Service';
        return [];
    }
}
