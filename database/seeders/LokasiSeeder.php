<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lokasis = [
            [
                'nama_lokasi' => 'Blok Utara',
                'deskripsi' => 'Lokasi kandang untuk ternak sapi di area utara',
            ],
            [
                'nama_lokasi' => 'Blok Selatan',
                'deskripsi' => 'Lokasi kandang untuk ternak kambing di area selatan',
            ],
            [
                'nama_lokasi' => 'Blok Timur',
                'deskripsi' => 'Lokasi kandang untuk ternak domba di area timur',
            ],
            [
                'nama_lokasi' => 'Blok Barat',
                'deskripsi' => 'Lokasi kandang dalam perbaikan di area barat',
            ],
            [
                'nama_lokasi' => 'Blok Tengah',
                'deskripsi' => 'Lokasi kandang utama untuk ternak ayam di area tengah',
            ],
        ];

        foreach ($lokasis as $lokasi) {
            \App\Models\Lokasi::create($lokasi);
        }
    }
}
