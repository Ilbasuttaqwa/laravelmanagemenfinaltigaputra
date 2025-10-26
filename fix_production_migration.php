<?php
/**
 * Script untuk memperbaiki migration di production
 * 
 * Jalankan di terminal cPanel:
 * php fix_production_migration.php
 */

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    echo "🔧 Starting Production Migration Fix...\n";
    
    // 1. Reset migration yang gagal
    echo "📋 Resetting failed migrations...\n";
    $kernel->call('migrate:reset', ['--force' => true]);
    
    // 2. Run migrations dari awal
    echo "🚀 Running fresh migrations...\n";
    $kernel->call('migrate:fresh', [
        '--seed' => true,
        '--force' => true
    ]);
    
    // 3. Generate salary reports
    echo "💰 Generating salary reports...\n";
    $kernel->call('salary:generate', [
        'tahun' => 2025,
        'bulan' => 11
    ]);
    
    // 4. Clear caches
    echo "🧹 Clearing caches...\n";
    $kernel->call('cache:clear');
    $kernel->call('config:clear');
    $kernel->call('view:clear');
    
    echo "✅ Production migration fix completed successfully!\n";
    echo "🎉 System is ready for production use!\n";
    
} catch (Exception $e) {
    echo "❌ Error during migration fix: " . $e->getMessage() . "\n";
    echo "📞 Please contact support if this error persists.\n";
    exit(1);
}
