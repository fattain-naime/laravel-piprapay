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
    */
    'base_url' => 'https://pay.yourdomain.com', // For self-hosted production
    'sandbox_base_url' => 'https://demo.piprapay.com',

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
