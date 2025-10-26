<?php
/**
 * Auto-cleanup script untuk dijalankan via cron job
 * Script ini akan berjalan otomatis setiap hari dan:
 * 1. Sync SalaryReportController logic
 * 2. Cleanup data absensi lama (1 bulan)
 * 3. Cleanup log files lama
 * 4. Cleanup cache files
 * 5. Cleanup salary reports lama (optional)
 * 
 * Cara penggunaan:
 * 1. Upload script ini ke hosting
 * 2. Set cron job: 0 2 * * * php /path/to/auto_cleanup_cron.php
 * 3. Script akan berjalan otomatis setiap hari jam 2 pagi
 */

// Set time limit untuk script yang berjalan lama
set_time_limit(600); // 10 minutes

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "[" . date('Y-m-d H:i:s') . "] Starting auto-cleanup...\n";

try {
    // Check if cleanup is needed
    $lastCleanup = \Illuminate\Support\Facades\Cache::get('auto_cleanup_last_run', 0);
    $currentTime = time();
    
    // Only cleanup if it's been more than 20 hours (to allow daily run)
    if (($currentTime - $lastCleanup) < 72000) {
        echo "Auto-cleanup not needed (last run: " . date('Y-m-d H:i:s', $lastCleanup) . ")\n";
        exit(0);
    }
    
    // 1. Sync Salary Reports Logic
    echo "Syncing salary reports logic...\n";
    syncSalaryReportsLogic();
    
    // 2. Cleanup old absensi data (30 days)
    echo "Cleaning up absensi data older than 30 days...\n";
    cleanupOldAbsensi(30);
    
    // 3. Cleanup old logs (7 days)
    echo "Cleaning up log files older than 7 days...\n";
    cleanupOldLogs(7);
    
    // 4. Cleanup old cache
    echo "Cleaning up old cache...\n";
    cleanupOldCache();
    
    // 5. Cleanup old salary reports (90 days)
    echo "Cleaning up salary reports older than 90 days...\n";
    cleanupOldSalaryReports(90);
    
    // 6. Update cleanup timestamp
    \Illuminate\Support\Facades\Cache::put('auto_cleanup_last_run', $currentTime, 86400);
    
    echo "[" . date('Y-m-d H:i:s') . "] Auto-cleanup completed successfully!\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Auto-cleanup failed: " . $e->getMessage() . "\n";
    \Illuminate\Support\Facades\Log::error('Auto-cleanup cron failed: ' . $e->getMessage());
}

/**
 * Sync salary reports logic
 */
function syncSalaryReportsLogic() {
    try {
        $controllerPath = __DIR__ . '/app/Http/Controllers/SalaryReportController.php';
        
        if (!file_exists($controllerPath)) {
            echo "❌ SalaryReportController not found!\n";
            return;
        }
        
        $content = file_get_contents($controllerPath);
        
        // Check if hasFilter logic exists
        if (strpos($content, 'hasFilter') === false) {
            echo "hasFilter logic not found, updating controller...\n";
            updateController($controllerPath, $content);
        } else {
            echo "✓ hasFilter logic already exists\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Failed to sync salary reports logic: " . $e->getMessage() . "\n";
    }
}

/**
 * Update controller with hasFilter logic
 */
function updateController($controllerPath, $content) {
    try {
        // Backup the original file
        $backupPath = $controllerPath . '.backup.' . date('Y-m-d_H-i-s');
        copy($controllerPath, $backupPath);
        echo "✓ Backup created: " . basename($backupPath) . "\n";
        
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
            
            echo "✓ SalaryReportController updated with hasFilter logic\n";
        } else {
            echo "❌ Could not find insertion point for hasFilter logic\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Failed to update controller: " . $e->getMessage() . "\n";
    }
}

/**
 * Cleanup old absensi data
 */
function cleanupOldAbsensi($days) {
    try {
        $cutoffDate = \Carbon\Carbon::now()->subDays($days);
        
        // Count records to be deleted
        $count = \Illuminate\Support\Facades\DB::table('absensis')
            ->where('created_at', '<', $cutoffDate)
            ->count();
        
        if ($count > 0) {
            // Delete old absensi records
            $deleted = \Illuminate\Support\Facades\DB::table('absensis')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
            
            echo "✓ Deleted {$deleted} old absensi records\n";
            
            // Log the cleanup
            \Illuminate\Support\Facades\Log::info("Auto-cleanup: Deleted {$deleted} absensi records older than {$days} days");
        } else {
            echo "✓ No old absensi records to clean up\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Failed to cleanup absensi data: " . $e->getMessage() . "\n";
    }
}

/**
 * Cleanup old logs
 */
function cleanupOldLogs($days) {
    try {
        $logPath = __DIR__ . '/storage/logs';
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
            echo "✓ Deleted {$deletedCount} old log files\n";
            \Illuminate\Support\Facades\Log::info("Auto-cleanup: Deleted {$deletedCount} log files older than {$days} days");
        } else {
            echo "✓ No old log files to clean up\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Failed to cleanup logs: " . $e->getMessage() . "\n";
    }
}

/**
 * Cleanup old cache
 */
function cleanupOldCache() {
    try {
        $deletedCount = 0;
        
        // Clear view cache
        $viewCacheDir = __DIR__ . '/storage/framework/views';
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
        $appCacheDir = __DIR__ . '/storage/framework/cache/data';
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
        $sessionDir = __DIR__ . '/storage/framework/sessions';
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
            echo "✓ Cleared {$deletedCount} cache files\n";
            \Illuminate\Support\Facades\Log::info("Auto-cleanup: Cleared {$deletedCount} cache files");
        } else {
            echo "✓ No cache files to clean up\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Failed to clear cache: " . $e->getMessage() . "\n";
    }
}

/**
 * Cleanup old salary reports
 */
function cleanupOldSalaryReports($days) {
    try {
        $cutoffDate = \Carbon\Carbon::now()->subDays($days);
        
        // Count records to be deleted
        $count = \Illuminate\Support\Facades\DB::table('salary_reports')
            ->where('created_at', '<', $cutoffDate)
            ->count();
        
        if ($count > 0) {
            // Delete old salary reports
            $deleted = \Illuminate\Support\Facades\DB::table('salary_reports')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
            
            echo "✓ Deleted {$deleted} old salary reports\n";
            
            // Log the cleanup
            \Illuminate\Support\Facades\Log::info("Auto-cleanup: Deleted {$deleted} salary reports older than {$days} days");
        } else {
            echo "✓ No old salary reports to clean up\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Failed to cleanup salary reports: " . $e->getMessage() . "\n";
    }
}
