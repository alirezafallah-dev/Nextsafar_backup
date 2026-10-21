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
    | a conventional file to locate the various service credentials.
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
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Providers
    |--------------------------------------------------------------------------
    */
    'kavenegar' => [
        'api_key' => env('KAVENEGAR_API_KEY'),
        'sender' => env('KAVENEGAR_SENDER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Services
    |--------------------------------------------------------------------------
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
        'places_api_key' => env('GOOGLE_PLACES_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Trip Planner Providers
    |--------------------------------------------------------------------------
    | Provider order: gemini → groq → qwen (fallback chain)
    | Each provider needs its own API key configured in .env
    */
    'trip_planner' => [
        'provider_order' => explode(',', env('TRIP_PLANNER_PROVIDER_ORDER', 'gemini,groq,qwen')),
        
        'gemini_key' => env('TRIP_PLANNER_GEMINI_KEY'),
        'gemini_model' => env('TRIP_PLANNER_GEMINI_MODEL', 'gemini-3.7-flash'),
        
        'groq_key' => env('TRIP_PLANNER_GROQ_KEY'),
        'groq_model' => env('TRIP_PLANNER_GROQ_MODEL', 'llama-3.3-70b-versatile'),
        
        'qwen_key' => env('TRIP_PLANNER_QWEN_KEY'),
        'qwen_model' => env('TRIP_PLANNER_QWEN_MODEL', 'qwen-plus'),
    ],

];
