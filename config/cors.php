<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration determines what cross-origin operations may execute
    | in web browsers. For Sanctum SPA auth we must allow credentials and
    | explicitly allow the frontend origin(s).
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Comma-separated list, e.g. "http://localhost:5173,https://app.aman.com"
    'allowed_origins' => array_values(array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173'))))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Required for Sanctum SPA cookies
    'supports_credentials' => true,

];

