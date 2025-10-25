<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Absensi;
use App\Models\Kandang;
use App\Models\Lokasi;
use App\Models\Pembibitan;
use App\Models\Gudang;
use Carbon\Carbon;

class AddTestData extends Command
{
    protected $signature = 'data:add-test';
    protected $description = 'Add test data for master tables and attendance';

    public function handle()
    {
        $this->info('=== MENAMBAH DATA TEST ===');
        
        // Add Lokasi if not exists
        $lokasi = Lokasi::firstOrCreate(
            ['nama_lokasi' => 'Srengat'],
            ['alamat' => 'Jl. Raya Srengat, Blitar']
        );
        $this->info("Lokasi: {$lokasi->nama_lokasi}");
        
        // Add Kandang if not exists
        $kandang = Kandang::firstOrCreate(
            ['nama_kandang' => 'Kandang A'],
            [
                'lokasi_id' => $lokasi->id,
                'kapasitas' => 100,
                'deskripsi' => 'Kandang untuk ayam broiler'
            ]
        );
        $this->info("Kandang: {$kandang->nama_kandang}");
        
        // Add Pembibitan if not exists
        $pembibitan = Pembibitan::firstOrCreate(
            ['judul' => 'Pembibitan Ayam Broiler'],
            [
                'lokasi_id' => $lokasi->id,
                'kandang_id' => $kandang->id,
                'tanggal_mulai' => Carbon::now()->subDays(30),
                'tanggal_selesai' => Carbon::now()->addDays(30),
                'deskripsi' => 'Program pembibitan ayam broiler'
            ]
        );
        $this->info("Pembibitan: {$pembibitan->judul}");
        
        // Add Gudang if not exists
        $gudang = Gudang::firstOrCreate(
            ['nama' => 'Gudang Pakan'],
            [
                'alamat' => 'Jl. Gudang Pakan, Srengat',
                'kapasitas' => 1000,
                'gaji' => 2500000
            ]
        );
        $this->info("Gudang: {$gudang->nama}");
        
        // Add Employee
        $employee = Employee::firstOrCreate(
            ['nama' => 'Ahmad Karyawan'],
            [
                'role' => 'karyawan',
                'gaji' => 3000000,
                'kandang_id' => $kandang->id,
                'lokasi_kerja' => $lokasi->nama_lokasi
            ]
        );
        $this->info("Employee: {$employee->nama}");
        
        // Add Absensi
        $absensi = Absensi::create([
            'employee_id' => $employee->id,
            'source_type' => 'employee',
            'source_id' => $employee->id,
            'nama_karyawan' => $employee->nama,
            'role_karyawan' => $employee->role,
            'gaji_karyawan' => $employee->gaji,
            'tanggal' => Carbon::now(),
            'status' => 'full',
            'lokasi_kerja' => $employee->lokasi_kerja
        ]);
        $this->info("Absensi: {$absensi->nama_karyawan} - {$absensi->status}");
        
        $this->info('✅ Data test berhasil ditambahkan!');
        
        return 0;
    }
}