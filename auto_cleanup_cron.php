<?php
/**
 * Auto Cleanup Cron Job
 * 
 * Jalankan setiap hari jam 2 pagi untuk cleanup otomatis
 * 
 * Crontab entry:
 * 0 2 * * * /usr/bin/php /path/to/your/project/auto_cleanup_cron.php
 * 
 * Atau untuk testing (setiap 5 menit):
 * */5 * * * * /usr/bin/php /path/to/your/project/auto_cleanup_cron.php
 */

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    echo "[" . date('Y-m-d H:i:s') . "] Starting auto cleanup...\n";
    
    // Run cleanup command
    $exitCode = $kernel->call('system:cleanup', [
        '--force' => true,
        '--stats' => true
    ]);
    
    if ($exitCode === 0) {
        echo "[" . date('Y-m-d H:i:s') . "] Auto cleanup completed successfully!\n";
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] Auto cleanup failed with exit code: {$exitCode}\n";
    }
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error during auto cleanup: " . $e->getMessage() . "\n";
    exit(1);
}
