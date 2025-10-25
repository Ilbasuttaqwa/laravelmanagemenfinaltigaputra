<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Lokasi;
use App\Models\Absensi;

class DebugLokasiData extends Command
{
    protected $signature = 'debug:lokasi-data';
    protected $description = 'Debug lokasi data relationships';

    public function handle()
    {
        $this->info('🔍 Debugging lokasi data...');

        try {
            // Check lokasi data
            $lokasis = Lokasi::all();
            $this->info("\n📍 Master Lokasi:");
            foreach ($lokasis as $lokasi) {
                $this->line("  - ID: {$lokasi->id}, Nama: {$lokasi->nama_lokasi}");
            }
            
            // Check employee data
            $employees = Employee::with('lokasi')->get();
            $this->info("\n👥 Employee Data:");
            foreach ($employees as $employee) {
                $lokasiNama = $employee->lokasi ? $employee->lokasi->nama_lokasi : 'TIDAK ADA LOKASI';
                $this->line("  - {$employee->nama} (ID: {$employee->id}) - Lokasi: {$lokasiNama} (lokasi_id: {$employee->lokasi_id})");
            }
            
            // Check absensi data
            $absensis = Absensi::with('employee.lokasi')->get();
            $this->info("\n📊 Absensi Data:");
            foreach ($absensis as $absensi) {
                $employeeLokasi = 'N/A';
                if ($absensi->employee && $absensi->employee->lokasi) {
                    $employeeLokasi = $absensi->employee->lokasi->nama_lokasi;
                }
                $this->line("  - ID: {$absensi->id}, Nama: {$absensi->nama_karyawan}, Lokasi Kerja: '{$absensi->lokasi_kerja}', Employee Lokasi: '{$employeeLokasi}'");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }
}
