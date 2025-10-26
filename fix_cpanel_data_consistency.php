<?php
/**
 * Script untuk memperbaiki konsistensi data di cPanel
 * Jalankan script ini untuk memperbaiki masalah "Karyawan Tidak Ditemukan"
 */

echo "🔧 MEMPERBAIKI KONSISTENSI DATA...\n";

// Update all absensi records with correct employee data
echo "👥 Updating employee data in absensi records...\n";

try {
    $absensis = \App\Models\Absensi::all();
    $updated = 0;
    
    foreach ($absensis as $absensi) {
        $updatedRecord = false;
        
        // If employee_id exists, get fresh employee data
        if ($absensi->employee_id) {
            $employee = \App\Models\Employee::find($absensi->employee_id);
            if ($employee) {
                $absensi->nama_karyawan = $employee->nama;
                $updatedRecord = true;
            }
        }
        
        // Update lokasi_kerja based on employee
        if ($absensi->employee_id) {
            $employee = \App\Models\Employee::with('lokasi')->find($absensi->employee_id);
            if ($employee && $employee->lokasi) {
                $absensi->lokasi_kerja = $employee->lokasi->nama_lokasi;
                $updatedRecord = true;
            }
        }
        
        if ($updatedRecord) {
            $absensi->save();
            $updated++;
        }
    }
    
    echo "✅ Updated {$updated} absensi records\n";
    
} catch (Exception $e) {
    echo "❌ Error updating absensi records: " . $e->getMessage() . "\n";
}

// Clear all caches after data update
echo "🧹 Clearing caches after data update...\n";
try {
    \Cache::flush();
    \Cache::forget('lokasis_data');
    \Cache::forget('kandangs_data');
    \Cache::forget('pembibitans_data');
    \Cache::forget('gudangs_data');
    \Cache::forget('employees_data');
    \Cache::forget('absensis_data');
    echo "✅ All caches cleared\n";
} catch (Exception $e) {
    echo "❌ Cache clearing error: " . $e->getMessage() . "\n";
}

// Verify data consistency
echo "🔍 Verifying data consistency...\n";
try {
    $totalAbsensi = \App\Models\Absensi::count();
    $absensiWithEmployee = \App\Models\Absensi::whereNotNull('employee_id')->count();
    $absensiWithName = \App\Models\Absensi::whereNotNull('nama_karyawan')->count();
    
    echo "📊 Data Statistics:\n";
    echo "   Total Absensi: {$totalAbsensi}\n";
    echo "   With Employee ID: {$absensiWithEmployee}\n";
    echo "   With Employee Name: {$absensiWithName}\n";
    
    if ($absensiWithName == $totalAbsensi) {
        echo "✅ All absensi records have employee names\n";
    } else {
        echo "⚠️ Some absensi records missing employee names\n";
    }
    
} catch (Exception $e) {
    echo "❌ Verification error: " . $e->getMessage() . "\n";
}

echo "\n🎉 DATA CONSISTENCY FIX COMPLETED!\n";
echo "📋 Next steps:\n";
echo "1. Test absensi table display\n";
echo "2. Verify employee names are showing\n";
echo "3. Check lokasi_kerja consistency\n";
echo "4. Test real-time data updates\n";
?>
