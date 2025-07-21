<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\UpdateArchiveStatusJob;
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk menggunakan Facade Log

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Job Anda yang ada (tetap everyMinute untuk pengujian)
        $schedule->job(new \App\Jobs\UpdateArchiveStatusJob())->everyMinute();

        // --- TAMBAH BARIS INI UNTUK PENGUJIAN ---
        $schedule->call(function () {
            Log::info('Test schedule command executed.');
        })->everyMinute()->name('test-schedule-command'); // Beri nama untuk identifikasi mudah
        // -------------------------------------

        // Baris dailyAt('00:30') harus dikomentari
        // $schedule->job(new \App\Jobs\UpdateArchiveStatusJob())->dailyAt('00:30'); 
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