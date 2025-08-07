<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TestTelegramBot extends Command
{
    protected $signature = 'telegram:test';
    protected $description = 'Test Telegram bot connection';

    public function handle(TelegramService $telegramService)
    {
        $this->info('Testing Telegram bot connection...');

        $message = "🤖 <b>TEST NOTIFICATION</b>\n\n";
        $message .= "Sistem Arsip Digital Telegram Bot berhasil terhubung!\n";
        $message .= "⏰ <b>Waktu Test:</b> " . now()->format('d/m/Y H:i:s') . "\n";
        $message .= "✅ <b>Status:</b> Bot aktif dan siap menerima notifikasi";

        $result = $telegramService->sendMessage($message);

        if ($result) {
            $this->info('✅ Telegram bot test successful!');
            $this->info('Check your Telegram for the test message.');
        } else {
            $this->error('❌ Telegram bot test failed!');
            $this->error('Please check your bot token and chat ID configuration.');
        }
    }
}
