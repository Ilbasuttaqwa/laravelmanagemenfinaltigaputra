<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SmartCacheService;
use App\Models\Absensi;
use Carbon\Carbon;

class AutoCleanupCommand extends Command
{
    protected $signature = 'system:cleanup 
                            {--force : Force cleanup without confirmation}
                            {--memory : Show memory usage before cleanup}
                            {--stats : Show cache statistics}';

    protected $description = 'Smart system cleanup for cache and old data';

    public function handle()
    {
        $this->info('🧹 Starting Smart System Cleanup...');
        
        if ($this->option('stats')) {
            $this->showCacheStats();
        }

        if ($this->option('memory')) {
            $this->showMemoryUsage();
        }

        // 1. Smart Cache Cleanup
        $this->info('📦 Cleaning up cache...');
        $clearedCount = SmartCacheService::smartCleanup();
        $this->info("✅ Cleared {$clearedCount} cache items");

        // 2. Cleanup old absensi data (older than 3 months)
        $this->info('🗑️  Cleaning up old attendance data...');
        $oldAbsensiCount = $this->cleanupOldAbsensi();
        $this->info("✅ Removed {$oldAbsensiCount} old attendance records");

        // 3. Optimize database
        $this->info('⚡ Optimizing database...');
        $this->optimizeDatabase();

        // 4. Clear compiled views
        $this->info('🎨 Clearing compiled views...');
        $this->call('view:clear');

        // 5. Clear config cache
        $this->info('⚙️  Clearing config cache...');
        $this->call('config:clear');

        // 6. Warm up critical cache
        $this->info('🔥 Warming up critical cache...');
        SmartCacheService::warmUpCache();

        $this->info('🎉 System cleanup completed successfully!');
        
        // Show final stats
        $this->showCacheStats();
    }

    private function cleanupOldAbsensi()
    {
        $threeMonthsAgo = Carbon::now()->subMonths(3);
        
        $oldAbsensi = Absensi::where('tanggal', '<', $threeMonthsAgo)->get();
        $count = $oldAbsensi->count();
        
        if ($count > 0) {
            if ($this->option('force') || $this->confirm("Found {$count} old attendance records. Delete them?")) {
                Absensi::where('tanggal', '<', $threeMonthsAgo)->delete();
                $this->info("🗑️  Deleted {$count} old attendance records");
            }
        }
        
        return $count;
    }

    private function optimizeDatabase()
    {
        try {
            // Optimize MySQL tables
            $tables = ['absensis', 'salary_reports', 'employees', 'gudangs', 'mandors', 'lokasis', 'kandangs', 'pembibitans'];
            
            foreach ($tables as $table) {
                \DB::statement("OPTIMIZE TABLE {$table}");
            }
            
            $this->info("✅ Database optimized");
        } catch (\Exception $e) {
            $this->warn("⚠️  Database optimization failed: " . $e->getMessage());
        }
    }

    private function showCacheStats()
    {
        $stats = SmartCacheService::getCacheStats();
        
        $this->info('📊 Cache Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Cache Keys', is_numeric($stats['total_keys']) ? number_format($stats['total_keys']) : $stats['total_keys']],
                ['Memory Usage', $this->formatBytes($stats['memory_usage'])],
                ['Memory Peak', $this->formatBytes($stats['memory_peak'])],
                ['Cache Hits', is_numeric($stats['cache_hits']) ? number_format($stats['cache_hits']) : $stats['cache_hits']],
                ['Cache Misses', is_numeric($stats['cache_misses']) ? number_format($stats['cache_misses']) : $stats['cache_misses']],
            ]
        );
    }

    private function showMemoryUsage()
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = ini_get('memory_limit');
        
        $this->info('💾 Memory Usage:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Current Usage', $this->formatBytes($memoryUsage)],
                ['Peak Usage', $this->formatBytes($memoryPeak)],
                ['Memory Limit', $memoryLimit],
                ['Usage %', round(($memoryUsage / $this->convertToBytes($memoryLimit)) * 100, 2) . '%'],
            ]
        );
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function convertToBytes($memoryLimit)
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit)-1]);
        $memoryLimit = (int) $memoryLimit;

        switch($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }

        return $memoryLimit;
    }
}