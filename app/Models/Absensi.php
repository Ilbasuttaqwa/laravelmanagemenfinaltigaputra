<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'gudang_id',
        'mandor_id',
        'tanggal',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
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

    // Scope untuk filter berdasarkan karyawan gudang
    public function scopeGudang($query, $gudangId)
    {
        return $query->where('gudang_id', $gudangId);
    }

    // Scope untuk filter berdasarkan karyawan mandor
    public function scopeMandor($query, $mandorId)
    {
        return $query->where('mandor_id', $mandorId);
    }

    // Scope untuk filter berdasarkan periode (bulan dan tahun)
    public function scopePeriode($query, $tahun, $bulan)
    {
        return $query->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan);
    }
}