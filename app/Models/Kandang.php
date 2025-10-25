<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kandang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kandang',
        'lokasi_id',
        'deskripsi',
    ];

    protected $casts = [
        'lokasi_id' => 'integer',
    ];

    /**
     * Get the lokasi that owns the kandang.
     */
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    /**
     * Get the primary employees for the kandang (one-to-many).
     */
    public function primaryEmployees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the employees for the kandang (many-to-many for additional assignments).
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'kandang_employee');
    }

    /**
     * Get all employees (primary + additional) for the kandang.
     */
    public function allEmployees()
    {
        return $this->primaryEmployees()->get()->merge($this->employees()->get());
    }

    /**
     * Get the pembibitans for the kandang.
     */
    public function pembibitans()
    {
        return $this->hasMany(Pembibitan::class);
    }

}
