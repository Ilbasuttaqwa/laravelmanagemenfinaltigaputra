<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceOptimizationMiddleware
{
    /**
     * Handle an incoming request.
     * Optimize performance for large datasets
     */
    public function handle(Request $request, Closure $next)
    {
        // Start performance monitoring
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Apply optimizations based on route
        if ($request->is('manager/absensis*') || $request->is('admin/absensis*')) {
            $this->optimizeAbsensiPerformance();
        }
        
        if ($request->is('manager/salary-reports*') || $request->is('admin/salary-reports*')) {
            $this->optimizeSalaryReportsPerformance();
        }
        
        // Process request
        $response = $next($request);
        
        // Log performance metrics
        $this->logPerformanceMetrics($startTime, $startMemory, $request);
        
        return $response;
    }
    
    /**
     * Optimize absensi performance
     */
    private function optimizeAbsensiPerformance()
    {
        // Set query timeout for large datasets
        DB::statement('SET SESSION wait_timeout = 300');
        DB::statement('SET SESSION interactive_timeout = 300');
        
        // Optimize MySQL settings for better performance
        DB::statement('SET SESSION sort_buffer_size = 2097152'); // 2MB
        DB::statement('SET SESSION read_buffer_size = 131072'); // 128KB
        DB::statement('SET SESSION read_rnd_buffer_size = 262144'); // 256KB
        
        // Enable query cache if available
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET SESSION query_cache_type = ON');
        }
    }
    
    /**
     * Optimize salary reports performance
     */
    private function optimizeSalaryReportsPerformance()
    {
        // Use read replica if available
        if (config('database.connections.mysql.read')) {
            DB::setDefaultConnection('mysql_read');
        }
        
        // Set larger buffer sizes for complex queries
        DB::statement('SET SESSION sort_buffer_size = 4194304'); // 4MB
        DB::statement('SET SESSION join_buffer_size = 2097152'); // 2MB
    }
    
    /**
     * Log performance metrics
     */
    private function logPerformanceMetrics($startTime, $startMemory, Request $request)
    {
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $executionTime = round(($endTime - $startTime) * 1000, 2); // milliseconds
        $memoryUsage = round(($endMemory - $startMemory) / 1024 / 1024, 2); // MB
        
        // Log slow queries (> 2 seconds)
        if ($executionTime > 2000) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime . 'ms',
                'memory_usage' => $memoryUsage . 'MB',
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);
        }
        
        // Log high memory usage (> 50MB)
        if ($memoryUsage > 50) {
            Log::warning('High memory usage detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'memory_usage' => $memoryUsage . 'MB',
                'execution_time' => $executionTime . 'ms'
            ]);
        }
    }
}
