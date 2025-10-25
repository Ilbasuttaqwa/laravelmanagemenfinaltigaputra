<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Employee;
use App\Models\Gudang;
use App\Models\Mandor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AbsensiService
{
    /**
     * Get latest salary for employee
     */
    public function getLatestSalary($employeeId)
    {
        try {
            if (str_starts_with($employeeId, 'gudang_')) {
                $gudangId = str_replace('gudang_', '', $employeeId);
                $gudang = Gudang::find($gudangId);
                return $gudang ? $gudang->gaji : 0;
            } elseif (str_starts_with($employeeId, 'employee_')) {
                $actualEmployeeId = str_replace('employee_', '', $employeeId);
                $employee = Employee::find($actualEmployeeId);
                return $employee ? $employee->gaji_pokok : 0;
            } elseif (str_starts_with($employeeId, 'mandor_')) {
                $mandorId = str_replace('mandor_', '', $employeeId);
                $mandor = Mandor::find($mandorId);
                return $mandor ? $mandor->gaji : 0;
            }
            return 0;
        } catch (\Exception $e) {
            Log::error('Error getting latest salary: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get employee data with latest salary
     */
    public function getEmployeeData($employeeId)
    {
        try {
            if (str_starts_with($employeeId, 'gudang_')) {
                $gudangId = str_replace('gudang_', '', $employeeId);
                $gudang = Gudang::find($gudangId);
                if (!$gudang) return null;
                
                return [
                    'id' => $employeeId,
                    'nama' => $gudang->nama,
                    'jabatan' => 'karyawan_gudang',
                    'gaji_pokok' => $gudang->gaji,
                    'lokasi_kerja' => 'Gudang',
                    'source' => 'gudang'
                ];
            } elseif (str_starts_with($employeeId, 'employee_')) {
                $actualEmployeeId = str_replace('employee_', '', $employeeId);
                $employee = Employee::with('kandang.lokasi')->find($actualEmployeeId);
                if (!$employee) return null;
                
                return [
                    'id' => $employeeId,
                    'nama' => $employee->nama,
                    'jabatan' => $employee->jabatan,
                    'gaji_pokok' => $employee->gaji_pokok,
                    'lokasi_kerja' => $employee->lokasi_kerja ?? ($employee->kandang->lokasi->nama_lokasi ?? 'Kantor Pusat'),
                    'source' => 'employee'
                ];
            } elseif (str_starts_with($employeeId, 'mandor_')) {
                $mandorId = str_replace('mandor_', '', $employeeId);
                $mandor = Mandor::find($mandorId);
                if (!$mandor) return null;
                
                return [
                    'id' => $employeeId,
                    'nama' => $mandor->nama,
                    'jabatan' => 'mandor',
                    'gaji_pokok' => $mandor->gaji,
                    'lokasi_kerja' => 'Kantor Pusat',
                    'source' => 'mandor'
                ];
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting employee data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create attendance record with validation
     */
    public function createAbsensi($data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // Get employee data
                $employeeData = $this->getEmployeeData($data['employee_id']);
                if (!$employeeData) {
                    throw new \Exception('Data karyawan tidak ditemukan.');
                }

                // Check for duplicate attendance
                $existingAbsensi = Absensi::where('nama_karyawan', $employeeData['nama'])
                    ->whereDate('tanggal', $data['tanggal'])
                    ->first();

                if ($existingAbsensi) {
                    throw new \Exception('Data absensi untuk karyawan ini pada tanggal tersebut sudah ada.');
                }

                // Create attendance record
                $absensiData = [
                    'employee_id' => str_starts_with($data['employee_id'], 'employee_') ? 
                        str_replace('employee_', '', $data['employee_id']) : null,
                    'pembibitan_id' => $data['pembibitan_id'] ?? null,
                    'nama_karyawan' => $employeeData['nama'],
                    'jabatan' => $employeeData['jabatan'],
                    'gaji_pokok_saat_itu' => $data['gaji_pokok_saat_itu'],
                    'tanggal' => $data['tanggal'],
                    'status' => $data['status'],
                    'gaji_hari_itu' => $data['gaji_hari_itu'],
                    'lokasi_kerja' => $employeeData['lokasi_kerja']
                ];

                $absensi = Absensi::create($absensiData);
                
                Log::info('Absensi created successfully', [
                    'absensi_id' => $absensi->id,
                    'employee_name' => $absensi->nama_karyawan,
                    'date' => $absensi->tanggal,
                    'salary' => $absensi->gaji_pokok_saat_itu
                ]);

                return $absensi;
            } catch (\Exception $e) {
                Log::error('Error creating absensi: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Update attendance record with validation
     */
    public function updateAbsensi($absensi, $data)
    {
        return DB::transaction(function () use ($absensi, $data) {
            try {
                // Get employee data
                $employeeData = $this->getEmployeeData($data['employee_id']);
                if (!$employeeData) {
                    throw new \Exception('Data karyawan tidak ditemukan.');
                }

                // Check for duplicate attendance (excluding current record)
                $existingAbsensi = Absensi::where('nama_karyawan', $employeeData['nama'])
                    ->whereDate('tanggal', $data['tanggal'])
                    ->where('id', '!=', $absensi->id)
                    ->first();

                if ($existingAbsensi) {
                    throw new \Exception('Data absensi untuk karyawan ini pada tanggal tersebut sudah ada.');
                }

                // Update attendance record
                $absensi->update([
                    'employee_id' => str_starts_with($data['employee_id'], 'employee_') ? 
                        str_replace('employee_', '', $data['employee_id']) : null,
                    'pembibitan_id' => $data['pembibitan_id'] ?? null,
                    'nama_karyawan' => $employeeData['nama'],
                    'jabatan' => $employeeData['jabatan'],
                    'gaji_pokok_saat_itu' => $data['gaji_pokok_saat_itu'],
                    'tanggal' => $data['tanggal'],
                    'status' => $data['status'],
                    'gaji_hari_itu' => $data['gaji_hari_itu'],
                    'lokasi_kerja' => $employeeData['lokasi_kerja']
                ]);

                Log::info('Absensi updated successfully', [
                    'absensi_id' => $absensi->id,
                    'employee_name' => $absensi->nama_karyawan,
                    'date' => $absensi->tanggal,
                    'salary' => $absensi->gaji_pokok_saat_itu
                ]);

                return $absensi;
            } catch (\Exception $e) {
                Log::error('Error updating absensi: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Get all employees with latest data
     */
    public function getAllEmployees()
    {
        try {
            $employees = collect();
            
            // Get regular employees
            $regularEmployees = Employee::orderBy('nama')->get()->map(function($employee) {
                return (object) [
                    'id' => 'employee_' . $employee->id,
                    'nama' => $employee->nama,
                    'jabatan' => $employee->jabatan,
                    'gaji_pokok' => $employee->gaji_pokok,
                    'source' => 'employee'
                ];
            });

            // Get gudang employees
            $gudangEmployees = Gudang::orderBy('nama')->get()->map(function($gudang) {
                return (object) [
                    'id' => 'gudang_' . $gudang->id,
                    'nama' => $gudang->nama,
                    'jabatan' => 'karyawan_gudang',
                    'gaji_pokok' => $gudang->gaji,
                    'source' => 'gudang'
                ];
            });

            // Get mandor employees
            $mandorEmployees = Mandor::orderBy('nama')->get()->map(function($mandor) {
                return (object) [
                    'id' => 'mandor_' . $mandor->id,
                    'nama' => $mandor->nama,
                    'jabatan' => 'mandor',
                    'gaji_pokok' => $mandor->gaji,
                    'source' => 'mandor'
                ];
            });

            // Combine with priority: gudang > employee > mandor
            $allEmployees = $gudangEmployees
                ->concat($regularEmployees)
                ->concat($mandorEmployees)
                ->unique('nama')
                ->sortBy('nama');

            return $allEmployees;
        } catch (\Exception $e) {
            Log::error('Error getting all employees: ' . $e->getMessage());
            return collect();
        }
    }
}
