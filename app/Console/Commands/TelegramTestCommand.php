<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class TelegramTestCommand extends Command
{
    protected $signature = 'telegram:test {chat_id} {--message=}';
    protected $description = 'Test Telegram bot by sending a message';

    public function handle()
    {
        $chatId = $this->argument('chat_id');
        $message = $this->option('message') ?: 'Test message dari ARSIPIN Bot!';

        $this->info("Testing Telegram bot...");
        $this->info("Chat ID: {$chatId}");
        $this->info("Message: {$message}");

        try {
            $telegramService = new TelegramService();

            $result = $telegramService->sendMessage(
                $chatId,
                "🧪 <b>Test Message dari ARSIPIN</b>\n\n{$message}\n\nWaktu: " . now()->format('d M Y H:i') . " WIB"
            );

            if ($result) {
                $this->info('✅ Test message sent successfully!');
                $this->info('Check your Telegram chat to see the message.');
            } else {
                $this->error('❌ Failed to send test message');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error testing bot: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
