<?php

// config for FattainNaime/PipraPay
return [
    /*
    |--------------------------------------------------------------------------
    | PipraPay Credentials
    |--------------------------------------------------------------------------
    */
    'api_key' => env('PIPRAPAY_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Environment Mode
    |--------------------------------------------------------------------------
    */
    'sandbox_mode' => env('PIPRAPAY_SANDBOX_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | API Base URLs
    |--------------------------------------------------------------------------
    | The base URLs for the PipraPay API endpoints. The production URL
    | must be set by the user in their .env file for self-hosted setups.
    */
    'base_url' => env('PIPRAPAY_BASE_URL'), // <-- THIS LINE IS UPDATED
    'sandbox_base_url' => 'https://sandbox.piprapay.com',

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */
    'webhook_uri' => '/piprapay/webhook',

    /*
    |--------------------------------------------------------------------------
    | Default Redirect URLs
    |--------------------------------------------------------------------------
    */
    'redirect_url' => env('PIPRAPAY_REDIRECT_URL', '/payment/success'),
    'cancel_url' => env('PIPRAPAY_CANCEL_URL', '/payment/cancel'),
];
