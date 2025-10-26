<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutoSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'auto:sync {--force : Force sync even if not needed}';

    /**
     * The console command description.
     */
    protected $description = 'Automatically sync salary reports logic and clear cache when needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-sync process...');
        
        try {
            // Check if force sync is requested
            $force = $this->option('force');
            
            if (!$force) {
                // Check if sync is needed
                $lastSync = Cache::get('auto_sync_last_run', 0);
                $currentTime = time();
                
                // Only sync if it's been more than 1 hour
                if (($currentTime - $lastSync) < 3600) {
                    $this->info('Auto-sync not needed (last run: ' . date('Y-m-d H:i:s', $lastSync) . ')');
                    return;
                }
            }
            
            // 1. Check and update SalaryReportController if needed
            $this->checkAndUpdateController();
            
            // 2. Clear cache if needed
            $this->clearCacheIfNeeded();
            
            // 3. Update sync timestamp
            Cache::put('auto_sync_last_run', time(), 3600);
            
            $this->info('Auto-sync completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('Auto-sync failed: ' . $e->getMessage());
            Log::error('AutoSync Command failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Check and update SalaryReportController if needed
     */
    private function checkAndUpdateController()
    {
        $this->info('Checking SalaryReportController...');
        
        $controllerPath = app_path('Http/Controllers/SalaryReportController.php');
        
        if (!file_exists($controllerPath)) {
            $this->warn('SalaryReportController not found!');
            return;
        }
        
        $content = file_get_contents($controllerPath);
        
        // Check if hasFilter logic exists
        if (strpos($content, 'hasFilter') === false) {
            $this->warn('hasFilter logic not found, updating controller...');
            $this->updateController($controllerPath, $content);
        } else {
            $this->info('✓ hasFilter logic already exists');
        }
    }
    
    /**
     * Update controller with hasFilter logic
     */
    private function updateController($controllerPath, $content)
    {
        try {
            // Backup the original file
            $backupPath = $controllerPath . '.backup.' . date('Y-m-d_H-i-s');
            copy($controllerPath, $backupPath);
            $this->info('✓ Backup created: ' . basename($backupPath));
            
            // Add hasFilter logic
            $hasFilterLogic = '
        // Cek apakah ada filter yang dipilih
        $hasFilter = $lokasiId || $kandangId || $pembibitanId || $tanggalMulai || $tanggalSelesai || ($tipe !== \'all\');
        
        if (!$hasFilter) {
            // Jika tidak ada filter, tampilkan tabel kosong
            $reports = collect();
        } else {';
            
            // Find the right place to insert
            $pattern = '/(\$tanggalSelesai = \$request->get\(\'tanggal_selesai\'\);)/';
            
            if (preg_match($pattern, $content)) {
                $content = preg_replace(
                    $pattern,
                    '$1' . $hasFilterLogic,
                    $content
                );
                
                // Close the else block
                $content = str_replace(
                    'return view(\'salary-reports.index\'',
                    '        }
        
        return view(\'salary-reports.index\'',
                    $content
                );
                
                // Write the updated content
                file_put_contents($controllerPath, $content);
                
                $this->info('✓ SalaryReportController updated with hasFilter logic');
            } else {
                $this->warn('Could not find insertion point for hasFilter logic');
            }
            
        } catch (\Exception $e) {
            $this->error('Failed to update controller: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear cache if needed
     */
    private function clearCacheIfNeeded()
    {
        $this->info('Checking cache...');
        
        $cacheKey = 'last_cache_clear';
        $lastClear = Cache::get($cacheKey, 0);
        $currentTime = time();
        
        // Clear cache if it's been more than 2 hours
        if (($currentTime - $lastClear) > 7200) {
            $this->info('Clearing cache...');
            $this->clearAllCaches();
            Cache::put($cacheKey, $currentTime, 7200);
            $this->info('✓ Cache cleared');
        } else {
            $this->info('✓ Cache is fresh (last clear: ' . date('Y-m-d H:i:s', $lastClear) . ')');
        }
    }
    
    /**
     * Clear all relevant caches
     */
    private function clearAllCaches()
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
                $count = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                        $count++;
                    }
                }
                $this->info("  - Cleared {$count} view cache files");
            }
            
            // Clear application cache
            $appCacheDir = storage_path('framework/cache/data');
            if (is_dir($appCacheDir)) {
                $files = glob($appCacheDir . '/*');
                $count = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                        $count++;
                    }
                }
                $this->info("  - Cleared {$count} application cache files");
            }
            
        } catch (\Exception $e) {
            $this->error('Failed to clear cache: ' . $e->getMessage());
        }
    }
}
