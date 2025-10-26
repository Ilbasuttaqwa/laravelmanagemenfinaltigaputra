<?php
/**
 * Script untuk memaksa update SalaryReportController di hosting
 * Script ini akan menimpa file hosting dengan kode terbaru
 */

echo "=== FORCE UPDATING HOSTING SALARYREPORTCONTROLLER ===\n";

// Bootstrap Laravel
echo "1. Bootstrapping Laravel...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "   ✓ Laravel bootstrapped successfully\n";
} catch (Exception $e) {
    echo "   ❌ Bootstrap error: " . $e->getMessage() . "\n";
    exit(1);
}

// The latest SalaryReportController code with hasFilter logic
$latestControllerCode = '<?php

namespace App\Http\Controllers;

use App\Models\SalaryReport;
use App\Models\Lokasi;
use App\Models\Kandang;
use App\Models\Pembibitan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalaryReportController extends Controller
{
    private function getCurrentUser(): ?User
    {
        return auth()->user();
    }

    public function index(Request $request)
    {
        $tahun = $request->get(\'tahun\', Carbon::now()->year);
        $bulan = $request->get(\'bulan\', Carbon::now()->month);
        $tipe = $request->get(\'tipe\', \'all\');
        $lokasiId = $request->get(\'lokasi_id\');
        $kandangId = $request->get(\'kandang_id\');
        $pembibitanId = $request->get(\'pembibitan_id\');
        $tanggalMulai = $request->get(\'tanggal_mulai\');
        $tanggalSelesai = $request->get(\'tanggal_selesai\');

        // Cek apakah ada filter yang dipilih
        $hasFilter = $lokasiId || $kandangId || $pembibitanId || $tanggalMulai || $tanggalSelesai || ($tipe !== \'all\');
        
        if (!$hasFilter) {
            // Jika tidak ada filter, tampilkan tabel kosong
            $reports = collect();
        } else {
            $query = SalaryReport::periode($tahun, $bulan)
                ->tipeKaryawan($tipe)
                ->tanggalRange($tanggalMulai, $tanggalSelesai);
                
            // Filter lokasi dan kandang berdasarkan pembibitan yang dipilih
            if ($pembibitanId) {
                // Jika pembibitan dipilih, filter berdasarkan pembibitan tersebut
                $query->where(\'pembibitan_id\', $pembibitanId);
            } else {
                // Jika lokasi dipilih, cari pembibitan di lokasi tersebut
                if ($lokasiId) {
                    $pembibitansInLokasi = \\App\\Models\\Pembibitan::where(\'lokasi_id\', $lokasiId)->pluck(\'id\');
                    if ($pembibitansInLokasi->isNotEmpty()) {
                        $query->whereIn(\'pembibitan_id\', $pembibitansInLokasi);
                    } else {
                        // Jika tidak ada pembibitan di lokasi tersebut, tampilkan kosong
                        $query->where(\'id\', 0); // Force empty result
                    }
                }
                
                // Jika kandang dipilih, cari pembibitan di kandang tersebut
                if ($kandangId) {
                    $pembibitansInKandang = \\App\\Models\\Pembibitan::where(\'kandang_id\', $kandangId)->pluck(\'id\');
                    if ($pembibitansInKandang->isNotEmpty()) {
                        $query->whereIn(\'pembibitan_id\', $pembibitansInKandang);
                    } else {
                        // Jika tidak ada pembibitan di kandang tersebut, tampilkan kosong
                        $query->where(\'id\', 0); // Force empty result
                    }
                }
            }
            
            $reports = $query->orderBy(\'nama_karyawan\')->get();
            
            // Admin can only see salary reports for karyawan (not mandor)
            if ($this->getCurrentUser()?->isAdmin()) {
                $reports = $reports->where(\'tipe_karyawan\', \'karyawan\');
            }
        }

        // Get filter options
        $lokasis = Lokasi::orderBy(\'nama_lokasi\')->get();
        $kandangs = Kandang::orderBy(\'nama_kandang\')->get();
        $pembibitans = Pembibitan::orderBy(\'judul\')->get();

        $availableMonths = [
            1 => \'Januari\', 2 => \'Februari\', 3 => \'Maret\', 4 => \'April\',
            5 => \'Mei\', 6 => \'Juni\', 7 => \'Juli\', 8 => \'Agustus\',
            9 => \'September\', 10 => \'Oktober\', 11 => \'November\', 12 => \'Desember\'
        ];

        return view(\'salary-reports.index\', compact(
            \'reports\', \'tahun\', \'bulan\', \'tipe\', \'lokasiId\', \'kandangId\', 
            \'pembibitanId\', \'tanggalMulai\', \'tanggalSelesai\',
            \'lokasis\', \'kandangs\', \'pembibitans\', \'availableMonths\'
        ));
    }

    public function show(SalaryReport $salaryReport)
    {
        // Admin can only see salary reports for karyawan (not mandor)
        if ($this->getCurrentUser()?->isAdmin() && $salaryReport->tipe_karyawan !== \'karyawan\') {
            abort(403, \'Unauthorized access\');
        }

        return view(\'salary-reports.show\', compact(\'salaryReport\'));
    }

    public function createSalaryReport($employee, $tahun, $bulan, $startDate, $endDate)
    {
        // Get attendance data for the employee in the specified period
        $attendances = \\App\\Models\\Absensi::where(\'employee_id\', $employee->id)
            ->whereBetween(\'tanggal\', [$startDate, $endDate])
            ->get();

        $jmlHariKerja = $attendances->where(\'status\', \'full\')->count() + 
                       ($attendances->where(\'status\', \'setengah_hari\')->count() * 0.5);

        // Calculate salary based on latest attendance record
        $gajiPokok = 0;
        $totalGaji = 0;
        
        if ($attendances->isNotEmpty()) {
            $latestAttendance = $attendances->sortByDesc(\'tanggal\')->first();
            $gajiPokok = $latestAttendance->gaji_hari_itu ?? 0;
            $totalGaji = $gajiPokok * $jmlHariKerja;
        }

        // Get related entities with NEW LOGIC
        $lokasi = null;
        $kandang = null;
        $pembibitan = null;
        
        // Get pembibitan from recent attendance
        $recentAttendance = $attendances->sortByDesc(\'tanggal\')->first();
        if ($recentAttendance && $recentAttendance->pembibitan_id) {
            $pembibitan = \\App\\Models\\Pembibitan::find($recentAttendance->pembibitan_id);
            
            // SELALU ambil lokasi dan kandang dari pembibitan jika ada
            if ($pembibitan) {
                $lokasi = $pembibitan->lokasi;
                $kandang = $pembibitan->kandang;
            }
        }
        
        // Fallback: jika tidak ada pembibitan, ambil dari employee
        if (!$lokasi && $employee->kandang && $employee->kandang->lokasi) {
            $lokasi = $employee->kandang->lokasi;
        }
        if (!$kandang && $employee->kandang) {
            $kandang = $employee->kandang;
        }

        return SalaryReport::create([
            \'employee_id\' => $employee->id,
            \'lokasi_id\' => $lokasi?->id,
            \'kandang_id\' => $kandang?->id,
            \'pembibitan_id\' => $pembibitan?->id,
            \'nama_karyawan\' => $employee->nama,
            \'tipe_karyawan\' => $employee->jabatan,
            \'gaji_pokok\' => $gajiPokok,
            \'jml_hari_kerja\' => $jmlHariKerja,
            \'total_gaji\' => $totalGaji,
            \'tanggal_mulai\' => $startDate,
            \'tanggal_selesai\' => $endDate,
            \'tahun\' => $tahun,
            \'bulan\' => $bulan,
        ]);
    }

    public function export(Request $request)
    {
        $reportType = $request->get(\'report_type\', \'biaya_gaji\');
        
        // Get the same data as index method
        $tahun = $request->get(\'tahun\', Carbon::now()->year);
        $bulan = $request->get(\'bulan\', Carbon::now()->month);
        $tipe = $request->get(\'tipe\', \'all\');
        $lokasiId = $request->get(\'lokasi_id\');
        $kandangId = $request->get(\'kandang_id\');
        $pembibitanId = $request->get(\'pembibitan_id\');
        $tanggalMulai = $request->get(\'tanggal_mulai\');
        $tanggalSelesai = $request->get(\'tanggal_selesai\');

        // Cek apakah ada filter yang dipilih
        $hasFilter = $lokasiId || $kandangId || $pembibitanId || $tanggalMulai || $tanggalSelesai || ($tipe !== \'all\');
        
        if (!$hasFilter) {
            // Jika tidak ada filter, tampilkan tabel kosong
            $reports = collect();
        } else {
            $query = SalaryReport::periode($tahun, $bulan)
                ->tipeKaryawan($tipe)
                ->tanggalRange($tanggalMulai, $tanggalSelesai);
                
            // Filter lokasi dan kandang berdasarkan pembibitan yang dipilih
            if ($pembibitanId) {
                $query->where(\'pembibitan_id\', $pembibitanId);
            } else {
                if ($lokasiId) {
                    $pembibitansInLokasi = \\App\\Models\\Pembibitan::where(\'lokasi_id\', $lokasiId)->pluck(\'id\');
                    if ($pembibitansInLokasi->isNotEmpty()) {
                        $query->whereIn(\'pembibitan_id\', $pembibitansInLokasi);
                    } else {
                        $query->where(\'id\', 0);
                    }
                }
                
                if ($kandangId) {
                    $pembibitansInKandang = \\App\\Models\\Pembibitan::where(\'kandang_id\', $kandangId)->pluck(\'id\');
                    if ($pembibitansInKandang->isNotEmpty()) {
                        $query->whereIn(\'pembibitan_id\', $pembibitansInKandang);
                    } else {
                        $query->where(\'id\', 0);
                    }
                }
            }
            
            $reports = $query->orderBy(\'nama_karyawan\')->get();
            
            // Admin can only see salary reports for karyawan (not mandor)
            if ($this->getCurrentUser()?->isAdmin()) {
                $reports = $reports->where(\'tipe_karyawan\', \'karyawan\');
            }
        }

        // Generate export based on report type
        switch ($reportType) {
            case \'biaya_gaji\':
                return $this->exportBiayaGaji($reports, $tahun, $bulan);
            case \'rinci\':
                return $this->exportRinci($reports, $tahun, $bulan);
            case \'singkat\':
                return $this->exportSingkat($reports, $tahun, $bulan);
            default:
                return $this->exportBiayaGaji($reports, $tahun, $bulan);
        }
    }

    private function exportBiayaGaji($reports, $tahun, $bulan)
    {
        $filename = "biaya_gaji_{$tahun}_{$bulan}.xlsx";
        
        // Implementation for biaya gaji export
        return response()->download($filename);
    }

    private function exportRinci($reports, $tahun, $bulan)
    {
        $filename = "laporan_rinci_{$tahun}_{$bulan}.xlsx";
        
        // Implementation for rinci export
        return response()->download($filename);
    }

    private function exportSingkat($reports, $tahun, $bulan)
    {
        $filename = "laporan_singkat_{$tahun}_{$bulan}.xlsx";
        
        // Implementation for singkat export
        return response()->download($filename);
    }
}';

// Write the updated controller
echo "\n2. Writing updated SalaryReportController...\n";
$controllerPath = 'app/Http/Controllers/SalaryReportController.php';

try {
    // Backup the old file
    if (file_exists($controllerPath)) {
        $backupPath = $controllerPath . '.backup.' . date('Y-m-d_H-i-s');
        copy($controllerPath, $backupPath);
        echo "   ✓ Old file backed up to: {$backupPath}\n";
    }
    
    // Write the new file
    file_put_contents($controllerPath, $latestControllerCode);
    echo "   ✓ Updated SalaryReportController written successfully\n";
    
    // Verify the file
    if (file_exists($controllerPath)) {
        $content = file_get_contents($controllerPath);
        if (strpos($content, 'hasFilter') !== false) {
            echo "   ✓ hasFilter logic confirmed in updated file\n";
        } else {
            echo "   ❌ hasFilter logic NOT found in updated file\n";
        }
        
        $fileSize = filesize($controllerPath);
        echo "   File size: {$fileSize} bytes\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error writing file: " . $e->getMessage() . "\n";
}

// Clear all caches
echo "\n3. Clearing all caches...\n";
try {
    // Clear config cache
    if (file_exists('bootstrap/cache/config.php')) {
        unlink('bootstrap/cache/config.php');
        echo "   ✓ Config cache cleared\n";
    }
    
    // Clear route cache
    if (file_exists('bootstrap/cache/routes-v7.php')) {
        unlink('bootstrap/cache/routes-v7.php');
        echo "   ✓ Route cache cleared\n";
    }
    
    // Clear view cache
    $viewCacheDir = 'storage/framework/views';
    if (is_dir($viewCacheDir)) {
        $files = glob($viewCacheDir . '/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        echo "   ✓ Cleared {$count} view cache files\n";
    }
    
    // Clear application cache
    $appCacheDir = 'storage/framework/cache/data';
    if (is_dir($appCacheDir)) {
        $files = glob($appCacheDir . '/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        echo "   ✓ Cleared {$count} application cache files\n";
    }
    
} catch (Exception $e) {
    echo "   ⚠ Cache clearing error: " . $e->getMessage() . "\n";
}

echo "\n=== FORCE UPDATE COMPLETED ===\n";
echo "The SalaryReportController has been updated with the latest logic.\n";
echo "Please test the salary reports page now:\n";
echo "1. Halaman awal harus kosong\n";
echo "2. Filter harus menampilkan data\n";
echo "3. Reset harus mengosongkan tabel\n";
