<?php
/**
 * Script untuk mengecek dan membersihkan data duplicate absensi
 * Jalankan di hosting untuk mengatasi error HTTP 409 Conflict
 */

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Absensi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== SCRIPT PERBAIKAN DUPLICATE ABSENSI ===\n\n";

try {
    // 1. Cek data duplicate
    echo "1. Mencari data duplicate...\n";
    
    $duplicates = Absensi::select('nama_karyawan', 'tanggal', DB::raw('COUNT(*) as count'))
        ->groupBy('nama_karyawan', 'tanggal')
        ->having('count', '>', 1)
        ->get();
    
    echo "Ditemukan " . $duplicates->count() . " kelompok data duplicate\n\n";
    
    if ($duplicates->count() > 0) {
        echo "Detail duplicate:\n";
        foreach ($duplicates as $dup) {
            echo "- {$dup->nama_karyawan} pada {$dup->tanggal} ({$dup->count} records)\n";
        }
        echo "\n";
        
        // 2. Bersihkan duplicate (keep latest)
        echo "2. Membersihkan data duplicate...\n";
        
        $cleaned = 0;
        foreach ($duplicates as $dup) {
            $records = Absensi::where('nama_karyawan', $dup->nama_karyawan)
                ->whereDate('tanggal', $dup->tanggal)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Keep the first (latest) record, delete the rest
            if ($records->count() > 1) {
                $toDelete = $records->skip(1);
                foreach ($toDelete as $record) {
                    echo "  Menghapus record ID: {$record->id} (created: {$record->created_at})\n";
                    $record->delete();
                    $cleaned++;
                }
            }
        }
        
        echo "\nBerhasil membersihkan {$cleaned} record duplicate\n\n";
    } else {
        echo "Tidak ada data duplicate ditemukan.\n\n";
    }
    
    // 3. Cek data absensi untuk karyawan_gudang
    echo "3. Cek data absensi karyawan_gudang...\n";
    
    $gudangAbsensi = Absensi::where('nama_karyawan', 'LIKE', '%gudang%')
        ->orWhere('nama_karyawan', 'budi')
        ->orderBy('tanggal', 'desc')
        ->get();
    
    echo "Ditemukan " . $gudangAbsensi->count() . " record absensi untuk karyawan gudang:\n";
    foreach ($gudangAbsensi as $abs) {
        echo "- ID: {$abs->id}, Nama: {$abs->nama_karyawan}, Tanggal: {$abs->tanggal}, Status: {$abs->status}\n";
    }
    echo "\n";
    
    // 4. Cek constraint database
    echo "4. Cek struktur tabel absensis...\n";
    
    $columns = DB::select("DESCRIBE absensis");
    echo "Kolom tabel absensis:\n";
    foreach ($columns as $col) {
        echo "- {$col->Field} ({$col->Type}) - {$col->Null} - {$col->Key}\n";
    }
    echo "\n";
    
    // 5. Cek index dan constraint
    $indexes = DB::select("SHOW INDEX FROM absensis");
    echo "Index dan constraint:\n";
    foreach ($indexes as $idx) {
        echo "- {$idx->Key_name} pada kolom {$idx->Column_name}\n";
    }
    echo "\n";
    
    echo "=== SCRIPT SELESAI ===\n";
    echo "Jika masih ada error HTTP 409, coba:\n";
    echo "1. Cek apakah ada data dengan nama dan tanggal yang sama\n";
    echo "2. Pastikan tidak ada constraint unique yang bermasalah\n";
    echo "3. Cek log Laravel untuk detail error\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
