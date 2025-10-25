<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Production Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for production environment
    |
    */

    'data_validation' => [
        'enabled' => env('DATA_VALIDATION_ENABLED', true),
        'strict_mode' => env('DATA_VALIDATION_STRICT', true),
        'cache_ttl' => env('DATA_CACHE_TTL', 3600), // 1 hour
    ],

    'real_time_sync' => [
        'enabled' => env('REAL_TIME_SYNC_ENABLED', true),
        'cache_clearing' => env('CACHE_CLEARING_ENABLED', true),
        'event_handling' => env('EVENT_HANDLING_ENABLED', true),
    ],

    'logging' => [
        'level' => env('LOG_LEVEL', 'info'),
        'data_changes' => env('LOG_DATA_CHANGES', true),
        'performance' => env('LOG_PERFORMANCE', true),
    ],

    'monitoring' => [
        'data_integrity_check' => env('MONITOR_DATA_INTEGRITY', true),
        'check_interval' => env('INTEGRITY_CHECK_INTERVAL', 3600), // 1 hour
        'auto_fix' => env('AUTO_FIX_ENABLED', false),
    ],
];
