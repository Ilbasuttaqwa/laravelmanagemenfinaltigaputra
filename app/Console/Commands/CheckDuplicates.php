<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AntiDuplicationService;

class CheckDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'data:check-duplicates {--cleanup : Clean up duplicates automatically}';

    /**
     * The console command description.
     */
    protected $description = 'Check for and optionally clean up duplicate data';

    protected $antiDuplicationService;

    public function __construct(AntiDuplicationService $antiDuplicationService)
    {
        parent::__construct();
        $this->antiDuplicationService = $antiDuplicationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for duplicate data...');
        
        $duplicates = $this->antiDuplicationService->findAllDuplicates();
        
        if (empty($duplicates)) {
            $this->info('✅ No duplicates found! System is clean.');
            return 0;
        }
        
        $this->warn('⚠️  Duplicates found:');
        
        $totalDuplicates = 0;
        foreach ($duplicates as $type => $typeDuplicates) {
            $count = $typeDuplicates->count();
            $totalDuplicates += $count;
            $this->warn("  - {$type}: {$count} duplicate(s)");
            
            // Show details for each duplicate
            foreach ($typeDuplicates as $duplicate) {
                if ($type === 'employees' || $type === 'gudang' || $type === 'mandor') {
                    $this->line("    • {$duplicate->nama} (count: {$duplicate->count})");
                } elseif ($type === 'lokasi') {
                    $this->line("    • {$duplicate->nama_lokasi} (count: {$duplicate->count})");
                } elseif ($type === 'kandang') {
                    $this->line("    • {$duplicate->nama_kandang} (count: {$duplicate->count})");
                } elseif ($type === 'pembibitan') {
                    $this->line("    • {$duplicate->judul} (count: {$duplicate->count})");
                } elseif ($type === 'absensi') {
                    $this->line("    • {$duplicate->nama_karyawan} on {$duplicate->tanggal} (count: {$duplicate->count})");
                }
            }
        }
        
        $this->warn("Total duplicates found: {$totalDuplicates}");
        
        if ($this->option('cleanup')) {
            if ($this->confirm('Do you want to clean up these duplicates? (This will keep the latest record and delete older ones)')) {
                $this->info('🧹 Cleaning up duplicates...');
                
                try {
                    $cleaned = $this->antiDuplicationService->cleanupDuplicates();
                    
                    $this->info('✅ Duplicates cleaned up successfully!');
                    
                    foreach ($cleaned as $type => $ids) {
                        if (!empty($ids)) {
                            $this->line("  - {$type}: " . count($ids) . " record(s) removed");
                        }
                    }
                } catch (\Exception $e) {
                    $this->error('❌ Error cleaning up duplicates: ' . $e->getMessage());
                    return 1;
                }
            } else {
                $this->info('Cleanup cancelled.');
            }
        } else {
            $this->info('Use --cleanup option to automatically clean up duplicates.');
        }
        
        return 0;
    }
}
