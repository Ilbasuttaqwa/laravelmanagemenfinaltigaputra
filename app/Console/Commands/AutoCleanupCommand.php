<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'auto:cleanup {--force : Force cleanup even if not needed} {--days=30 : Number of days to keep data}';

    /**
     * The console command description.
     */
    protected $description = 'Automatically cleanup old data (absensi, logs, cache) and sync salary reports logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-cleanup process...');
        
        try {
            $force = $this->option('force');
            $days = (int) $this->option('days');
            
            if (!$force) {
                // Check if cleanup is needed
                $lastCleanup = Cache::get('auto_cleanup_last_run', 0);
                $currentTime = time();
                
                // Only cleanup if it's been more than 24 hours
                if (($currentTime - $lastCleanup) < 86400) {
                    $this->info('Auto-cleanup not needed (last run: ' . date('Y-m-d H:i:s', $lastCleanup) . ')');
                    return;
                }
            }
            
            // 1. Sync Salary Reports Logic
            $this->syncSalaryReportsLogic();
            
            // 2. Cleanup old absensi data
            $this->cleanupOldAbsensi($days);
            
            // 3. Cleanup old logs
            $this->cleanupOldLogs($days);
            
            // 4. Cleanup old cache
            $this->cleanupOldCache();
            
            // 5. Cleanup old salary reports (optional)
            $this->cleanupOldSalaryReports($days);
            
            // 6. Update cleanup timestamp
            Cache::put('auto_cleanup_last_run', time(), 86400);
            
            $this->info('Auto-cleanup completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('Auto-cleanup failed: ' . $e->getMessage());
            Log::error('Auto-cleanup Command failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Sync salary reports logic
     */
    private function syncSalaryReportsLogic()
    {
        $this->info('Syncing salary reports logic...');
        
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
     * Cleanup old absensi data
     */
    private function cleanupOldAbsensi($days)
    {
        $this->info("Cleaning up absensi data older than {$days} days...");
        
        try {
            $cutoffDate = Carbon::now()->subDays($days);
            
            // Count records to be deleted
            $count = DB::table('absensis')
                ->where('created_at', '<', $cutoffDate)
                ->count();
            
            if ($count > 0) {
                // Delete old absensi records
                $deleted = DB::table('absensis')
                    ->where('created_at', '<', $cutoffDate)
                    ->delete();
                
                $this->info("✓ Deleted {$deleted} old absensi records");
                
                // Log the cleanup
                Log::info("Auto-cleanup: Deleted {$deleted} absensi records older than {$days} days");
            } else {
                $this->info('✓ No old absensi records to clean up');
            }
            
        } catch (\Exception $e) {
            $this->error('Failed to cleanup absensi data: ' . $e->getMessage());
        }
    }
    
    /**
     * Cleanup old logs
     */
    private function cleanupOldLogs($days)
    {
        $this->info("Cleaning up log files older than {$days} days...");
        
        try {
            $logPath = storage_path('logs');
            $cutoffTime = time() - ($days * 24 * 60 * 60);
            $deletedCount = 0;
            
            if (is_dir($logPath)) {
                $files = glob($logPath . '/*.log');
                
                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < $cutoffTime) {
                        // Don't delete the current log file
                        if (basename($file) !== 'laravel.log') {
                            unlink($file);
                            $deletedCount++;
                        }
                    }
                }
            }
            
            if ($deletedCount > 0) {
                $this->info("✓ Deleted {$deletedCount} old log files");
                Log::info("Auto-cleanup: Deleted {$deletedCount} log files older than {$days} days");
            } else {
                $this->info('✓ No old log files to clean up');
            }
            
        } catch (\Exception $e) {
            $this->error('Failed to cleanup logs: ' . $e->getMessage());
        }
    }
    
    /**
     * Cleanup old cache
     */
    private function cleanupOldCache()
    {
        $this->info('Cleaning up old cache...');
        
        try {
            $deletedCount = 0;
            
            // Clear view cache
            $viewCacheDir = storage_path('framework/views');
            if (is_dir($viewCacheDir)) {
                $files = glob($viewCacheDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                        $deletedCount++;
                    }
                }
            }
            
            // Clear application cache
            $appCacheDir = storage_path('framework/cache/data');
            if (is_dir($appCacheDir)) {
                $files = glob($appCacheDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                        $deletedCount++;
                    }
                }
            }
            
            // Clear session files older than 1 day
            $sessionDir = storage_path('framework/sessions');
            if (is_dir($sessionDir)) {
                $cutoffTime = time() - (24 * 60 * 60); // 1 day
                $files = glob($sessionDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < $cutoffTime) {
                        unlink($file);
                        $deletedCount++;
                    }
                }
            }
            
            if ($deletedCount > 0) {
                $this->info("✓ Cleared {$deletedCount} cache files");
                Log::info("Auto-cleanup: Cleared {$deletedCount} cache files");
            } else {
                $this->info('✓ No cache files to clean up');
            }
            
        } catch (\Exception $e) {
            $this->error('Failed to cleanup cache: ' . $e->getMessage());
        }
    }
    
    /**
     * Cleanup old salary reports (optional)
     */
    private function cleanupOldSalaryReports($days)
    {
        $this->info("Cleaning up salary reports older than {$days} days...");
        
        try {
            $cutoffDate = Carbon::now()->subDays($days);
            
            // Count records to be deleted
            $count = DB::table('salary_reports')
                ->where('created_at', '<', $cutoffDate)
                ->count();
            
            if ($count > 0) {
                // Ask for confirmation if more than 100 records
                if ($count > 100 && !$this->option('force')) {
                    if (!$this->confirm("Found {$count} old salary reports. Delete them?")) {
                        $this->info('Skipping salary reports cleanup');
                        return;
                    }
                }
                
                // Delete old salary reports
                $deleted = DB::table('salary_reports')
                    ->where('created_at', '<', $cutoffDate)
                    ->delete();
                
                $this->info("✓ Deleted {$deleted} old salary reports");
                
                // Log the cleanup
                Log::info("Auto-cleanup: Deleted {$deleted} salary reports older than {$days} days");
            } else {
                $this->info('✓ No old salary reports to clean up');
            }
            
        } catch (\Exception $e) {
            $this->error('Failed to cleanup salary reports: ' . $e->getMessage());
        }
    }
}
