<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Lokasi;
use App\Models\Absensi;

class FixEmployeeLokasi extends Command
{
    protected $signature = 'fix:employee-lokasi';
    protected $description = 'Fix employee lokasi_id and update existing absensi records';

    public function handle()
    {
        $this->info('🔧 Memperbaiki lokasi_id di data employee...');

        try {
            // Get all employees
            $employees = Employee::all();
            $lokasis = Lokasi::all();
            
            $this->info("Found {$employees->count()} employees and {$lokasis->count()} locations");
            
            $updatedEmployees = 0;
            $updatedAbsensis = 0;
            
            foreach ($employees as $employee) {
                $oldLokasiId = $employee->lokasi_id;
                $newLokasiId = null;
                $newLokasiNama = 'Kantor Pusat';
                
                // Try to find matching lokasi by name or assign first available
                if ($lokasis->count() > 0) {
                    // If employee has no lokasi_id, assign first available
                    if (!$oldLokasiId) {
                        $newLokasiId = $lokasis->first()->id;
                        $newLokasiNama = $lokasis->first()->nama_lokasi;
                    } else {
                        // Check if current lokasi_id exists
                        $currentLokasi = $lokasis->find($oldLokasiId);
                        if ($currentLokasi) {
                            $newLokasiId = $oldLokasiId;
                            $newLokasiNama = $currentLokasi->nama_lokasi;
                        } else {
                            // Assign first available lokasi
                            $newLokasiId = $lokasis->first()->id;
                            $newLokasiNama = $lokasis->first()->nama_lokasi;
                        }
                    }
                    
                    // Update employee lokasi_id
                    if ($newLokasiId && $newLokasiId != $oldLokasiId) {
                        $employee->update(['lokasi_id' => $newLokasiId]);
                        $this->line("✅ Updated employee {$employee->nama}: lokasi_id {$oldLokasiId} → {$newLokasiId} ({$newLokasiNama})");
                        $updatedEmployees++;
                    }
                    
                    // Update all absensi records for this employee
                    $absensis = Absensi::where('employee_id', $employee->id)->get();
                    foreach ($absensis as $absensi) {
                        if ($absensi->lokasi_kerja !== $newLokasiNama) {
                            $absensi->update(['lokasi_kerja' => $newLokasiNama]);
                            $this->line("✅ Updated absensi ID {$absensi->id}: lokasi_kerja → {$newLokasiNama}");
                            $updatedAbsensis++;
                        }
                    }
                }
            }
            
            $this->info("🎉 Selesai! {$updatedEmployees} employees dan {$updatedAbsensis} absensi records diperbaiki.");
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }
}
