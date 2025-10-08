<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Gudang;
use App\Models\Mandor;
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
        // Get all master data for seeding
        $gudangs = Gudang::all();
        $mandors = Mandor::all();

        if ($gudangs->isEmpty() || $mandors->isEmpty()) {
            return; // Skip if no master data
        }

        // Generate attendance data for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        
        for ($i = 0; $i < 30; $i++) {
            $tanggal = $startDate->copy()->addDays($i);
            
            // Skip weekends
            if ($tanggal->isWeekend()) {
                continue;
            }

            // Generate attendance for gudang employees
            foreach ($gudangs as $gudang) {
                $status = $this->getRandomStatus();
                Absensi::create([
                    'gudang_id' => $gudang->id,
                    'tanggal' => $tanggal,
                    'status' => $status,
                ]);
            }

            // Generate attendance for mandor employees
            foreach ($mandors as $mandor) {
                $status = $this->getRandomStatus();
                Absensi::create([
                    'mandor_id' => $mandor->id,
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