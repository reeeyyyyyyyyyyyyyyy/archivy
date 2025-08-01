<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\UpdateArchiveStatusJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Archive status update job - runs daily at 00:30 AM
        $schedule->job(new UpdateArchiveStatusJob())->dailyAt('00:30');
        
        // For testing purposes, you can temporarily change to everyMinute():
        // $schedule->job(new UpdateArchiveStatusJob())->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}