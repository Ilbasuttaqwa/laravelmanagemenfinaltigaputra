<?php
/**
 * Script sederhana untuk clear cache di hosting
 * Jalankan script ini jika fix_hosting_salary_reports.php tidak berhasil
 */

echo "=== CLEARING HOSTING CACHE ===\n";

// Clear config cache
echo "1. Clearing config cache...\n";
$configCache = 'bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    echo "   ✓ Config cache cleared\n";
} else {
    echo "   - Config cache not found\n";
}

// Clear route cache
echo "2. Clearing route cache...\n";
$routeCache = 'bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "   ✓ Route cache cleared\n";
} else {
    echo "   - Route cache not found\n";
}

// Clear view cache
echo "3. Clearing view cache...\n";
$viewCacheDir = 'storage/framework/views';
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    echo "   ✓ Cleared {$count} view cache files\n";
} else {
    echo "   - View cache directory not found\n";
}

// Clear application cache
echo "4. Clearing application cache...\n";
$appCacheDir = 'storage/framework/cache/data';
if (is_dir($appCacheDir)) {
    $files = glob($appCacheDir . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    echo "   ✓ Cleared {$count} application cache files\n";
} else {
    echo "   - Application cache directory not found\n";
}

// Clear session cache
echo "5. Clearing session cache...\n";
$sessionDir = 'storage/framework/sessions';
if (is_dir($sessionDir)) {
    $files = glob($sessionDir . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    echo "   ✓ Cleared {$count} session files\n";
} else {
    echo "   - Session directory not found\n";
}

echo "\n=== CACHE CLEARED ===\n";
echo "Please refresh your browser and test the salary reports page.\n";
