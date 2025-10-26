<?php
/**
 * Script untuk mengecek data di hosting
 * Jalankan di hosting untuk debugging error HTTP 409
 */

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Absensi;
use App\Models\Gudang;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

echo "=== CEK DATA HOSTING ===\n\n";

try {
    // 1. Cek data gudang
    echo "1. Data Gudang:\n";
    $gudangs = Gudang::all();
    foreach ($gudangs as $gudang) {
        echo "- ID: {$gudang->id}, Nama: {$gudang->nama}, Gaji: {$gudang->gaji}\n";
    }
    echo "\n";
    
    // 2. Cek data employee
    echo "2. Data Employee:\n";
    $employees = Employee::all();
    foreach ($employees as $emp) {
        echo "- ID: {$emp->id}, Nama: {$emp->nama}, Jabatan: {$emp->jabatan}\n";
    }
    echo "\n";
    
    // 3. Cek data absensi terbaru
    echo "3. Data Absensi Terbaru (10 record):\n";
    $absensis = Absensi::orderBy('created_at', 'desc')->limit(10)->get();
    foreach ($absensis as $abs) {
        echo "- ID: {$abs->id}, Nama: {$abs->nama_karyawan}, Tanggal: {$abs->tanggal}, Status: {$abs->status}\n";
    }
    echo "\n";
    
    // 4. Cek data absensi untuk budi
    echo "4. Data Absensi untuk 'budi':\n";
    $budiAbsensi = Absensi::where('nama_karyawan', 'budi')->get();
    foreach ($budiAbsensi as $abs) {
        echo "- ID: {$abs->id}, Tanggal: {$abs->tanggal}, Status: {$abs->status}, Created: {$abs->created_at}\n";
    }
    echo "\n";
    
    // 5. Cek data absensi untuk tanggal 2025-10-01
    echo "5. Data Absensi untuk tanggal 2025-10-01:\n";
    $absensiTanggal = Absensi::whereDate('tanggal', '2025-10-01')->get();
    foreach ($absensiTanggal as $abs) {
        echo "- ID: {$abs->id}, Nama: {$abs->nama_karyawan}, Status: {$abs->status}\n";
    }
    echo "\n";
    
    // 6. Cek duplicate berdasarkan nama dan tanggal
    echo "6. Cek Duplicate berdasarkan nama dan tanggal:\n";
    $duplicates = DB::select("
        SELECT nama_karyawan, tanggal, COUNT(*) as count 
        FROM absensis 
        GROUP BY nama_karyawan, tanggal 
        HAVING COUNT(*) > 1
    ");
    
    if (count($duplicates) > 0) {
        foreach ($duplicates as $dup) {
            echo "- {$dup->nama_karyawan} pada {$dup->tanggal} ({$dup->count} records)\n";
        }
    } else {
        echo "Tidak ada duplicate ditemukan.\n";
    }
    echo "\n";
    
    // 7. Cek constraint unique
    echo "7. Cek Constraint Unique:\n";
    $constraints = DB::select("
        SELECT CONSTRAINT_NAME, COLUMN_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'absensis' 
        AND CONSTRAINT_SCHEMA = DATABASE()
        AND CONSTRAINT_NAME != 'PRIMARY'
    ");
    
    foreach ($constraints as $constraint) {
        echo "- {$constraint->CONSTRAINT_NAME} pada {$constraint->COLUMN_NAME}\n";
    }
    echo "\n";
    
    echo "=== CEK SELESAI ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
