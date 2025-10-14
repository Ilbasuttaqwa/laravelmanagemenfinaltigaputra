<?php

namespace App\Services;

use App\Models\UnifiedEmployee;
use App\Models\Employee;
use App\Models\Gudang;
use App\Models\Mandor;

class MasterSyncService
{
    /**
     * Sync all master data to unified employees table
     */
    public static function syncAll()
    {
        // Clear existing data
        UnifiedEmployee::truncate();
        
        // Sync employees
        self::syncEmployees();
        
        // Sync gudang employees
        self::syncGudangEmployees();
        
        // Sync mandor employees
        self::syncMandorEmployees();
    }

    /**
     * Sync employees table
     */
    public static function syncEmployees()
    {
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            UnifiedEmployee::updateOrCreate(
                [
                    'source_type' => 'employee',
                    'source_id' => $employee->id,
                ],
                [
                    'nama' => $employee->nama,
                    'gaji' => $employee->gaji,
                    'role' => $employee->role,
                ]
            );
        }
    }

    /**
     * Sync gudang employees
     */
    public static function syncGudangEmployees()
    {
        $gudangs = Gudang::all();
        
        foreach ($gudangs as $gudang) {
            UnifiedEmployee::updateOrCreate(
                [
                    'source_type' => 'gudang',
                    'source_id' => $gudang->id,
                ],
                [
                    'nama' => $gudang->nama,
                    'gaji' => $gudang->gaji,
                    'role' => 'karyawan_gudang',
                ]
            );
        }
    }

    /**
     * Sync mandor employees
     */
    public static function syncMandorEmployees()
    {
        $mandors = Mandor::all();
        
        foreach ($mandors as $mandor) {
            UnifiedEmployee::updateOrCreate(
                [
                    'source_type' => 'mandor',
                    'source_id' => $mandor->id,
                ],
                [
                    'nama' => $mandor->nama,
                    'gaji' => $mandor->gaji,
                    'role' => 'mandor',
                ]
            );
        }
    }

    /**
     * Sync single employee from any source
     */
    public static function syncEmployee($sourceType, $sourceId)
    {
        switch ($sourceType) {
            case 'employee':
                $employee = Employee::find($sourceId);
                if ($employee) {
                    UnifiedEmployee::updateOrCreate(
                        [
                            'source_type' => 'employee',
                            'source_id' => $employee->id,
                        ],
                        [
                            'nama' => $employee->nama,
                            'gaji' => $employee->gaji,
                            'role' => $employee->role,
                        ]
                    );
                }
                break;
                
            case 'gudang':
                $gudang = Gudang::find($sourceId);
                if ($gudang) {
                    UnifiedEmployee::updateOrCreate(
                        [
                            'source_type' => 'gudang',
                            'source_id' => $gudang->id,
                        ],
                        [
                            'nama' => $gudang->nama,
                            'gaji' => $gudang->gaji,
                            'role' => 'karyawan_gudang',
                        ]
                    );
                }
                break;
                
            case 'mandor':
                $mandor = Mandor::find($sourceId);
                if ($mandor) {
                    UnifiedEmployee::updateOrCreate(
                        [
                            'source_type' => 'mandor',
                            'source_id' => $mandor->id,
                        ],
                        [
                            'nama' => $mandor->nama,
                            'gaji' => $mandor->gaji,
                            'role' => 'mandor',
                        ]
                    );
                }
                break;
        }
    }

    /**
     * Remove employee from unified table
     */
    public static function removeEmployee($sourceType, $sourceId)
    {
        UnifiedEmployee::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->delete();
    }
}
