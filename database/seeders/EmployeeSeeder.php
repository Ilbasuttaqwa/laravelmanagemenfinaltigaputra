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
            ],
            [
                'nama' => 'Jane Smith',
                'gaji' => 10000000,
            ],
            [
                'nama' => 'Bob Johnson',
                'gaji' => 7000000,
            ],
            [
                'nama' => 'Alice Brown',
                'gaji' => 6500000,
            ],
            [
                'nama' => 'Charlie Wilson',
                'gaji' => 12000000,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
