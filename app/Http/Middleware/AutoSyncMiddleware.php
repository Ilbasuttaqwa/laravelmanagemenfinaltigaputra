<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class AutoSyncMiddleware
{
    /**
     * Handle an incoming request.
     * Automatically sync files and clear cache when needed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only run on salary reports page
        if ($request->is('manager/salary-reports*') || $request->is('admin/salary-reports*')) {
            $this->autoSyncSalaryReports();
        }
        
        return $next($request);
    }
    
    /**
     * Auto-sync salary reports logic
     */
    private function autoSyncSalaryReports()
    {
        try {
            // Check if we need to update the controller logic
            $controllerPath = app_path('Http/Controllers/SalaryReportController.php');
            
            if (file_exists($controllerPath)) {
                $content = file_get_contents($controllerPath);
                
                // Check if the file has the latest hasFilter logic
                if (strpos($content, 'hasFilter') === false) {
                    $this->updateControllerLogic($controllerPath);
                }
            }
            
            // Auto-clear cache if needed
            $this->autoClearCache();
            
        } catch (\Exception $e) {
            // Log error but don't break the application
            \Log::warning('AutoSync error: ' . $e->getMessage());
        }
    }
    
    /**
     * Update controller logic if needed
     */
    private function updateControllerLogic($controllerPath)
    {
        try {
            // Read current content
            $content = file_get_contents($controllerPath);
            
            // Check if we need to add hasFilter logic
            if (strpos($content, 'hasFilter') === false) {
                // Add hasFilter logic to the index method
                $newLogic = '
        // Cek apakah ada filter yang dipilih
        $hasFilter = $lokasiId || $kandangId || $pembibitanId || $tanggalMulai || $tanggalSelesai || ($tipe !== \'all\');
        
        if (!$hasFilter) {
            // Jika tidak ada filter, tampilkan tabel kosong
            $reports = collect();
        } else {';
                
                // Find the right place to insert the logic
                $pattern = '/public function index\(Request \$request\)\s*\{[^}]*\$tanggalSelesai = \$request->get\(\'tanggal_selesai\'\);/';
                
                if (preg_match($pattern, $content, $matches)) {
                    $replacement = $matches[0] . $newLogic;
                    $content = str_replace($matches[0], $replacement, $content);
                    
                    // Also need to close the else block
                    $content = str_replace(
                        '$reports = $query->orderBy(\'nama_karyawan\')->get();',
                        '$reports = $query->orderBy(\'nama_karyawan\')->get();' . "\n        }",
                        $content
                    );
                    
                    // Write the updated content
                    file_put_contents($controllerPath, $content);
                    
                    \Log::info('AutoSync: Updated SalaryReportController with hasFilter logic');
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('AutoSync: Failed to update controller: ' . $e->getMessage());
        }
    }
    
    /**
     * Auto-clear cache when needed
     */
    private function autoClearCache()
    {
        try {
            // Check if cache is stale (older than 1 hour)
            $cacheKey = 'salary_reports_cache_timestamp';
            $lastCacheTime = Cache::get($cacheKey, 0);
            $currentTime = time();
            
            if (($currentTime - $lastCacheTime) > 3600) { // 1 hour
                // Clear relevant caches
                if (file_exists(base_path('bootstrap/cache/config.php'))) {
                    unlink(base_path('bootstrap/cache/config.php'));
                }
                
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
                
                // Update cache timestamp
                Cache::put($cacheKey, $currentTime, 3600);
                
                \Log::info('AutoSync: Cleared stale cache');
            }
            
        } catch (\Exception $e) {
            \Log::error('AutoSync: Failed to clear cache: ' . $e->getMessage());
        }
    }
}
