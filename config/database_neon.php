<?php

// Konfigurasi database Neon untuk production
// File ini akan digunakan untuk mengkonversi DATABASE_URL ke format Laravel

if (env('DATABASE_URL')) {
    $url = parse_url(env('DATABASE_URL'));
    
    return [
        'driver' => 'pgsql',
        'host' => $url['host'] ?? 'localhost',
        'port' => $url['port'] ?? '5432',
        'database' => ltrim($url['path'] ?? '/', '/'),
        'username' => $url['user'] ?? '',
        'password' => $url['pass'] ?? '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'prefer',
    ];
}

// Fallback ke konfigurasi default jika DATABASE_URL tidak ada
return [
    'driver' => env('DB_CONNECTION', 'mysql'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'managemen'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
];
