<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SmartCacheService;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsed = $endMemory - $startMemory;

        // Log slow queries (> 2 seconds)
        if ($executionTime > 2000) {
            Log::warning("Slow request detected", [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime . 'ms',
                'memory_used' => $this->formatBytes($memoryUsed),
                'user_id' => auth()->id(),
            ]);
        }

        // Auto cleanup jika memory usage tinggi
        if ($endMemory > (ini_get('memory_limit') * 0.8)) {
            SmartCacheService::emergencyCleanup();
            Log::info("Emergency cache cleanup triggered by high memory usage");
        }

        // Add performance headers
        $response->headers->set('X-Execution-Time', $executionTime . 'ms');
        $response->headers->set('X-Memory-Used', $this->formatBytes($memoryUsed));

        return $response;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
