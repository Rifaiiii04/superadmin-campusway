<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'super-admin/api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://103.23.198.101',
        'http://103.23.198.101:3000',
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://10.112.234.213:3000',
        'http://10.112.234.213',
        'http://192.168.1.40:3000',
        'http://192.168.1.40',
    ],

    'allowed_origins_patterns' => [
        '/^http:\/\/10\.\d{1,3}\.\d{1,3}\.\d{1,3}(:\d+)?$/',
        '/^http:\/\/192\.168\.\d{1,3}\.\d{1,3}(:\d+)?$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Content-Disposition', 'Content-Type', 'Content-Length', 'Authorization'],

    'max_age' => 3600,

    'supports_credentials' => true,

];
