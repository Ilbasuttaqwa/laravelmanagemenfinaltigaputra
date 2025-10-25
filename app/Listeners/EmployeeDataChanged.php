<?php

namespace App\Listeners;

use App\Services\DataSyncService;
use Illuminate\Support\Facades\Log;

class EmployeeDataChanged
{
    protected $dataSyncService;

    public function __construct(DataSyncService $dataSyncService)
    {
        $this->dataSyncService = $dataSyncService;
    }

    /**
     * Handle the event.
     */
    public function handle($event)
    {
        try {
            // Clear employee cache
            $this->dataSyncService->clearEmployeeCache();
            
            // Sync changes to attendance records
            if (isset($event->employee)) {
                $employee = $event->employee;
                $type = $this->getEmployeeType($employee);
                $this->dataSyncService->syncEmployeeChanges($employee->id, $type);
            }
            
            Log::info('Employee data changed event handled', [
                'event' => get_class($event),
                'employee_id' => $event->employee->id ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling employee data changed event: ' . $e->getMessage());
        }
    }

    /**
     * Determine employee type
     */
    private function getEmployeeType($employee)
    {
        if ($employee instanceof \App\Models\Gudang) {
            return 'gudang';
        } elseif ($employee instanceof \App\Models\Mandor) {
            return 'mandor';
        } else {
            return 'employee';
        }
    }
}
