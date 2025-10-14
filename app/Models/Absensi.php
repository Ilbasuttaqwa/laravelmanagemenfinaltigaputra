<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'tanggal',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
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

    // Accessor untuk mendapatkan nama karyawan
    public function getNamaKaryawanAttribute()
    {
        return $this->employee->nama ?? 'Karyawan Tidak Ditemukan';
    }

    // Accessor untuk mendapatkan tipe karyawan
    public function getTipeKaryawanAttribute()
    {
        return $this->employee->role ?? null;
    }

    // Accessor untuk mendapatkan ID karyawan
    public function getKaryawanIdAttribute()
    {
        return $this->employee_id;
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