<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional place to find your various credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways
    |--------------------------------------------------------------------------
    */
    'zarinpal' => [
        'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
        'sandbox' => env('ZARINPAL_SANDBOX', false),
        'callback_url' => env('ZARINPAL_CALLBACK_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Gateways
    |--------------------------------------------------------------------------
    */
    'kavenegar' => [
        'api_key' => env('KAVENEGAR_API_KEY'),
        'sender' => env('KAVENEGAR_SENDER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Providers
    |--------------------------------------------------------------------------
    */
    'serpapi' => [
        'key' => env('SERPAPI_KEY'),
    ],

    'searchapi' => [
        'key' => env('SEARCHAPI_KEY'),
    ],

    'brsapi' => [
        'key' => env('BRAPI_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WordPress Integration
    |--------------------------------------------------------------------------
    */
    'wordpress' => [
        'url' => env('WORDPRESS_SITE_URL', 'http://cms.nextsafar.local'),
        'api_key' => env('WORDPRESS_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Secrets
    |--------------------------------------------------------------------------
    | Shared secrets for verifying webhook signatures from external services.
    */
    'webhooks' => [
        'wordpress_secret' => env('WORDPRESS_WEBHOOK_SECRET'),
        'gateway_secret' => env('GATEWAY_WEBHOOK_SECRET'),
        'provider_secret' => env('PROVIDER_WEBHOOK_SECRET'),
    ],

];
