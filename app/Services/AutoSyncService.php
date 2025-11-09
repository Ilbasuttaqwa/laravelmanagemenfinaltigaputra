<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutoSyncService
{
    /**
     * Auto-sync salary reports logic
     * This method will be called automatically when needed
     */
    public static function syncSalaryReports()
    {
        try {
            // Check if we need to sync
            $lastSync = Cache::get('salary_reports_last_sync', 0);
            $currentTime = time();
            
            // Only sync if it's been more than 5 minutes
            if (($currentTime - $lastSync) < 300) {
                return;
            }
            
            // Generate salary reports for current month
            $tahun = date('Y');
            $bulan = date('n');
            
            // Check if reports exist for current month
            $existingReports = \App\Models\SalaryReport::where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->count();
            
            // If no reports exist, generate them
            if ($existingReports == 0) {
                self::generateSalaryReports($tahun, $bulan);
                Log::info("AutoSync: Generated {$existingReports} salary reports for {$tahun}-{$bulan}");
            }
            
            // Update sync timestamp
            Cache::put('salary_reports_last_sync', $currentTime, 3600);
            
            Log::info('AutoSync: Salary reports synced successfully');
            
        } catch (\Exception $e) {
            Log::error('AutoSync: Failed to sync salary reports: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate salary reports for specific month
     */
    private static function generateSalaryReports($tahun, $bulan)
    {
        try {
            // Clear existing reports for this period
            \App\Models\SalaryReport::where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->delete();
            
            // Generate reports for karyawan kandang (Employee table)
            $employees = \App\Models\Employee::all();
            foreach ($employees as $employee) {
                self::createSalaryReportForEmployee($employee, $tahun, $bulan);
            }
            
            // Generate reports for karyawan gudang (Gudang table)
            $gudangs = \App\Models\Gudang::all();
            foreach ($gudangs as $gudang) {
                self::createSalaryReportForGudang($gudang, $tahun, $bulan);
            }
            
            // Generate reports for mandor (Mandor table)
            $mandors = \App\Models\Mandor::all();
            foreach ($mandors as $mandor) {
                self::createSalaryReportForMandor($mandor, $tahun, $bulan);
            }
            
        } catch (\Exception $e) {
            Log::error('AutoSync: Failed to generate salary reports: ' . $e->getMessage());
        }
    }
    
    /**
     * Create salary report for employee
     */
    private static function createSalaryReportForEmployee($employee, $tahun, $bulan)
    {
        $startDate = \Carbon\Carbon::create($tahun, $bulan, 1);
        $endDate = \Carbon\Carbon::create($tahun, $bulan)->endOfMonth();
        
        $attendances = \App\Models\Absensi::where('employee_id', $employee->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $jmlHariKerja = $attendances->where('status', 'full')->count() +
                       ($attendances->where('status', 'setengah_hari')->count() * 0.5);

        // PERBAIKAN: Gunakan gaji_hari_itu yang tersimpan, bukan recalculate dari gaji sekarang
        // Ini penting untuk akurasi saat ada perubahan gaji di pertengahan bulan
        $totalGaji = $attendances->sum('gaji_hari_itu');

        // Ambil gaji pokok dari attendance record terakhir (lebih akurat)
        $gajiPokokBulanan = $attendances->first()?->gaji_pokok_saat_itu ?? $employee->gaji_pokok;
        
        // Get pembibitan from recent attendance
        $pembibitan = null;
        $lokasi = null;
        $kandang = null;
        
        $recentAttendance = $attendances->sortByDesc('tanggal')->first();
        if ($recentAttendance && $recentAttendance->pembibitan_id) {
            $pembibitan = \App\Models\Pembibitan::find($recentAttendance->pembibitan_id);
            if ($pembibitan) {
                $lokasi = $pembibitan->lokasi;
                $kandang = $pembibitan->kandang;
            }
        }
        
        if (!$lokasi && $employee->kandang && $employee->kandang->lokasi) {
            $lokasi = $employee->kandang->lokasi;
        }
        if (!$kandang && $employee->kandang) {
            $kandang = $employee->kandang;
        }
        
        \App\Models\SalaryReport::create([
            'employee_id' => $employee->id,
            'lokasi_id' => $lokasi?->id,
            'kandang_id' => $kandang?->id,
            'pembibitan_id' => $pembibitan?->id,
            'nama_karyawan' => $employee->nama,
            'tipe_karyawan' => $employee->jabatan,
            'gaji_pokok' => $totalGaji,
            'gaji_pokok_bulanan' => $gajiPokokBulanan,
            'jml_hari_kerja' => $jmlHariKerja,
            'total_gaji' => $totalGaji,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }
    
    /**
     * Create salary report for gudang
     */
    private static function createSalaryReportForGudang($gudang, $tahun, $bulan)
    {
        $startDate = \Carbon\Carbon::create($tahun, $bulan, 1);
        $endDate = \Carbon\Carbon::create($tahun, $bulan)->endOfMonth();
        
        $attendances = \App\Models\Absensi::where('nama_karyawan', $gudang->nama)
            ->where('jabatan', 'karyawan_gudang')  // Tambah filter jabatan untuk akurasi
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $jmlHariKerja = $attendances->where('status', 'full')->count() +
                       ($attendances->where('status', 'setengah_hari')->count() * 0.5);

        // PERBAIKAN: Gunakan gaji_hari_itu yang tersimpan, bukan recalculate dari gaji sekarang
        $totalGaji = $attendances->sum('gaji_hari_itu');

        // Ambil gaji pokok dari attendance record terakhir (lebih akurat)
        $gajiPokokBulanan = $attendances->first()?->gaji_pokok_saat_itu ?? $gudang->gaji;
        
        // Get pembibitan from recent attendance
        $pembibitan = null;
        $lokasi = null;
        $kandang = null;
        
        $recentAttendance = $attendances->sortByDesc('tanggal')->first();
        if ($recentAttendance && $recentAttendance->pembibitan_id) {
            $pembibitan = \App\Models\Pembibitan::find($recentAttendance->pembibitan_id);
            if ($pembibitan) {
                $lokasi = $pembibitan->lokasi;
                $kandang = $pembibitan->kandang;
            }
        }
        
        \App\Models\SalaryReport::create([
            'employee_id' => null,
            'lokasi_id' => $lokasi?->id,
            'kandang_id' => $kandang?->id,
            'pembibitan_id' => $pembibitan?->id,
            'nama_karyawan' => $gudang->nama,
            'tipe_karyawan' => 'karyawan_gudang',
            'gaji_pokok' => $totalGaji,
            'gaji_pokok_bulanan' => $gajiPokokBulanan,
            'jml_hari_kerja' => $jmlHariKerja,
            'total_gaji' => $totalGaji,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }
    
    /**
     * Create salary report for mandor
     */
    private static function createSalaryReportForMandor($mandor, $tahun, $bulan)
    {
        $startDate = \Carbon\Carbon::create($tahun, $bulan, 1);
        $endDate = \Carbon\Carbon::create($tahun, $bulan)->endOfMonth();
        
        $attendances = \App\Models\Absensi::where('nama_karyawan', $mandor->nama)
            ->where('jabatan', 'mandor')  // Tambah filter jabatan untuk akurasi
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $jmlHariKerja = $attendances->where('status', 'full')->count() +
                       ($attendances->where('status', 'setengah_hari')->count() * 0.5);

        // PERBAIKAN: Gunakan gaji_hari_itu yang tersimpan, bukan recalculate dari gaji sekarang
        $totalGaji = $attendances->sum('gaji_hari_itu');

        // Ambil gaji pokok dari attendance record terakhir (lebih akurat)
        $gajiPokokBulanan = $attendances->first()?->gaji_pokok_saat_itu ?? $mandor->gaji;
        
        // Get pembibitan from recent attendance
        $pembibitan = null;
        $lokasi = null;
        $kandang = null;
        
        $recentAttendance = $attendances->sortByDesc('tanggal')->first();
        if ($recentAttendance && $recentAttendance->pembibitan_id) {
            $pembibitan = \App\Models\Pembibitan::find($recentAttendance->pembibitan_id);
            if ($pembibitan) {
                $lokasi = $pembibitan->lokasi;
                $kandang = $pembibitan->kandang;
            }
        }
        
        \App\Models\SalaryReport::create([
            'employee_id' => null,
            'lokasi_id' => $lokasi?->id,
            'kandang_id' => $kandang?->id,
            'pembibitan_id' => $pembibitan?->id,
            'nama_karyawan' => $mandor->nama,
            'tipe_karyawan' => 'mandor',
            'gaji_pokok' => $totalGaji,
            'gaji_pokok_bulanan' => $gajiPokokBulanan,
            'jml_hari_kerja' => $jmlHariKerja,
            'total_gaji' => $totalGaji,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }
    
    /**
     * Force generate salary reports for specific month
     */
    public static function forceGenerateSalaryReports($tahun, $bulan)
    {
        try {
            self::generateSalaryReports($tahun, $bulan);
            Log::info("AutoSync: Force generated salary reports for {$tahun}-{$bulan}");
            return true;
        } catch (\Exception $e) {
            Log::error('AutoSync: Failed to force generate salary reports: ' . $e->getMessage());
            return false;
        }
    }
    
    
    /**
     * Auto-clear cache when needed
     */
    public static function clearCacheIfNeeded()
    {
        try {
            $cacheKey = 'last_cache_clear';
            $lastClear = Cache::get($cacheKey, 0);
            $currentTime = time();
            
            // Clear cache if it's been more than 2 hours
            if (($currentTime - $lastClear) > 7200) {
                self::clearAllCaches();
                Cache::put($cacheKey, $currentTime, 7200);
            }
            
        } catch (\Exception $e) {
            Log::error('AutoSync: Failed to clear cache: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear all relevant caches
     */
    private static function clearAllCaches()
    {
        try {
            // Clear config cache
            if (file_exists(base_path('bootstrap/cache/config.php'))) {
                unlink(base_path('bootstrap/cache/config.php'));
            }
            
            // Clear route cache
            if (file_exists(base_path('bootstrap/cache/routes-v7.php'))) {
                unlink(base_path('bootstrap/cache/routes-v7.php'));
            }
            
            // Clear view cache
            $viewCacheDir = storage_path('framework/views');
            if (is_dir($viewCacheDir)) {
                $files = glob($viewCacheDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
            
            Log::info('AutoSync: All caches cleared');
            
        } catch (\Exception $e) {
            Log::error('AutoSync: Failed to clear caches: ' . $e->getMessage());
        }
    }
}
