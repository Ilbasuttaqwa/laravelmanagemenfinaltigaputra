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

}
