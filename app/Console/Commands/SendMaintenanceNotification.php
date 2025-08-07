<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendMaintenanceNotification extends Command
{
    protected $signature = 'telegram:maintenance-notification';
    protected $description = 'Send maintenance notification to Telegram';

    public function handle(TelegramService $telegramService)
    {
        $this->info('Sending maintenance notification...');

        $telegramService->sendMaintenanceNotification();

        $this->info('Maintenance notification sent successfully!');
    }
}
