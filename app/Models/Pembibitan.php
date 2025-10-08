<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembibitan extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'lokasi_id',
        'kandang_id',
        'tanggal_mulai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
    ];

    // Relations
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}