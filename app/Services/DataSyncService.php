<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Employee;
use App\Models\Gudang;
use App\Models\Mandor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataSyncService
{
    /**
     * Clear all caches related to employee data
     */
    public function clearEmployeeCache()
    {
        try {
            Cache::forget('all_employees');
            Cache::forget('gudang_employees');
            Cache::forget('mandor_employees');
            Cache::forget('regular_employees');
            
            Log::info('Employee cache cleared successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('Error clearing employee cache: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all caches related to attendance data
     */
    public function clearAbsensiCache()
    {
        try {
            Cache::forget('absensi_data');
            Cache::forget('attendance_reports');
            
            Log::info('Absensi cache cleared successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('Error clearing absensi cache: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync employee data changes to attendance records
     */
    public function syncEmployeeChanges($employeeId, $type = 'employee')
    {
        try {
            DB::beginTransaction();
            
            if ($type === 'gudang') {
                $gudang = Gudang::find($employeeId);
                if ($gudang) {
                    // Update all attendance records for this gudang employee
                    Absensi::where('nama_karyawan', $gudang->nama)
                        ->where('jabatan', 'karyawan_gudang')
                        ->update([
                            'lokasi_kerja' => 'Gudang'
                        ]);
                }
            } elseif ($type === 'employee') {
                $employee = Employee::with('kandang.lokasi')->find($employeeId);
                if ($employee) {
                    // Update all attendance records for this employee
                    Absensi::where('nama_karyawan', $employee->nama)
                        ->where('jabatan', $employee->jabatan)
                        ->update([
                            'lokasi_kerja' => $employee->lokasi_kerja ?? ($employee->kandang->lokasi->nama_lokasi ?? 'Kantor Pusat')
                        ]);
                }
            } elseif ($type === 'mandor') {
                $mandor = Mandor::find($employeeId);
                if ($mandor) {
                    // Update all attendance records for this mandor
                    Absensi::where('nama_karyawan', $mandor->nama)
                        ->where('jabatan', 'mandor')
                        ->update([
                            'lokasi_kerja' => 'Kantor Pusat'
                        ]);
                }
            }
            
            DB::commit();
            $this->clearAbsensiCache();
            
            Log::info('Employee changes synced successfully', [
                'employee_id' => $employeeId,
                'type' => $type
            ]);
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncing employee changes: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate data integrity
     */
    public function validateDataIntegrity()
    {
        try {
            $issues = [];
            
            // Check for orphaned attendance records
            $orphanedAbsensi = Absensi::whereNotNull('employee_id')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('employees')
                        ->whereColumn('employees.id', 'absensis.employee_id');
                })
                ->count();
            
            if ($orphanedAbsensi > 0) {
                $issues[] = "Found {$orphanedAbsensi} orphaned attendance records";
            }
            
            // Check for inconsistent salary data
            $inconsistentSalary = Absensi::join('employees', 'absensis.employee_id', '=', 'employees.id')
                ->whereRaw('absensis.gaji_pokok_saat_itu != employees.gaji_pokok')
                ->count();
            
            if ($inconsistentSalary > 0) {
                $issues[] = "Found {$inconsistentSalary} records with inconsistent salary data";
            }
            
            // Check for duplicate attendance records
            $duplicates = Absensi::select('nama_karyawan', 'tanggal')
                ->groupBy('nama_karyawan', 'tanggal')
                ->havingRaw('COUNT(*) > 1')
                ->count();
            
            if ($duplicates > 0) {
                $issues[] = "Found {$duplicates} duplicate attendance records";
            }
            
            if (empty($issues)) {
                Log::info('Data integrity validation passed');
                return ['status' => 'success', 'message' => 'Data integrity is valid'];
            } else {
                Log::warning('Data integrity issues found', ['issues' => $issues]);
                return ['status' => 'warning', 'issues' => $issues];
            }
        } catch (\Exception $e) {
            Log::error('Error validating data integrity: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Fix data integrity issues
     */
    public function fixDataIntegrity()
    {
        try {
            DB::beginTransaction();
            
            // Fix orphaned attendance records
            Absensi::whereNotNull('employee_id')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('employees')
                        ->whereColumn('employees.id', 'absensis.employee_id');
                })
                ->update(['employee_id' => null]);
            
            // Update salary data to match current employee salary
            Absensi::join('employees', 'absensis.employee_id', '=', 'employees.id')
                ->whereRaw('absensis.gaji_pokok_saat_itu != employees.gaji_pokok')
                ->update(['absensis.gaji_pokok_saat_itu' => DB::raw('employees.gaji_pokok')]);
            
            // Remove duplicate attendance records (keep the latest one)
            $duplicates = Absensi::select('nama_karyawan', 'tanggal')
                ->groupBy('nama_karyawan', 'tanggal')
                ->havingRaw('COUNT(*) > 1')
                ->get();
            
            foreach ($duplicates as $duplicate) {
                Absensi::where('nama_karyawan', $duplicate->nama_karyawan)
                    ->where('tanggal', $duplicate->tanggal)
                    ->orderBy('created_at', 'desc')
                    ->skip(1)
                    ->delete();
            }
            
            DB::commit();
            $this->clearAbsensiCache();
            
            Log::info('Data integrity issues fixed successfully');
            return ['status' => 'success', 'message' => 'Data integrity issues fixed'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error fixing data integrity: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
