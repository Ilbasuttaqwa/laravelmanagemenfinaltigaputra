<?php

namespace Database\Seeders;

use App\Models\Pembibitan;
use App\Models\Lokasi;
use App\Models\Kandang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PembibitanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first few lokasi and kandang for seeding
        $lokasi1 = Lokasi::first();
        $lokasi2 = Lokasi::skip(1)->first();
        $kandang1 = Kandang::first();
        $kandang2 = Kandang::skip(1)->first();

        $pembibitans = [
            [
                'judul' => 'Program Pembibitan Sapi Unggul',
                'lokasi_id' => $lokasi1?->id,
                'kandang_id' => $kandang1?->id,
                'tanggal_mulai' => now()->subDays(30),
            ],
            [
                'judul' => 'Pengembangan Bibit Kambing Kacang',
                'lokasi_id' => $lokasi2?->id,
                'kandang_id' => $kandang2?->id,
                'tanggal_mulai' => now()->subDays(15),
            ],
            [
                'judul' => 'Program Pembibitan Domba Garut',
                'lokasi_id' => $lokasi1?->id,
                'kandang_id' => null,
                'tanggal_mulai' => now()->subDays(10),
            ],
            [
                'judul' => 'Pembibitan Ayam Kampung Super',
                'lokasi_id' => $lokasi2?->id,
                'kandang_id' => null,
                'tanggal_mulai' => now()->subDays(5),
            ],
            [
                'judul' => 'Program Pembibitan Ternak Unggas',
                'lokasi_id' => null,
                'kandang_id' => $kandang1?->id,
                'tanggal_mulai' => now()->subDays(3),
            ]
        ];

        foreach ($pembibitans as $pembibitan) {
            Pembibitan::create($pembibitan);
        }
    }
}