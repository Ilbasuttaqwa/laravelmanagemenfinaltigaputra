<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\DataSyncService;
use App\Models\Absensi;
use App\Models\Employee;
use App\Models\Gudang;
use App\Models\Mandor;

class SystemMonitorController extends Controller
{
    protected $dataSyncService;

    public function __construct(DataSyncService $dataSyncService)
    {
        $this->dataSyncService = $dataSyncService;
    }

    /**
     * System health dashboard
     */
    public function dashboard()
    {
        try {
            $stats = [
                'total_employees' => Employee::count(),
                'total_gudang' => Gudang::count(),
                'total_mandor' => Mandor::count(),
                'total_absensi' => Absensi::count(),
                'absensi_today' => Absensi::whereDate('tanggal', today())->count(),
                'absensi_this_month' => Absensi::whereMonth('tanggal', now()->month)->count(),
            ];

            $dataIntegrity = $this->dataSyncService->validateDataIntegrity();
            
            $recentAbsensi = Absensi::with('employee')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('admin.monitor', compact('stats', 'dataIntegrity', 'recentAbsensi'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API endpoint for system status
     */
    public function status()
    {
        try {
            $status = [
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
                'data_integrity' => $this->dataSyncService->validateDataIntegrity(),
                'timestamp' => now()->toISOString()
            ];

            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Check database connectivity
     */
    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'connected', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check cache functionality
     */
    private function checkCache()
    {
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 60);
            $value = Cache::get($testKey);
            Cache::forget($testKey);
            
            if ($value === 'test') {
                return ['status' => 'working', 'message' => 'Cache is functioning properly'];
            } else {
                return ['status' => 'error', 'message' => 'Cache test failed'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Fix data integrity issues
     */
    public function fixIntegrity()
    {
        try {
            $result = $this->dataSyncService->fixDataIntegrity();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
