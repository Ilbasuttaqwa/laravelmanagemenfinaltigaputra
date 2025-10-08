<?php

namespace Database\Seeders;

use App\Models\Gudang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gudangs = [
            ['nama' => 'Gudang A', 'gaji' => 8000000],
            ['nama' => 'Gudang B', 'gaji' => 10000000],
            ['nama' => 'Gudang C', 'gaji' => 7000000],
            ['nama' => 'Gudang D', 'gaji' => 6500000],
            ['nama' => 'Gudang E', 'gaji' => 12000000],
        ];

        foreach ($gudangs as $gudang) {
            Gudang::create($gudang);
        }
    }
}