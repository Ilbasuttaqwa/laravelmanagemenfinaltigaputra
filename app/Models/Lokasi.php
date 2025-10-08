<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lokasi',
        'deskripsi',
    ];

    /**
     * Get the kandangs for the lokasi.
     */
    public function kandangs()
    {
        return $this->hasMany(Kandang::class, 'lokasi_id');
    }
}
