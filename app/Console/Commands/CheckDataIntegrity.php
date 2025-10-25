<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataSyncService;

class CheckDataIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'data:check-integrity {--fix : Fix data integrity issues}';

    /**
     * The console command description.
     */
    protected $description = 'Check and optionally fix data integrity issues';

    protected $dataSyncService;

    public function __construct(DataSyncService $dataSyncService)
    {
        parent::__construct();
        $this->dataSyncService = $dataSyncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking data integrity...');
        
        $validation = $this->dataSyncService->validateDataIntegrity();
        
        if ($validation['status'] === 'success') {
            $this->info('✅ Data integrity is valid');
            return 0;
        }
        
        if ($validation['status'] === 'warning') {
            $this->warn('⚠️  Data integrity issues found:');
            foreach ($validation['issues'] as $issue) {
                $this->warn("  - {$issue}");
            }
        }
        
        if ($validation['status'] === 'error') {
            $this->error('❌ Data integrity validation failed: ' . $validation['message']);
            return 1;
        }
        
        if ($this->option('fix')) {
            $this->info('Fixing data integrity issues...');
            $fixResult = $this->dataSyncService->fixDataIntegrity();
            
            if ($fixResult['status'] === 'success') {
                $this->info('✅ Data integrity issues fixed');
            } else {
                $this->error('❌ Failed to fix data integrity issues: ' . $fixResult['message']);
                return 1;
            }
        } else {
            $this->info('Use --fix option to automatically fix issues');
        }
        
        return 0;
    }
}
