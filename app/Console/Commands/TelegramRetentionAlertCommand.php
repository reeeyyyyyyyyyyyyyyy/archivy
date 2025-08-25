<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class TelegramRetentionAlertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:retention-alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automatic retention alerts via Telegram bot for archives approaching retention dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚨 Starting automatic retention alerts...');

        try {
            $telegramService = new TelegramService();
            $telegramService->sendAutomaticRetentionAlerts();

            $this->info('✅ Automatic retention alerts sent successfully!');
            $this->info('📱 Check your Telegram for alerts');

        } catch (\Exception $e) {
            $this->error('❌ Error sending retention alerts: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
