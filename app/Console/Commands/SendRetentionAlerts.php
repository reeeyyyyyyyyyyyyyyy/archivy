<?php

namespace App\Console\Commands;

use App\Models\Archive;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendRetentionAlerts extends Command
{
    protected $signature = 'telegram:retention-alerts';
    protected $description = 'Send retention alerts to Telegram';

    public function handle(TelegramService $telegramService)
    {
        $this->info('Checking for archives nearing retention...');

        // Arsip yang akan jatuh tempo dalam 30 hari
        $archives30Days = Archive::where('status', 'aktif')
            ->where('transition_active_due', '<=', now()->addDays(30))
            ->where('transition_active_due', '>', now())
            ->with(['category', 'createdByUser'])
            ->get();

        // Arsip yang akan jatuh tempo dalam 7 hari
        $archives7Days = Archive::where('status', 'aktif')
            ->where('transition_active_due', '<=', now()->addDays(7))
            ->where('transition_active_due', '>', now())
            ->with(['category', 'createdByUser'])
            ->get();

        if ($archives7Days->count() > 0) {
            $this->info("Found {$archives7Days->count()} archives expiring in 7 days");
            $telegramService->sendRetentionAlert($archives7Days);
        }

        if ($archives30Days->count() > 0) {
            $this->info("Found {$archives30Days->count()} archives expiring in 30 days");
            $telegramService->sendRetentionAlert($archives30Days);
        }

        $this->info('Retention alerts sent successfully!');
    }
}
