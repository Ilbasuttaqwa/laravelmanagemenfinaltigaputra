<?php

namespace Database\Seeders;

use App\Models\Kandang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KandangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing lokasis
        $lokasis = \App\Models\Lokasi::all();

        $kandangs = [
            [
                'nama_kandang' => 'Kandang A',
                'lokasi_id' => $lokasis->where('nama_lokasi', 'Blok Utara')->first()?->id,
                'deskripsi' => 'Kandang utama untuk ternak sapi',
            ],
            [
                'nama_kandang' => 'Kandang B',
                'lokasi_id' => $lokasis->where('nama_lokasi', 'Blok Selatan')->first()?->id,
                'deskripsi' => 'Kandang untuk ternak kambing',
            ],
            [
                'nama_kandang' => 'Kandang C',
                'lokasi_id' => $lokasis->where('nama_lokasi', 'Blok Timur')->first()?->id,
                'deskripsi' => 'Kandang untuk ternak domba',
            ],
            [
                'nama_kandang' => 'Kandang D',
                'lokasi_id' => $lokasis->where('nama_lokasi', 'Blok Barat')->first()?->id,
                'deskripsi' => 'Kandang dalam perbaikan',
            ],
            [
                'nama_kandang' => 'Kandang E',
                'lokasi_id' => $lokasis->where('nama_lokasi', 'Blok Tengah')->first()?->id,
                'deskripsi' => 'Kandang untuk ternak ayam',
            ],
        ];

        foreach ($kandangs as $kandangData) {
            Kandang::create($kandangData);
        }
    }
}