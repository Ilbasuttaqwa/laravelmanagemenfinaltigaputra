<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use App\Models\Employee;

class CreateTestAbsensi extends Command
{
    protected $signature = 'test:absensi';
    protected $description = 'Create test absensi data with proper employee names';

    public function handle()
    {
        // Clear existing absensi data
        Absensi::truncate();
        
        // Get first employee
        $employee = Employee::first();
        
        if (!$employee) {
            $this->error('No employees found. Please run: php artisan db:seed --class=EmployeeSeeder');
            return;
        }
        
        // Create test absensi with proper data
        $absensi = Absensi::create([
            'employee_id' => $employee->id,
            'source_type' => 'employee',
            'source_id' => $employee->id,
            'nama_karyawan' => $employee->nama, // Store the name directly
            'role_karyawan' => $employee->role, // Store the role directly
            'gaji_karyawan' => $employee->gaji, // Store the salary directly
            'tanggal' => now(),
            'status' => 'full',
            'lokasi_kerja' => $employee->lokasi_kerja ?? 'Kantor Pusat'
        ]);
        
        $this->info('Test absensi created successfully!');
        $this->info('Employee: ' . $employee->nama);
        $this->info('Role: ' . $employee->role);
        $this->info('Salary: Rp ' . number_format($employee->gaji, 0, ',', '.'));
    }
}