<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Response Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the response cache settings.
    |
    */

    'enabled' => env('RESPONSE_CACHE_ENABLED', true),

    'cache_profile' => \Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests::class,

    'cache_time_seconds' => env('RESPONSE_CACHE_TIME', 60 * 60 * 24), // 24 hours

    'add_cache_time_header' => env('APP_DEBUG', false),

    'cache_time_header_name' => env('RESPONSE_CACHE_TIME_HEADER_NAME', 'X-Cache-Time'),

    'cache_tag' => env('RESPONSE_CACHE_TAG', 'responsecache'),

    'replacers' => [
        \Spatie\ResponseCache\Replacers\CsrfTokenReplacer::class,
    ],

    'hasher' => \Spatie\ResponseCache\Hasher\DefaultHasher::class,

    'serializer' => \Spatie\ResponseCache\Serializers\DefaultSerializer::class,
];
