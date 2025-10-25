<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Employee;
use App\Models\Lokasi;
use App\Models\Kandang;
use App\Models\Pembibitan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RealtimeController extends Controller
{
    /**
     * Get real-time absensi data
     */
    public function getAbsensiData(Request $request)
    {
        try {
            $cacheKey = 'realtime_absensi_' . auth()->id();
            $cached = Cache::get($cacheKey);
            
            if ($cached && !$request->has('force_refresh')) {
                return response()->json([
                    'success' => true,
                    'data' => $cached,
                    'cached' => true,
                    'timestamp' => now()
                ]);
            }

            $query = Absensi::with(['employee'])
                ->orderBy('tanggal', 'desc')
                ->orderBy('created_at', 'desc');

            // Apply role-based filtering
            if (auth()->user()->isAdmin()) {
                $query->whereHas('employee', function($q) {
                    $q->where('jabatan', 'karyawan');
                });
            }

            $absensis = $query->limit(100)->get();

            $data = [
                'absensis' => $absensis,
                'total_count' => $absensis->count(),
                'last_updated' => now()->toISOString()
            ];

            // Cache for 30 seconds
            Cache::put($cacheKey, $data, 30);

            return response()->json([
                'success' => true,
                'data' => $data,
                'cached' => false,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching absensi data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time employee data
     */
    public function getEmployeeData(Request $request)
    {
        try {
            $cacheKey = 'realtime_employees_' . auth()->id();
            $cached = Cache::get($cacheKey);
            
            if ($cached && !$request->has('force_refresh')) {
                return response()->json([
                    'success' => true,
                    'data' => $cached,
                    'cached' => true,
                    'timestamp' => now()
                ]);
            }

            $query = Employee::with(['lokasi', 'kandang']);

            // Apply role-based filtering
            if (auth()->user()->isAdmin()) {
                $query->where('jabatan', 'karyawan');
            }

            $employees = $query->orderBy('nama')->get();

            $data = [
                'employees' => $employees,
                'total_count' => $employees->count(),
                'last_updated' => now()->toISOString()
            ];

            // Cache for 60 seconds
            Cache::put($cacheKey, $data, 60);

            return response()->json([
                'success' => true,
                'data' => $data,
                'cached' => false,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching employee data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time master data
     */
    public function getMasterData(Request $request)
    {
        try {
            $cacheKey = 'realtime_master_' . auth()->id();
            $cached = Cache::get($cacheKey);
            
            if ($cached && !$request->has('force_refresh')) {
                return response()->json([
                    'success' => true,
                    'data' => $cached,
                    'cached' => true,
                    'timestamp' => now()
                ]);
            }

            $data = [
                'lokasis' => Lokasi::orderBy('nama_lokasi')->get(),
                'kandangs' => Kandang::with('lokasi')->orderBy('nama_kandang')->get(),
                'pembibitans' => Pembibitan::with(['lokasi', 'kandang'])->orderBy('judul')->get(),
                'last_updated' => now()->toISOString()
            ];

            // Cache for 2 minutes
            Cache::put($cacheKey, $data, 120);

            return response()->json([
                'success' => true,
                'data' => $data,
                'cached' => false,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching master data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system performance metrics
     */
    public function getPerformanceMetrics(Request $request)
    {
        try {
            $metrics = [
                'database' => [
                    'absensi_count' => Absensi::count(),
                    'employee_count' => Employee::count(),
                    'lokasi_count' => Lokasi::count(),
                    'kandang_count' => Kandang::count(),
                    'pembibitan_count' => Pembibitan::count(),
                ],
                'cache' => [
                    'hit_rate' => $this->getCacheHitRate(),
                    'memory_usage' => memory_get_usage(true),
                    'peak_memory' => memory_get_peak_usage(true),
                ],
                'system' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'uptime' => $this->getSystemUptime(),
                    'load_average' => $this->getLoadAverage(),
                ],
                'timestamp' => now()->toISOString()
            ];

            return response()->json([
                'success' => true,
                'metrics' => $metrics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching performance metrics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all caches
     */
    public function clearCache(Request $request)
    {
        try {
            Cache::flush();
            
            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully',
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cache hit rate
     */
    private function getCacheHitRate()
    {
        // This is a simplified implementation
        // In production, you'd want to use Redis or Memcached with proper metrics
        return rand(80, 95); // Mock data for now
    }

    /**
     * Get system uptime
     */
    private function getSystemUptime()
    {
        if (function_exists('sys_getloadavg')) {
            $uptime = shell_exec('uptime');
            return $uptime ? trim($uptime) : 'Unknown';
        }
        return 'Unknown';
    }

    /**
     * Get load average
     */
    private function getLoadAverage()
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                '1min' => $load[0] ?? 0,
                '5min' => $load[1] ?? 0,
                '15min' => $load[2] ?? 0,
            ];
        }
        return ['1min' => 0, '5min' => 0, '15min' => 0];
    }
}
