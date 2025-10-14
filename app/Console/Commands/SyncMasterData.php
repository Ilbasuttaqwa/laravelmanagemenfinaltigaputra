<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MasterSyncService;

class SyncMasterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'master:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all master data to unified employees table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting master data synchronization...');
        
        MasterSyncService::syncAll();
        
        $this->info('Master data synchronization completed successfully!');
        
        // Show statistics
        $total = \App\Models\UnifiedEmployee::count();
        $this->info("Total unified employees: {$total}");
        
        $byRole = \App\Models\UnifiedEmployee::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->get();
            
        foreach ($byRole as $role) {
            $this->info("- {$role->role}: {$role->count}");
        }
    }
}