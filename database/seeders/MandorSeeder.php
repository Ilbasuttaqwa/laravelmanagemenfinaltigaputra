<?php

namespace Database\Seeders;

use App\Models\Employee;
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
            ['nama' => 'Mandor A', 'gaji' => 9000000, 'role' => 'mandor'],
            ['nama' => 'Mandor B', 'gaji' => 11000000, 'role' => 'mandor'],
            ['nama' => 'Mandor C', 'gaji' => 8000000, 'role' => 'mandor'],
            ['nama' => 'Mandor D', 'gaji' => 7500000, 'role' => 'mandor'],
            ['nama' => 'Mandor E', 'gaji' => 13000000, 'role' => 'mandor'],
        ];

        foreach ($mandors as $mandor) {
            Employee::create($mandor);
        }
    }
}