<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CalendarAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'gudang_id',
        'mandor_id',
        'tahun',
        'bulan',
        'attendance_data',
    ];

    protected $casts = [
        'attendance_data' => 'array',
    ];

    // Relations
    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    public function mandor()
    {
        return $this->belongsTo(Mandor::class);
    }

    // Accessor untuk mendapatkan nama karyawan
    public function getNamaKaryawanAttribute()
    {
        if ($this->gudang_id) {
            return $this->gudang->nama ?? 'Karyawan Gudang Tidak Ditemukan';
        }
        
        if ($this->mandor_id) {
            return $this->mandor->nama ?? 'Karyawan Mandor Tidak Ditemukan';
        }
        
        return 'Karyawan Tidak Ditemukan';
    }

    // Accessor untuk mendapatkan tipe karyawan
    public function getTipeKaryawanAttribute()
    {
        if ($this->gudang_id) {
            return 'gudang';
        }
        
        if ($this->mandor_id) {
            return 'mandor';
        }
        
        return null;
    }

    // Accessor untuk mendapatkan ID karyawan
    public function getKaryawanIdAttribute()
    {
        return $this->gudang_id ?? $this->mandor_id;
    }

    // Accessor untuk nama bulan
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

    // Method untuk mendapatkan status absensi pada hari tertentu
    public function getAttendanceStatus($day)
    {
        $data = $this->attendance_data ?? [];
        return $data[$day] ?? null;
    }

    // Method untuk mengupdate status absensi pada hari tertentu
    public function updateAttendanceStatus($day, $status)
    {
        $data = $this->attendance_data ?? [];
        $data[$day] = $status;
        $this->attendance_data = $data;
        $this->save();
    }

    // Method untuk mendapatkan semua hari dalam bulan
    public function getDaysInMonth()
    {
        return Carbon::create($this->tahun, $this->bulan)->daysInMonth;
    }

    // Method untuk menghitung statistik
    public function getStatistics()
    {
        $data = $this->attendance_data ?? [];
        $totalDays = $this->getDaysInMonth();
        $totalAktif = 0;
        $totalSetengahHari = 0;
        $totalAbsen = 0;

        for ($day = 1; $day <= $totalDays; $day++) {
            $status = $data[$day] ?? null;
            switch ($status) {
                case 'aktif':
                    $totalAktif++;
                    break;
                case 'setengah_hari':
                    $totalSetengahHari++;
                    break;
                case 'absen':
                default:
                    $totalAbsen++;
                    break;
            }
        }

        return [
            'total_days' => $totalDays,
            'total_aktif' => $totalAktif,
            'total_setengah_hari' => $totalSetengahHari,
            'total_absen' => $totalAbsen,
            'persentase_kehadiran' => $totalDays > 0 ? (($totalAktif + $totalSetengahHari) / $totalDays) * 100 : 0
        ];
    }

    // Scope untuk filter berdasarkan periode
    public function scopePeriode($query, $tahun, $bulan)
    {
        return $query->where('tahun', $tahun)->where('bulan', $bulan);
    }

    // Scope untuk filter berdasarkan tipe karyawan
    public function scopeTipeKaryawan($query, $tipe)
    {
        if ($tipe === 'gudang') {
            return $query->whereNotNull('gudang_id');
        } elseif ($tipe === 'mandor') {
            return $query->whereNotNull('mandor_id');
        }
        return $query;
    }
}