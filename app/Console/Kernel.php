<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\UpdateArchiveStatusJob;
use App\Console\Commands\SendRetentionAlerts;
use App\Console\Commands\SendMaintenanceNotification;

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

        // Telegram notifications
        $schedule->command('telegram:retention-alerts')->dailyAt('08:00');
        $schedule->command('telegram:maintenance-notification')->dailyAt('23:00');
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
