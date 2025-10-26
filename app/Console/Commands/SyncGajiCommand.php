<?php

namespace App\Console\Commands;

use App\Services\AutoSyncGajiService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SyncGajiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gaji:sync 
                            {--periode-mulai= : Tanggal mulai periode (Y-m-d)}
                            {--periode-selesai= : Tanggal selesai periode (Y-m-d)}
                            {--employee-id= : ID employee tertentu}
                            {--all : Sync semua gaji}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi gaji untuk periode tertentu atau semua data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $autoSyncService = new AutoSyncGajiService();
        
        $periodeMulai = $this->option('periode-mulai') ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $periodeSelesai = $this->option('periode-selesai') ?: Carbon::now()->endOfMonth()->format('Y-m-d');
        $employeeId = $this->option('employee-id');
        $syncAll = $this->option('all');

        $this->info("🔄 Memulai sinkronisasi gaji...");
        $this->info("📅 Periode: {$periodeMulai} - {$periodeSelesai}");

        if ($syncAll) {
            $this->info("🌍 Mode: Sync semua gaji");
            $result = $autoSyncService->syncAllGajiForPeriod($periodeMulai, $periodeSelesai);
        } elseif ($employeeId) {
            $this->info("👤 Mode: Sync gaji employee ID {$employeeId}");
            $employee = \App\Models\Employee::find($employeeId);
            if (!$employee) {
                $this->error("❌ Employee dengan ID {$employeeId} tidak ditemukan");
                return 1;
            }
            $result = $autoSyncService->syncEmployeeGaji($employeeId, $employee->gaji_pokok, $periodeMulai, $periodeSelesai);
        } else {
            $this->error("❌ Pilih salah satu: --employee-id=ID atau --all");
            return 1;
        }

        if ($result['success']) {
            $this->info("✅ " . $result['message']);
            if (isset($result['updated_count'])) {
                $this->info("📊 Jumlah absensi yang diupdate: {$result['updated_count']}");
            }
            if (isset($result['total_updated'])) {
                $this->info("📊 Total absensi yang diupdate: {$result['total_updated']}");
            }
        } else {
            $this->error("❌ " . $result['message']);
            return 1;
        }

        return 0;
    }
}
