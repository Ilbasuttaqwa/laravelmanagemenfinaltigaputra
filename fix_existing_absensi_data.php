<?php
/**
 * Script untuk memperbaiki data absensi yang sudah ada di cPanel
 * Memperbaiki lokasi_kerja berdasarkan pembibitan yang dipilih
 */

echo "🔧 MEMPERBAIKI DATA ABSENSI YANG SUDAH ADA...\n";

try {
    // Ambil semua data absensi
    $absensis = \App\Models\Absensi::with(['pembibitan.lokasi', 'employee.lokasi'])->get();
    $updated = 0;
    $errors = 0;
    
    echo "📊 Found {$absensis->count()} absensi records\n";
    
    foreach ($absensis as $absensi) {
        $originalLokasi = $absensi->lokasi_kerja;
        $newLokasi = null;
        
        // PRIORITAS 1: Ambil lokasi dari pembibitan yang dipilih
        if ($absensi->pembibitan_id && $absensi->pembibitan) {
            if ($absensi->pembibitan->lokasi) {
                $newLokasi = $absensi->pembibitan->lokasi->nama_lokasi;
                echo "✅ Absensi ID {$absensi->id}: Lokasi dari pembibitan '{$absensi->pembibitan->judul}' -> '{$newLokasi}'\n";
            }
        }
        
        // PRIORITAS 2: Jika tidak ada pembibitan, ambil dari employee
        if (!$newLokasi && $absensi->employee_id && $absensi->employee) {
            if ($absensi->employee->lokasi) {
                $newLokasi = $absensi->employee->lokasi->nama_lokasi;
                echo "✅ Absensi ID {$absensi->id}: Lokasi dari employee '{$absensi->employee->nama}' -> '{$newLokasi}'\n";
            }
        }
        
        // Update jika ada perubahan
        if ($newLokasi && $newLokasi !== $originalLokasi) {
            $absensi->lokasi_kerja = $newLokasi;
            $absensi->save();
            $updated++;
            echo "🔄 Updated: '{$originalLokasi}' -> '{$newLokasi}'\n";
        } elseif (!$newLokasi) {
            echo "⚠️ Absensi ID {$absensi->id}: Tidak ada lokasi yang ditemukan\n";
            $errors++;
        }
    }
    
    echo "\n📈 SUMMARY:\n";
    echo "✅ Records updated: {$updated}\n";
    echo "⚠️ Records with errors: {$errors}\n";
    echo "📊 Total processed: {$absensis->count()}\n";
    
    // Clear cache setelah update
    echo "\n🧹 Clearing cache...\n";
    \Cache::flush();
    \Cache::forget('absensis_data');
    \Cache::forget('lokasis_data');
    \Cache::forget('pembibitans_data');
    echo "✅ Cache cleared\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🎉 DATA FIX COMPLETED!\n";
echo "📋 Next steps:\n";
echo "1. Check absensi table in browser\n";
echo "2. Verify lokasi_kerja shows correct location\n";
echo "3. Test new absensi creation\n";
echo "4. Verify real-time data updates\n";
?>
