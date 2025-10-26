<?php
/**
 * Auto-sync script untuk dijalankan via cron job
 * Script ini akan berjalan otomatis setiap jam dan memastikan
 * SalaryReportController selalu menggunakan logika terbaru
 * 
 * Cara penggunaan:
 * 1. Upload script ini ke hosting
 * 2. Set cron job: 0 * * * * php /path/to/auto_sync_cron.php
 * 3. Script akan berjalan otomatis setiap jam
 */

// Set time limit untuk script yang berjalan lama
set_time_limit(300);

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "[" . date('Y-m-d H:i:s') . "] Starting auto-sync...\n";

try {
    // Check if sync is needed
    $lastSync = \Illuminate\Support\Facades\Cache::get('auto_sync_last_run', 0);
    $currentTime = time();
    
    // Only sync if it's been more than 1 hour
    if (($currentTime - $lastSync) < 3600) {
        echo "Auto-sync not needed (last run: " . date('Y-m-d H:i:s', $lastSync) . ")\n";
        exit(0);
    }
    
    // 1. Check SalaryReportController
    echo "Checking SalaryReportController...\n";
    $controllerPath = __DIR__ . '/app/Http/Controllers/SalaryReportController.php';
    
    if (file_exists($controllerPath)) {
        $content = file_get_contents($controllerPath);
        
        // Check if hasFilter logic exists
        if (strpos($content, 'hasFilter') === false) {
            echo "hasFilter logic not found, updating controller...\n";
            updateController($controllerPath, $content);
        } else {
            echo "✓ hasFilter logic already exists\n";
        }
    } else {
        echo "❌ SalaryReportController not found!\n";
    }
    
    // 2. Clear cache if needed
    echo "Checking cache...\n";
    $cacheKey = 'last_cache_clear';
    $lastClear = \Illuminate\Support\Facades\Cache::get($cacheKey, 0);
    
    if (($currentTime - $lastClear) > 7200) { // 2 hours
        echo "Clearing cache...\n";
        clearAllCaches();
        \Illuminate\Support\Facades\Cache::put($cacheKey, $currentTime, 7200);
        echo "✓ Cache cleared\n";
    } else {
        echo "✓ Cache is fresh\n";
    }
    
    // 3. Update sync timestamp
    \Illuminate\Support\Facades\Cache::put('auto_sync_last_run', $currentTime, 3600);
    
    echo "[" . date('Y-m-d H:i:s') . "] Auto-sync completed successfully!\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Auto-sync failed: " . $e->getMessage() . "\n";
    \Illuminate\Support\Facades\Log::error('Auto-sync cron failed: ' . $e->getMessage());
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
 * Clear all relevant caches
 */
function clearAllCaches() {
    try {
        // Clear config cache
        if (file_exists(__DIR__ . '/bootstrap/cache/config.php')) {
            unlink(__DIR__ . '/bootstrap/cache/config.php');
        }
        
        // Clear route cache
        if (file_exists(__DIR__ . '/bootstrap/cache/routes-v7.php')) {
            unlink(__DIR__ . '/bootstrap/cache/routes-v7.php');
        }
        
        // Clear view cache
        $viewCacheDir = __DIR__ . '/storage/framework/views';
        if (is_dir($viewCacheDir)) {
            $files = glob($viewCacheDir . '/*');
            $count = 0;
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $count++;
                }
            }
            echo "  - Cleared {$count} view cache files\n";
        }
        
        // Clear application cache
        $appCacheDir = __DIR__ . '/storage/framework/cache/data';
        if (is_dir($appCacheDir)) {
            $files = glob($appCacheDir . '/*');
            $count = 0;
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $count++;
                }
            }
            echo "  - Cleared {$count} application cache files\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Failed to clear cache: " . $e->getMessage() . "\n";
    }
}
