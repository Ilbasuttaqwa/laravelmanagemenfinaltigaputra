<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalaryReport;
use App\Models\Employee;
use App\Models\Absensi;
use Carbon\Carbon;

class GenerateSalaryReports extends Command
{
    protected $signature = 'salary:generate {tahun?} {bulan?}';
    protected $description = 'Generate salary reports from attendance data';

    public function handle()
    {
        $tahun = $this->argument('tahun') ?? Carbon::now()->year;
        $bulan = $this->argument('bulan') ?? Carbon::now()->month;
        
        $this->info("Generating salary reports for {$tahun}-{$bulan}...");
        
        // Clear existing reports for this period
        $deleted = SalaryReport::where('tahun', $tahun)->where('bulan', $bulan)->delete();
        $this->info("Deleted {$deleted} existing reports");
        
        // Get all employees
        $employees = Employee::all();
        $this->info("Found {$employees->count()} employees");
        
        $generated = 0;
        foreach ($employees as $employee) {
            $this->generateSalaryReport($employee, $tahun, $bulan);
            $generated++;
        }
        
        $this->info("Generated {$generated} salary reports successfully!");
    }
    
    private function generateSalaryReport($employee, $tahun, $bulan)
    {
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = Carbon::create($tahun, $bulan)->endOfMonth();
        
        // Get attendance data
        $attendances = Absensi::where('employee_id', $employee->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();
            
        $jmlHariKerja = $attendances->where('status', 'full')->count() + 
                       ($attendances->where('status', 'setengah_hari')->count() * 0.5);
        
        // Calculate salary - PRODUCTION FIX: Gunakan gaji dari absensi
        $gajiPokok = 0;
        $totalGaji = 0;
        
        if ($attendances->isNotEmpty()) {
            // Ambil gaji dari absensi terbaru
            $latestAttendance = $attendances->sortByDesc('tanggal')->first();
            $gajiPokok = $latestAttendance->gaji_hari_itu ?? 0;
            $totalGaji = $gajiPokok * $jmlHariKerja;
        }
        
        // Get related entities
        $lokasi = null;
        $kandang = null;
        $pembibitan = null;
        
        if ($employee->kandang) {
            $kandang = $employee->kandang;
            $lokasi = $employee->kandang->lokasi;
        }
        
        // Get pembibitan from recent attendance
        $recentAttendance = $attendances->sortByDesc('tanggal')->first();
        if ($recentAttendance && $recentAttendance->pembibitan_id) {
            $pembibitan = \App\Models\Pembibitan::find($recentAttendance->pembibitan_id);
        }
        
        // Create salary report
        SalaryReport::create([
            'employee_id' => $employee->id,
            'lokasi_id' => $lokasi?->id,
            'kandang_id' => $kandang?->id,
            'pembibitan_id' => $pembibitan?->id,
            'nama_karyawan' => $employee->nama,
            'tipe_karyawan' => $employee->jabatan,
            'gaji_pokok' => $gajiPokok,
            'jml_hari_kerja' => $jmlHariKerja,
            'total_gaji' => $totalGaji,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }
}
