<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutoSyncService
{
    /**
     * Auto-sync salary reports logic
     * This method will be called automatically when needed
     */
    public static function syncSalaryReports()
    {
        try {
            // Check if we need to sync
            $lastSync = Cache::get('salary_reports_last_sync', 0);
            $currentTime = time();
            
            // Only sync if it's been more than 30 minutes
            if (($currentTime - $lastSync) < 1800) {
                return;
            }
            
            // Check if the controller has the latest logic
            $controllerPath = app_path('Http/Controllers/SalaryReportController.php');
            
            if (file_exists($controllerPath)) {
                $content = file_get_contents($controllerPath);
                
                // If hasFilter logic is missing, add it
                if (strpos($content, 'hasFilter') === false) {
                    self::addHasFilterLogic($controllerPath, $content);
                }
            }
            
            // Update sync timestamp
            Cache::put('salary_reports_last_sync', $currentTime, 3600);
            
            Log::info('AutoSync: Salary reports logic synced successfully');
            
        } catch (\Exception $e) {
            Log::error('AutoSync: Failed to sync salary reports: ' . $e->getMessage());
        }
    }
    
    /**
     * Add hasFilter logic to the controller
     */
    private static function addHasFilterLogic($controllerPath, $content)
    {
        try {
            // The hasFilter logic to add
            $hasFilterLogic = '
        // Cek apakah ada filter yang dipilih
        $hasFilter = $lokasiId || $kandangId || $pembibitanId || $tanggalMulai || $tanggalSelesai || ($tipe !== \'all\');
        
        if (!$hasFilter) {
            // Jika tidak ada filter, tampilkan tabel kosong
            $reports = collect();
        } else {';
            
            // Find the right place to insert (after tanggalSelesai line)
            $pattern = '/(\$tanggalSelesai = \$request->get\(\'tanggal_selesai\'\);)/';
            
            if (preg_match($pattern, $content)) {
                $content = preg_replace(
                    $pattern,
                    '$1' . $hasFilterLogic,
                    $content
                );
                
                // Also need to close the else block before the return statement
                $content = str_replace(
                    'return view(\'salary-reports.index\'',
                    '        }
        
        return view(\'salary-reports.index\'',
                    $content
                );
                
                // Write the updated content
                file_put_contents($controllerPath, $content);
                
                Log::info('AutoSync: Added hasFilter logic to SalaryReportController');
            }
            
        } catch (\Exception $e) {
            Log::error('AutoSync: Failed to add hasFilter logic: ' . $e->getMessage());
        }
    }
    
    /**
     * Auto-clear cache when needed
     */
    public static function clearCacheIfNeeded()
    {
        try {
            $cacheKey = 'last_cache_clear';
            $lastClear = Cache::get($cacheKey, 0);
            $currentTime = time();
            
            // Clear cache if it's been more than 2 hours
            if (($currentTime - $lastClear) > 7200) {
                self::clearAllCaches();
                Cache::put($cacheKey, $currentTime, 7200);
            }
            
        } catch (\Exception $e) {
            Log::error('AutoSync: Failed to clear cache: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear all relevant caches
     */
    private static function clearAllCaches()
    {
        try {
            // Clear config cache
            if (file_exists(base_path('bootstrap/cache/config.php'))) {
                unlink(base_path('bootstrap/cache/config.php'));
            }
            
            // Clear route cache
            if (file_exists(base_path('bootstrap/cache/routes-v7.php'))) {
                unlink(base_path('bootstrap/cache/routes-v7.php'));
            }
            
            // Clear view cache
            $viewCacheDir = storage_path('framework/views');
            if (is_dir($viewCacheDir)) {
                $files = glob($viewCacheDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
            
            Log::info('AutoSync: All caches cleared');
            
        } catch (\Exception $e) {
            Log::error('AutoSync: Failed to clear caches: ' . $e->getMessage());
        }
    }
}
