<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'nama' => 'John Doe',
                'gaji' => 8000000,
                'role' => 'karyawan',
            ],
            [
                'nama' => 'Jane Smith',
                'gaji' => 10000000,
                'role' => 'karyawan',
            ],
            [
                'nama' => 'Bob Johnson',
                'gaji' => 7000000,
                'role' => 'karyawan',
            ],
            [
                'nama' => 'Alice Brown',
                'gaji' => 6500000,
                'role' => 'karyawan',
            ],
            [
                'nama' => 'Charlie Wilson',
                'gaji' => 12000000,
                'role' => 'karyawan',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
