<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ImportExternalTransactions;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule the import external transactions job to run every hour
        $schedule->job(new ImportExternalTransactions)->hourly();
        
        // Abandon pending transactions after 6 hours (run every hour)
        $schedule->command('transactions:abandon-pending --hours=6')
                ->hourly()
                ->withoutOverlapping()
                ->runInBackground();

        // Automatic savings collection every 24 hours
        $schedule->command('savings:auto-collect --business-id=1')
                ->everyTwelveHours()
                ->withoutOverlapping()
                ->runInBackground();

        // Reset daily savings tracking at midnight
        $schedule->command('savings:reset-daily')
                ->dailyAt('00:00')
                ->withoutOverlapping()
                ->runInBackground();
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
