<?php

namespace Database\Seeders;

use App\Models\Mandor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MandorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mandors = [
            ['nama' => 'Mandor A', 'gaji' => 9000000],
            ['nama' => 'Mandor B', 'gaji' => 11000000],
            ['nama' => 'Mandor C', 'gaji' => 8000000],
            ['nama' => 'Mandor D', 'gaji' => 7500000],
            ['nama' => 'Mandor E', 'gaji' => 13000000],
        ];

        foreach ($mandors as $mandor) {
            Mandor::create($mandor);
        }
    }
}