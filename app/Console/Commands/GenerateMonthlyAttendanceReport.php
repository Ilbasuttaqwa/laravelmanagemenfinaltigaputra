<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use App\Models\Gudang;
use App\Models\Mandor;
use App\Models\MonthlyAttendanceReport;
use Carbon\Carbon;

class GenerateMonthlyAttendanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:generate-monthly-report {tahun?} {bulan?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly attendance report for all employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tahun = $this->argument('tahun') ?? Carbon::now()->year;
        $bulan = $this->argument('bulan') ?? Carbon::now()->month;

        $this->info("Generating monthly attendance report for {$tahun}-{$bulan}...");

        // Get all employees (gudang and mandor)
        $gudangs = Gudang::all();
        $mandors = Mandor::all();

        $totalProcessed = 0;

        // Process gudang employees
        foreach ($gudangs as $gudang) {
            $this->generateReportForEmployee($gudang, 'gudang', $tahun, $bulan);
            $totalProcessed++;
        }

        // Process mandor employees
        foreach ($mandors as $mandor) {
            $this->generateReportForEmployee($mandor, 'mandor', $tahun, $bulan);
            $totalProcessed++;
        }

        $this->info("Successfully generated reports for {$totalProcessed} employees.");
    }

    private function generateReportForEmployee($employee, $tipe, $tahun, $bulan)
    {
        // Check if report already exists
        $existingReport = MonthlyAttendanceReport::where('tipe_karyawan', $tipe)
            ->where('karyawan_id', $employee->id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        if ($existingReport) {
            $this->warn("Report already exists for {$employee->nama} ({$tipe}) - {$tahun}-{$bulan}");
            return;
        }

        // Get attendance data for the month
        $absensis = Absensi::where($tipe . '_id', $employee->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        // Prepare attendance data
        $dataAbsensi = [];
        $totalHari = 0;
        $totalFull = 0;
        $totalSetengah = 0;
        $totalAbsen = 0;

        // Get all days in the month
        $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $tanggal = Carbon::create($tahun, $bulan, $day);
            $tanggalStr = $tanggal->format('Y-m-d');
            
            // Find attendance for this date
            $absensi = $absensis->where('tanggal', $tanggalStr)->first();
            
            if ($absensi) {
                $dataAbsensi[$tanggalStr] = $absensi->status;
                $totalHari++;
                
                if ($absensi->status === 'full') {
                    $totalFull++;
                } elseif ($absensi->status === 'setengah_hari') {
                    $totalSetengah++;
                }
            } else {
                // Check if it's a working day (Monday to Friday)
                if ($tanggal->isWeekday()) {
                    $dataAbsensi[$tanggalStr] = 'absen';
                    $totalHari++;
                    $totalAbsen++;
                }
            }
        }

        // Calculate attendance percentage
        $persentaseKehadiran = $totalHari > 0 ? (($totalFull + $totalSetengah) / $totalHari) * 100 : 0;

        // Create monthly report
        MonthlyAttendanceReport::create([
            'nama_karyawan' => $employee->nama,
            'tipe_karyawan' => $tipe,
            'karyawan_id' => $employee->id,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'data_absensi' => $dataAbsensi,
            'total_hari_kerja' => $totalHari,
            'total_hari_full' => $totalFull,
            'total_hari_setengah' => $totalSetengah,
            'total_hari_absen' => $totalAbsen,
            'persentase_kehadiran' => $persentaseKehadiran,
        ]);

        $this->line("Generated report for {$employee->nama} ({$tipe}) - {$totalHari} working days, {$persentaseKehadiran}% attendance");
    }
}