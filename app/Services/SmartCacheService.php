<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SmartCacheService
{
    /**
     * Smart cache management to reduce memory usage
     */
    
    /**
     * Get cached data with intelligent refresh
     */
    public static function get($key, $callback = null, $ttl = 3600)
    {
        // Check if cache exists and is not expired
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        
        // If callback provided, execute and cache result
        if ($callback && is_callable($callback)) {
            $data = $callback();
            
            // Only cache if data is not too large (< 1MB)
            $dataSize = strlen(serialize($data));
            if ($dataSize < 1048576) { // 1MB
                Cache::put($key, $data, $ttl);
            } else {
                Log::warning('Data too large to cache', [
                    'key' => $key,
                    'size' => $dataSize . ' bytes'
                ]);
            }
            
            return $data;
        }
        
        return null;
    }
    
    /**
     * Cache employee data with smart refresh
     */
    public static function getEmployees($forceRefresh = false)
    {
        $cacheKey = 'employees_data_smart';
        
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }
        
        return self::get($cacheKey, function() {
            return \App\Models\Employee::select('id', 'nama', 'jabatan', 'gaji_pokok')
                ->orderBy('nama')
                ->get();
        }, 1800); // 30 minutes
    }
    
    /**
     * Cache gudang data with smart refresh
     */
    public static function getGudangs($forceRefresh = false)
    {
        $cacheKey = 'gudangs_data_smart';
        
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }
        
        return self::get($cacheKey, function() {
            return \App\Models\Gudang::select('id', 'nama', 'gaji')
                ->orderBy('nama')
                ->get();
        }, 1800); // 30 minutes
    }
    
    /**
     * Cache mandor data with smart refresh
     */
    public static function getMandors($forceRefresh = false)
    {
        $cacheKey = 'mandors_data_smart';
        
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }
        
        return self::get($cacheKey, function() {
            return \App\Models\Mandor::select('id', 'nama', 'gaji')
                ->orderBy('nama')
                ->get();
        }, 1800); // 30 minutes
    }
    
    /**
     * Cache pembibitan data with smart refresh
     */
    public static function getPembibitans($forceRefresh = false)
    {
        $cacheKey = 'pembibitans_data_smart';
        
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }
        
        return self::get($cacheKey, function() {
            return \App\Models\Pembibitan::with(['kandang', 'lokasi'])
                ->select('id', 'judul', 'kandang_id', 'lokasi_id')
                ->orderBy('judul')
                ->get();
        }, 1800); // 30 minutes
    }
    
    /**
     * Smart cache clearing - only clear what's necessary
     */
    public static function smartClear($type = null)
    {
        $cleared = [];
        
        switch ($type) {
            case 'employees':
                Cache::forget('employees_data_smart');
                Cache::forget('employees_data');
                $cleared[] = 'employees';
                break;
                
            case 'gudangs':
                Cache::forget('gudangs_data_smart');
                Cache::forget('gudangs_data');
                $cleared[] = 'gudangs';
                break;
                
            case 'mandors':
                Cache::forget('mandors_data_smart');
                Cache::forget('mandors_data');
                $cleared[] = 'mandors';
                break;
                
            case 'pembibitans':
                Cache::forget('pembibitans_data_smart');
                Cache::forget('pembibitans_data');
                $cleared[] = 'pembibitans';
                break;
                
            default:
                // Clear all smart caches
                Cache::forget('employees_data_smart');
                Cache::forget('gudangs_data_smart');
                Cache::forget('mandors_data_smart');
                Cache::forget('pembibitans_data_smart');
                $cleared = ['employees', 'gudangs', 'mandors', 'pembibitans'];
        }
        
        Log::info('Smart cache cleared', ['types' => $cleared]);
        
        return $cleared;
    }
    
    /**
     * Get cache statistics
     */
    public static function getStats()
    {
        $stats = [
            'employees_cached' => Cache::has('employees_data_smart'),
            'gudangs_cached' => Cache::has('gudangs_data_smart'),
            'mandors_cached' => Cache::has('mandors_data_smart'),
            'pembibitans_cached' => Cache::has('pembibitans_data_smart'),
            'memory_usage' => memory_get_usage(true) / 1024 / 1024, // MB
            'peak_memory' => memory_get_peak_usage(true) / 1024 / 1024, // MB
        ];
        
        return $stats;
    }
}
