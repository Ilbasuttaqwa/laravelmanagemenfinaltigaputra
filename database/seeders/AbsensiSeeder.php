<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all employees for seeding
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            return; // Skip if no employees
        }

        // Generate attendance data for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        
        for ($i = 0; $i < 30; $i++) {
            $tanggal = $startDate->copy()->addDays($i);
            
            // Skip weekends
            if ($tanggal->isWeekend()) {
                continue;
            }

            // Generate attendance for all employees
            foreach ($employees as $employee) {
                $status = $this->getRandomStatus();
                Absensi::create([
                    'employee_id' => $employee->id,
                    'tanggal' => $tanggal,
                    'status' => $status,
                ]);
            }
        }
    }

    private function getRandomStatus()
    {
        $statuses = ['full', 'setengah_hari'];
        $weights = [80, 20]; // 80% full, 20% half day
        
        $random = mt_rand(1, 100);
        $cumulative = 0;
        
        for ($i = 0; $i < count($statuses); $i++) {
            $cumulative += $weights[$i];
            if ($random <= $cumulative) {
                return $statuses[$i];
            }
        }
        
        return 'full';
    }

    private function getKeterangan($status)
    {
        return match($status) {
            'full' => 'Masuk full day',
            'setengah_hari' => 'Masuk setengah hari',
            default => 'Tidak ada keterangan',
        };
    }
}