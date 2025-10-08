<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyAttendanceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_karyawan',
        'tipe_karyawan',
        'karyawan_id',
        'tahun',
        'bulan',
        'data_absensi',
        'total_hari_kerja',
        'total_hari_full',
        'total_hari_setengah',
        'total_hari_absen',
        'persentase_kehadiran',
    ];

    protected $casts = [
        'data_absensi' => 'array',
        'persentase_kehadiran' => 'decimal:2',
    ];

    // Relations
    public function karyawanGudang()
    {
        return $this->belongsTo(Gudang::class, 'karyawan_id')->where('tipe_karyawan', 'gudang');
    }

    public function karyawanMandor()
    {
        return $this->belongsTo(Mandor::class, 'karyawan_id')->where('tipe_karyawan', 'mandor');
    }

    // Accessor untuk mendapatkan nama bulan
    public function getNamaBulanAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulan[$this->bulan] ?? 'Bulan Tidak Valid';
    }

    // Accessor untuk format periode
    public function getPeriodeAttribute()
    {
        return $this->nama_bulan . ' ' . $this->tahun;
    }

    // Accessor untuk persentase kehadiran yang diformat
    public function getPersentaseKehadiranFormattedAttribute()
    {
        return number_format($this->persentase_kehadiran, 1) . '%';
    }

    // Method untuk mendapatkan data absensi per hari
    public function getAbsensiPerHari($tanggal)
    {
        $data = $this->data_absensi ?? [];
        return $data[$tanggal] ?? null;
    }

    // Method untuk menambah data absensi
    public function addAbsensi($tanggal, $status)
    {
        $data = $this->data_absensi ?? [];
        $data[$tanggal] = $status;
        $this->data_absensi = $data;
        $this->save();
    }

    // Method untuk menghitung statistik
    public function calculateStatistics()
    {
        $data = $this->data_absensi ?? [];
        $totalHari = count($data);
        $totalFull = 0;
        $totalSetengah = 0;
        $totalAbsen = 0;

        foreach ($data as $status) {
            switch ($status) {
                case 'full':
                    $totalFull++;
                    break;
                case 'setengah_hari':
                    $totalSetengah++;
                    break;
                case 'absen':
                default:
                    $totalAbsen++;
                    break;
            }
        }

        $this->total_hari_kerja = $totalHari;
        $this->total_hari_full = $totalFull;
        $this->total_hari_setengah = $totalSetengah;
        $this->total_hari_absen = $totalAbsen;
        
        if ($totalHari > 0) {
            $this->persentase_kehadiran = (($totalFull + $totalSetengah) / $totalHari) * 100;
        }
        
        $this->save();
    }

    // Scope untuk filter berdasarkan periode
    public function scopePeriode($query, $tahun, $bulan)
    {
        return $query->where('tahun', $tahun)->where('bulan', $bulan);
    }

    // Scope untuk filter berdasarkan tipe karyawan
    public function scopeTipeKaryawan($query, $tipe)
    {
        return $query->where('tipe_karyawan', $tipe);
    }
}