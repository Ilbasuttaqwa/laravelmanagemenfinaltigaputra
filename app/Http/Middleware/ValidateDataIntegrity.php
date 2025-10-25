<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\DataSyncService;

class ValidateDataIntegrity
{
    protected $dataSyncService;

    public function __construct(DataSyncService $dataSyncService)
    {
        $this->dataSyncService = $dataSyncService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Validate data integrity for critical operations
            if ($this->shouldValidate($request)) {
                $validation = $this->dataSyncService->validateDataIntegrity();
                
                if ($validation['status'] === 'error') {
                    Log::error('Data integrity validation failed', $validation);
                    return response()->json([
                        'error' => 'Data integrity validation failed',
                        'message' => $validation['message']
                    ], 500);
                }
                
                if ($validation['status'] === 'warning') {
                    Log::warning('Data integrity issues found', $validation);
                    // Continue but log the issues
                }
            }
            
            return $next($request);
        } catch (\Exception $e) {
            Log::error('Data integrity middleware error: ' . $e->getMessage());
            return $next($request);
        }
    }

    /**
     * Determine if request should be validated
     */
    private function shouldValidate(Request $request)
    {
        $criticalRoutes = [
            'absensis.store',
            'absensis.update',
            'employees.store',
            'employees.update',
            'gudangs.store',
            'gudangs.update',
            'mandors.store',
            'mandors.update'
        ];
        
        return in_array($request->route()->getName(), $criticalRoutes);
    }
}
