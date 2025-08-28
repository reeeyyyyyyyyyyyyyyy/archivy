<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class SendMaintenanceNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:maintenance-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send maintenance notification via Telegram';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->info('🔧 Sending maintenance notification...');

        try {
            $telegramService = new TelegramService();
            $telegramService->sendMaintenanceNotification();

            $this->info('✅ Maintenance notification sent!');
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }

        return 0;
    }
}
