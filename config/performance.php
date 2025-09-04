<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk mengoptimalkan performa aplikasi Laravel
    |
    */

    'database' => [
        'query_log' => env('DB_QUERY_LOG', false),
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000), // milliseconds
        'connection_timeout' => env('DB_CONNECTION_TIMEOUT', 60),
        'query_timeout' => env('DB_QUERY_TIMEOUT', 120),
    ],

    'cache' => [
        'enable_query_cache' => env('CACHE_QUERIES', true),
        'cache_ttl' => env('CACHE_TTL', 3600), // 1 hour
    ],

    'memory' => [
        'limit' => env('MEMORY_LIMIT', '1024M'),
        'gc_threshold' => env('GC_THRESHOLD', 1000),
    ],

    'timeout' => [
        'max_execution_time' => env('MAX_EXECUTION_TIME', 300),
        'max_input_time' => env('MAX_INPUT_TIME', 300),
    ],

    'optimization' => [
        'disable_query_log' => env('DISABLE_QUERY_LOG', true),
        'enable_opcache' => env('ENABLE_OPCACHE', true),
        'batch_size' => env('BATCH_SIZE', 100),
    ],
];
