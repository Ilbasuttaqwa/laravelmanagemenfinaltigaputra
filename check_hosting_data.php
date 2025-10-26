<?php
/**
 * Script untuk check data di hosting
 * Jalankan script ini untuk melihat kondisi data saat ini
 */

echo "=== CHECKING HOSTING DATA ===\n";

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

// Check employees
echo "\n2. Checking employees...\n";
try {
    $employees = \App\Models\Employee::all();
    echo "   Total employees: " . $employees->count() . "\n";
    
    foreach ($employees as $employee) {
        $kandangName = $employee->kandang ? $employee->kandang->nama_kandang : 'NULL';
        $lokasiName = $employee->kandang && $employee->kandang->lokasi ? $employee->kandang->lokasi->nama_lokasi : 'NULL';
        echo "   - {$employee->nama} ({$employee->jabatan}): Kandang={$kandangName}, Lokasi={$lokasiName}\n";
    }
} catch (Exception $e) {
    echo "   ⚠ Error checking employees: " . $e->getMessage() . "\n";
}

// Check pembibitans
echo "\n3. Checking pembibitans...\n";
try {
    $pembibitans = \App\Models\Pembibitan::all();
    echo "   Total pembibitans: " . $pembibitans->count() . "\n";
    
    foreach ($pembibitans as $pembibitan) {
        $lokasiName = $pembibitan->lokasi ? $pembibitan->lokasi->nama_lokasi : 'NULL';
        $kandangName = $pembibitan->kandang ? $pembibitan->kandang->nama_kandang : 'NULL';
        echo "   - {$pembibitan->judul}: Lokasi={$lokasiName}, Kandang={$kandangName}\n";
    }
} catch (Exception $e) {
    echo "   ⚠ Error checking pembibitans: " . $e->getMessage() . "\n";
}

// Check absensis
echo "\n4. Checking absensis...\n";
try {
    $absensis = \App\Models\Absensi::whereBetween('tanggal', ['2025-10-01', '2025-10-31'])->get();
    echo "   Total absensis in October 2025: " . $absensis->count() . "\n";
    
    foreach ($absensis as $absensi) {
        $employeeName = $absensi->employee ? $absensi->employee->nama : 'NULL';
        $pembibitanName = $absensi->pembibitan ? $absensi->pembibitan->judul : 'NULL';
        $kandangName = $absensi->kandang ? $absensi->kandang->nama_kandang : 'NULL';
        echo "   - {$employeeName} on {$absensi->tanggal}: Pembibitan={$pembibitanName}, Kandang={$kandangName}\n";
    }
} catch (Exception $e) {
    echo "   ⚠ Error checking absensis: " . $e->getMessage() . "\n";
}

// Check salary reports
echo "\n5. Checking salary reports...\n";
try {
    $reports = \App\Models\SalaryReport::where('tahun', 2025)->where('bulan', 10)->get();
    echo "   Total salary reports for October 2025: " . $reports->count() . "\n";
    
    foreach ($reports as $report) {
        $lokasiName = $report->lokasi ? $report->lokasi->nama_lokasi : 'NULL';
        $kandangName = $report->kandang ? $report->kandang->nama_kandang : 'NULL';
        $pembibitanName = $report->pembibitan ? $report->pembibitan->judul : 'NULL';
        echo "   - {$report->nama_karyawan}: Lokasi={$lokasiName}, Kandang={$kandangName}, Pembibitan={$pembibitanName}\n";
    }
} catch (Exception $e) {
    echo "   ⚠ Error checking salary reports: " . $e->getMessage() . "\n";
}

echo "\n=== DATA CHECK COMPLETED ===\n";