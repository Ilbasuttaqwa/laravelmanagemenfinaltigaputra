<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Auto cleanup setiap hari jam 2 pagi
        $schedule->command('system:cleanup --force')
                 ->dailyAt('02:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Cache warm-up setiap 6 jam
        $schedule->call(function () {
            \App\Services\SmartCacheService::warmUpCache();
        })->everySixHours();

        // Database optimization setiap minggu
        $schedule->command('system:cleanup --force --memory')
                 ->weekly()
                 ->sundays()
                 ->at('03:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
