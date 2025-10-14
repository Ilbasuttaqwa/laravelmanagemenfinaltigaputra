<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnifiedEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'gaji',
        'role',
        'source_type',
        'source_id',
    ];

    protected $casts = [
        'gaji' => 'decimal:2',
    ];

    // Scope untuk filter berdasarkan role
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Scope untuk filter berdasarkan source type
    public function scopeBySource($query, $sourceType)
    {
        return $query->where('source_type', $sourceType);
    }

    // Scope untuk admin (tidak bisa lihat mandor)
    public function scopeForAdmin($query)
    {
        return $query->where('role', '!=', 'mandor');
    }

    // Scope untuk manager (bisa lihat semua)
    public function scopeForManager($query)
    {
        return $query;
    }

    // Accessor untuk badge class berdasarkan role
    public function getRoleBadgeAttribute()
    {
        return match($this->role) {
            'mandor' => 'bg-success',
            'karyawan_gudang' => 'bg-info',
            'karyawan' => 'bg-primary',
            default => 'bg-secondary',
        };
    }

    // Accessor untuk icon berdasarkan role
    public function getRoleIconAttribute()
    {
        return match($this->role) {
            'mandor' => 'bi-person-badge',
            'karyawan_gudang' => 'bi-building',
            'karyawan' => 'bi-building',
            default => 'bi-person',
        };
    }

    // Accessor untuk role label yang lebih readable
    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            'mandor' => 'Mandor',
            'karyawan_gudang' => 'Karyawan Gudang',
            'karyawan' => 'Karyawan',
            default => ucfirst($this->role),
        };
    }
}