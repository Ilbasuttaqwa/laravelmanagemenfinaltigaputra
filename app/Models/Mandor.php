<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mandor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'gaji',
    ];

    protected $casts = [
        'gaji' => 'decimal:2',
    ];
}