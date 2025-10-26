<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SmartCacheService
{
    /**
     * Cache TTL berdasarkan jenis data
     */
    private static $cacheTTL = [
        'employees' => 3600,        // 1 jam
        'gudangs' => 3600,          // 1 jam
        'mandors' => 3600,          // 1 jam
        'lokasis' => 7200,          // 2 jam (jarang berubah)
        'kandangs' => 7200,         // 2 jam (jarang berubah)
        'pembibitans' => 1800,      // 30 menit (sering berubah)
        'absensis' => 300,          // 5 menit (data real-time)
        'salary_reports' => 1800,   // 30 menit
        'dropdown_data' => 1800,    // 30 menit
    ];

    /**
     * Cache keys yang perlu di-clear secara berkala
     */
    private static $periodicKeys = [
        'employees_data',
        'gudangs_data', 
        'mandors_data',
        'lokasis_data',
        'kandangs_data',
        'pembibitans_data',
        'absensis_data',
        'salary_reports_data',
        'dropdown_employees',
        'dropdown_gudangs',
        'dropdown_mandors',
        'dropdown_lokasis',
        'dropdown_kandangs',
        'dropdown_pembibitans',
    ];

    /**
     * Smart cache dengan TTL dinamis
     */
    public static function remember($key, $callback, $type = 'default', $customTTL = null)
    {
        $ttl = $customTTL ?? self::$cacheTTL[$type] ?? 3600;
        
        return Cache::remember($key, $ttl, function() use ($callback, $key) {
            Log::info("Cache miss for key: {$key}");
            return $callback();
        });
    }

    /**
     * Clear cache berdasarkan pattern
     */
    public static function clearByPattern($pattern)
    {
        try {
            // Check if Redis is available
            if (method_exists(Cache::getStore(), 'getRedis')) {
                $keys = Cache::getRedis()->keys("*{$pattern}*");
                if (!empty($keys)) {
                    Cache::getRedis()->del($keys);
                    Log::info("Cleared cache pattern: {$pattern}, count: " . count($keys));
                }
            } else {
                // Fallback untuk file cache - clear specific keys
                $keysToClear = [
                    'employees_data',
                    'gudangs_data', 
                    'mandors_data',
                    'lokasis_data',
                    'kandangs_data',
                    'pembibitans_data',
                    'absensis_data',
                    'salary_reports_data',
                ];
                
                foreach ($keysToClear as $key) {
                    if (strpos($key, $pattern) !== false || strpos($pattern, '*') !== false) {
                        Cache::forget($key);
                    }
                }
                Log::info("Cleared cache pattern (fallback): {$pattern}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to clear cache pattern: " . $e->getMessage());
        }
    }

    /**
     * Smart cleanup berdasarkan waktu dan usage
     */
    public static function smartCleanup()
    {
        $now = Carbon::now();
        $clearedCount = 0;

        // Clear cache yang sudah expired
        foreach (self::$periodicKeys as $key) {
            if (Cache::has($key)) {
                $metadata = Cache::getRedis()->hgetall("cache_metadata:{$key}");
                if (!empty($metadata['expires_at'])) {
                    $expiresAt = Carbon::createFromTimestamp($metadata['expires_at']);
                    if ($now->gt($expiresAt)) {
                        Cache::forget($key);
                        $clearedCount++;
                    }
                }
            }
        }

        // Clear cache berdasarkan memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = self::convertToBytes($memoryLimit);
        
        if ($memoryUsage > ($memoryLimitBytes * 0.8)) { // 80% memory usage
            self::emergencyCleanup();
            $clearedCount += 10; // Estimate
        }

        Log::info("Smart cleanup completed. Cleared: {$clearedCount} items");
        return $clearedCount;
    }

    /**
     * Emergency cleanup saat memory tinggi
     */
    public static function emergencyCleanup()
    {
        // Clear semua cache kecuali yang critical
        $criticalKeys = ['app_config', 'user_sessions'];
        
        foreach (self::$periodicKeys as $key) {
            if (!in_array($key, $criticalKeys)) {
                Cache::forget($key);
            }
        }

        // Clear cache patterns
        self::clearByPattern('absensis_*');
        self::clearByPattern('dropdown_*');
        self::clearByPattern('temp_*');

        Log::warning("Emergency cache cleanup executed");
    }

    /**
     * Cache statistics
     */
    public static function getCacheStats()
    {
        $stats = [
            'total_keys' => 0,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'cache_hits' => 0,
            'cache_misses' => 0,
        ];

        try {
            if (method_exists(Cache::getStore(), 'getRedis')) {
                $redis = Cache::getRedis();
                $keys = $redis->keys('*');
                $stats['total_keys'] = count($keys);
                
                $info = $redis->info();
                $stats['cache_hits'] = $info['keyspace_hits'] ?? 0;
                $stats['cache_misses'] = $info['keyspace_misses'] ?? 0;
            } else {
                // Fallback untuk file cache
                $stats['total_keys'] = 'N/A (File Cache)';
                $stats['cache_hits'] = 'N/A (File Cache)';
                $stats['cache_misses'] = 'N/A (File Cache)';
            }
        } catch (\Exception $e) {
            Log::error("Failed to get cache stats: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Convert memory limit to bytes
     */
    private static function convertToBytes($memoryLimit)
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit)-1]);
        $memoryLimit = (int) $memoryLimit;

        switch($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }

        return $memoryLimit;
    }

    /**
     * Warm up cache untuk data yang sering digunakan
     */
    public static function warmUpCache()
    {
        $warmUpData = [
            'lokasis_data' => function() { return \App\Models\Lokasi::all(); },
            'kandangs_data' => function() { return \App\Models\Kandang::all(); },
            'gudangs_data' => function() { return \App\Models\Gudang::all(); },
            'mandors_data' => function() { return \App\Models\Mandor::all(); },
        ];

        foreach ($warmUpData as $key => $callback) {
            self::remember($key, $callback, 'lokasis');
        }

        Log::info("Cache warm-up completed");
    }

    /**
     * Clear cache berdasarkan model yang diupdate
     */
    public static function clearModelCache($model)
    {
        $modelName = strtolower(class_basename($model));
        
        switch ($modelName) {
            case 'employee':
                Cache::forget('employees_data');
                Cache::forget('dropdown_employees');
                break;
            case 'gudang':
                Cache::forget('gudangs_data');
                Cache::forget('dropdown_gudangs');
                break;
            case 'mandor':
                Cache::forget('mandors_data');
                Cache::forget('dropdown_mandors');
                break;
            case 'lokasi':
                Cache::forget('lokasis_data');
                Cache::forget('dropdown_lokasis');
                break;
            case 'kandang':
                Cache::forget('kandangs_data');
                Cache::forget('dropdown_kandangs');
                break;
            case 'pembibitan':
                Cache::forget('pembibitans_data');
                Cache::forget('dropdown_pembibitans');
                break;
            case 'absensi':
                Cache::forget('absensis_data');
                Cache::forget('salary_reports_data');
                break;
        }

        Log::info("Cleared cache for model: {$modelName}");
    }
}