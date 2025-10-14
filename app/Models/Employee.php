<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'gaji',
        'role',
        'kandang_id',
        'lokasi_kerja',
    ];

    protected $casts = [
        'gaji' => 'decimal:2',
    ];

    /**
     * Get the kandang that the employee belongs to.
     */
    public function kandang()
    {
        return $this->belongsTo(Kandang::class);
    }

    /**
     * Get the kandangs for the employee (many-to-many for additional assignments).
     */
    public function kandangs()
    {
        return $this->belongsToMany(Kandang::class, 'kandang_employee');
    }

    /**
     * Get the absensis for the employee.
     */
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
