<?php
/**
 * Script untuk memperbaiki cache dan konsistensi data di cPanel
 * Jalankan script ini di cPanel untuk membersihkan semua cache
 */

echo "🧹 MEMBERSIHKAN CACHE CPANEL...\n";

// Clear Laravel caches
if (function_exists('artisan')) {
    echo "📝 Clearing Laravel caches...\n";
    
    // Clear config cache
    try {
        \Artisan::call('config:clear');
        echo "✅ Config cache cleared\n";
    } catch (Exception $e) {
        echo "❌ Config cache error: " . $e->getMessage() . "\n";
    }
    
    // Clear route cache
    try {
        \Artisan::call('route:clear');
        echo "✅ Route cache cleared\n";
    } catch (Exception $e) {
        echo "❌ Route cache error: " . $e->getMessage() . "\n";
    }
    
    // Clear view cache
    try {
        \Artisan::call('view:clear');
        echo "✅ View cache cleared\n";
    } catch (Exception $e) {
        echo "❌ View cache error: " . $e->getMessage() . "\n";
    }
    
    // Clear application cache
    try {
        \Artisan::call('cache:clear');
        echo "✅ Application cache cleared\n";
    } catch (Exception $e) {
        echo "❌ Application cache error: " . $e->getMessage() . "\n";
    }
    
    // Optimize for production
    try {
        \Artisan::call('config:cache');
        echo "✅ Config cached for production\n";
    } catch (Exception $e) {
        echo "❌ Config cache error: " . $e->getMessage() . "\n";
    }
    
    try {
        \Artisan::call('route:cache');
        echo "✅ Route cached for production\n";
    } catch (Exception $e) {
        echo "❌ Route cache error: " . $e->getMessage() . "\n";
    }
    
    try {
        \Artisan::call('view:cache');
        echo "✅ View cached for production\n";
    } catch (Exception $e) {
        echo "❌ View cache error: " . $e->getMessage() . "\n";
    }
}

// Clear specific cache keys
echo "🗑️ Clearing specific cache keys...\n";
try {
    \Cache::forget('lokasis_data');
    \Cache::forget('kandangs_data');
    \Cache::forget('pembibitans_data');
    \Cache::forget('gudangs_data');
    \Cache::forget('employees_data');
    \Cache::forget('absensis_data');
    \Cache::flush();
    echo "✅ Specific cache keys cleared\n";
} catch (Exception $e) {
    echo "❌ Cache keys error: " . $e->getMessage() . "\n";
}

// Clear Composer autoload
echo "🔄 Regenerating Composer autoload...\n";
try {
    exec('composer dump-autoload --optimize');
    echo "✅ Composer autoload regenerated\n";
} catch (Exception $e) {
    echo "❌ Composer error: " . $e->getMessage() . "\n";
}

// Clear browser cache headers
echo "🌐 Setting cache control headers...\n";
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo "\n🎉 CACHE CLEANING COMPLETED!\n";
echo "📋 Next steps:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Test all master data pages\n";
echo "3. Test absensi form submission\n";
echo "4. Verify real-time data updates\n";
?>
