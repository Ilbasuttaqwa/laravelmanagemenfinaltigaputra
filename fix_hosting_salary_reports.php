<?php
/**
 * Script untuk memperbaiki masalah laporan gaji di hosting
 * Jalankan script ini di hosting untuk:
 * 1. Clear semua cache
 * 2. Regenerate salary reports dengan logika terbaru
 * 3. Fix data lokasi dan kandang yang kosong
 */

echo "=== FIXING HOSTING SALARY REPORTS ===\n";

// 1. Clear semua cache
echo "1. Clearing all caches...\n";
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
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "   ✓ View cache cleared\n";
    }
    
    // Clear application cache
    $appCacheDir = 'storage/framework/cache';
    if (is_dir($appCacheDir)) {
        $files = glob($appCacheDir . '/data/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "   ✓ Application cache cleared\n";
    }
    
} catch (Exception $e) {
    echo "   ⚠ Cache clearing error: " . $e->getMessage() . "\n";
}

// 2. Bootstrap Laravel
echo "\n2. Bootstrapping Laravel...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "   ✓ Laravel bootstrapped successfully\n";
} catch (Exception $e) {
    echo "   ❌ Bootstrap error: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Check current salary reports
echo "\n3. Checking current salary reports...\n";
try {
    $currentReports = \App\Models\SalaryReport::where('tahun', 2025)->where('bulan', 10)->get();
    echo "   Current reports count: " . $currentReports->count() . "\n";
    
    foreach ($currentReports as $report) {
        echo "   - {$report->nama_karyawan}: Lokasi=" . ($report->lokasi_id ?: 'NULL') . ", Kandang=" . ($report->kandang_id ?: 'NULL') . ", Pembibitan=" . ($report->pembibitan_id ?: 'NULL') . "\n";
    }
} catch (Exception $e) {
    echo "   ⚠ Error checking reports: " . $e->getMessage() . "\n";
}

// 4. Delete existing salary reports
echo "\n4. Deleting existing salary reports...\n";
try {
    $deleted = \App\Models\SalaryReport::where('tahun', 2025)->where('bulan', 10)->delete();
    echo "   ✓ Deleted {$deleted} existing reports\n";
} catch (Exception $e) {
    echo "   ⚠ Error deleting reports: " . $e->getMessage() . "\n";
}

// 5. Regenerate salary reports with new logic
echo "\n5. Regenerating salary reports with new logic...\n";
try {
    // Get employees
    $employees = \App\Models\Employee::all();
    echo "   Found {$employees->count()} employees\n";
    
    $generated = 0;
    foreach ($employees as $employee) {
        // Simulate the new logic from GenerateSalaryReports command
        $tahun = 2025;
        $bulan = 10;
        $startDate = \Carbon\Carbon::create($tahun, $bulan, 1);
        $endDate = \Carbon\Carbon::create($tahun, $bulan)->endOfMonth();
        
        // Get attendance data
        $attendances = \App\Models\Absensi::where('employee_id', $employee->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();
            
        $jmlHariKerja = $attendances->where('status', 'full')->count() + 
                       ($attendances->where('status', 'setengah_hari')->count() * 0.5);
        
        // Calculate salary
        $gajiPokok = 0;
        $totalGaji = 0;
        
        if ($attendances->isNotEmpty()) {
            $latestAttendance = $attendances->sortByDesc('tanggal')->first();
            $gajiPokok = $latestAttendance->gaji_hari_itu ?? 0;
            $totalGaji = $gajiPokok * $jmlHariKerja;
        }
        
        // Get related entities with NEW LOGIC
        $lokasi = null;
        $kandang = null;
        $pembibitan = null;
        
        // Get pembibitan from recent attendance
        $recentAttendance = $attendances->sortByDesc('tanggal')->first();
        if ($recentAttendance && $recentAttendance->pembibitan_id) {
            $pembibitan = \App\Models\Pembibitan::find($recentAttendance->pembibitan_id);
            
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
        
        // Create salary report
        \App\Models\SalaryReport::create([
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
        
        $generated++;
        echo "   ✓ Generated report for {$employee->nama}: Lokasi=" . ($lokasi?->nama_lokasi ?: 'NULL') . ", Kandang=" . ($kandang?->nama_kandang ?: 'NULL') . ", Pembibitan=" . ($pembibitan?->judul ?: 'NULL') . "\n";
    }
    
    echo "   ✓ Generated {$generated} salary reports successfully!\n";
    
} catch (Exception $e) {
    echo "   ❌ Error generating reports: " . $e->getMessage() . "\n";
}

// 6. Verify results
echo "\n6. Verifying results...\n";
try {
    $newReports = \App\Models\SalaryReport::where('tahun', 2025)->where('bulan', 10)->get();
    echo "   New reports count: " . $newReports->count() . "\n";
    
    foreach ($newReports as $report) {
        $lokasiName = $report->lokasi ? $report->lokasi->nama_lokasi : 'NULL';
        $kandangName = $report->kandang ? $report->kandang->nama_kandang : 'NULL';
        $pembibitanName = $report->pembibitan ? $report->pembibitan->judul : 'NULL';
        
        echo "   - {$report->nama_karyawan}: Lokasi={$lokasiName}, Kandang={$kandangName}, Pembibitan={$pembibitanName}\n";
    }
} catch (Exception $e) {
    echo "   ⚠ Error verifying results: " . $e->getMessage() . "\n";
}

echo "\n=== FIX COMPLETED ===\n";
echo "Please test the salary reports page now:\n";
echo "1. Halaman awal harus kosong\n";
echo "2. Filter harus menampilkan data\n";
echo "3. Reset harus mengosongkan tabel\n";
echo "4. Lokasi dan kandang harus konsisten dengan pembibitan\n";
