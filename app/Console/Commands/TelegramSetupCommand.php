<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class TelegramSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:setup {--url=} {--test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Telegram bot webhook dan test koneksi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $telegramService = new TelegramService();

        $this->info('🤖 ARSIPIN Telegram Bot Setup');
        $this->info('================================');

        // Test koneksi bot
        $this->info('🔍 Testing bot connection...');
        $connection = $telegramService->testConnection();

        if (!$connection['success']) {
            $this->error('❌ Bot connection failed: ' . $connection['message']);
            $this->error('Please check your TELEGRAM_BOT_TOKEN in .env file');
            return 1;
        }

        $this->info('✅ Bot connected successfully!');
        $this->info('🤖 Bot Name: ' . $connection['bot_name']);
        $this->info('👤 Username: @' . $connection['username']);

        // Set webhook jika URL diberikan
        if ($url = $this->option('url')) {
            $this->info('🔗 Setting webhook to: ' . $url);
            $webhookResult = $telegramService->setWebhook($url);

            if ($webhookResult && isset($webhookResult['ok']) && $webhookResult['ok']) {
                $this->info('✅ Webhook set successfully!');
            } else {
                $this->error('❌ Failed to set webhook');
                return 1;
            }
        }

        // Test webhook jika diminta
        if ($this->option('test')) {
            $this->info('🧪 Testing webhook...');
            $this->info('Please send a message to your bot on Telegram');
            $this->info('Then check the logs to see if webhook is working');
        }

        $this->info('');
        $this->info('📋 Next steps:');
        $this->info('1. Start your bot on Telegram by sending /start');
        $this->info('2. Test webhook: php artisan telegram:setup --test');
        $this->info('3. Set webhook: php artisan telegram:setup --url=https://yourdomain.com/api/telegram/webhook');

        return 0;
    }
}
