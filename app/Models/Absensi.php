<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'pembibitan_id',
        'nama_karyawan',
        'jabatan',
        'gaji_pokok_saat_itu',
        'tanggal',
        'status',
        'gaji_hari_itu',
        'lokasi_kerja',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'gaji_pokok_saat_itu' => 'decimal:2',
        'gaji_hari_itu' => 'decimal:2',
    ];

    // Relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Relationship dengan Pembibitan (sesuai ERD)
    public function pembibitan()
    {
        return $this->belongsTo(Pembibitan::class, 'pembibitan_id');
    }

    // Accessor untuk status yang lebih readable
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'full' => 'Full Day',
            'setengah_hari' => 'Half Day',
            default => $this->status,
        };
    }

    // Accessor untuk badge class berdasarkan status
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'full' => 'bg-success',
            'setengah_hari' => 'bg-warning',
            default => 'bg-secondary',
        };
    }

    // Accessor untuk mendapatkan nama karyawan (tidak mengubah data yang sudah tersimpan)
    public function getNamaKaryawanAttribute()
    {
        // Jika nama_karyawan sudah tersimpan, gunakan yang tersimpan
        if (!empty($this->attributes['nama_karyawan'])) {
            return $this->attributes['nama_karyawan'];
        }
        
        // Fallback ke employee relationship jika ada
        if ($this->employee_id && $this->employee) {
            return $this->employee->nama;
        }
        
        // Fallback terakhir
        return 'Karyawan Tidak Ditemukan';
    }

    // Accessor untuk mendapatkan tipe karyawan
    public function getTipeKaryawanAttribute()
    {
        return $this->role_karyawan ?? $this->employee->role ?? null;
    }

    // Accessor untuk mendapatkan ID karyawan
    public function getKaryawanIdAttribute()
    {
        return $this->employee_id;
    }

    // Accessor untuk mendapatkan gaji karyawan
    public function getGajiKaryawanAttribute()
    {
        return $this->gaji_pokok_saat_itu ?? $this->employee->gaji ?? 0;
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    // Scope untuk filter berdasarkan status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan karyawan
    public function scopeEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // Scope untuk filter berdasarkan periode (bulan dan tahun)
    public function scopePeriode($query, $tahun, $bulan)
    {
        return $query->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan);
    }
}