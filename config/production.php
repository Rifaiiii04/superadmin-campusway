<?php

/**
 * Production Configuration
 * This file contains production-specific optimizations
 */

return [
    // Performance optimizations
    'performance' => [
        'enable_query_cache' => true,
        'enable_route_cache' => true,
        'enable_config_cache' => true,
        'enable_view_cache' => true,
        'enable_compiled_views' => true,
        'enable_optimized_autoloader' => true,
    ],

    // Cache configuration
    'cache' => [
        'default_ttl' => 3600, // 1 hour
        'api_ttl' => 300, // 5 minutes
        'view_ttl' => 86400, // 24 hours
        'config_ttl' => 86400, // 24 hours
    ],

    // API optimizations
    'api' => [
        'rate_limit' => [
            'enabled' => true,
            'max_attempts' => 1000,
            'decay_minutes' => 60,
        ],
        'response_compression' => true,
        'json_pretty_print' => false,
        'cors_enabled' => true,
    ],

    // Database optimizations
    'database' => [
        'query_logging' => false,
        'slow_query_threshold' => 2000, // 2 seconds
        'connection_pooling' => true,
        'read_write_splitting' => false,
    ],

    // Logging configuration
    'logging' => [
        'level' => 'error',
        'channels' => ['single', 'slack'],
        'max_files' => 5,
        'max_size' => 10240, // 10MB
    ],

    // Security settings
    'security' => [
        'force_https' => false, // Set to true if using HTTPS
        'secure_cookies' => true,
        'http_only_cookies' => true,
        'same_site_cookies' => 'lax',
        'csrf_protection' => true,
        'xss_protection' => true,
    ],

    // Monitoring and debugging
    'monitoring' => [
        'enable_performance_monitoring' => true,
        'enable_error_tracking' => true,
        'enable_query_monitoring' => false,
        'enable_memory_monitoring' => true,
    ],

    // CORS settings
    'cors' => [
        'allowed_origins' => [
            'http://103.23.198.101',
            'http://103.23.198.101:3000',
            'http://10.112.234.213:3000',
            'http://192.168.1.40:3000',
        ],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => [
            'Content-Type',
            'Authorization',
            'X-Requested-With',
            'Accept',
            'Origin',
        ],
        'max_age' => 86400,
    ],
];
