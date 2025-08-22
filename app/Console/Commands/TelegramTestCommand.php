<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramTestCommand extends Command
{
    protected $signature = 'telegram:test {--message=} {--chat-id=} {--keyboard} {--command=}';
    protected $description = 'Test Telegram bot connection dan kirim pesan';

    public function handle()
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = $this->option('chat-id') ?: config('services.telegram.chat_id');
        $message = $this->option('message') ?: 'Test message from ARSIPIN Bot!';
        $showKeyboard = $this->option('keyboard');
        $command = $this->option('command');

        if (!$botToken) {
            $this->error('❌ TELEGRAM_BOT_TOKEN tidak ditemukan di .env file');
            return 1;
        }

        if (!$chatId) {
            $this->error('❌ TELEGRAM_CHAT_ID tidak ditemukan di .env file');
            return 1;
        }

        $this->info('🤖 Testing Telegram Bot...');
        $this->info('Bot Token: ' . substr($botToken, 0, 10) . '...');
        $this->info('Chat ID: ' . $chatId);

        // Test koneksi bot
        $this->info('🔍 Testing bot connection...');
        $response = Http::get("https://api.telegram.org/bot{$botToken}/getMe");

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['ok']) && $data['ok']) {
                $this->info('✅ Bot connected successfully!');
                $this->info('🤖 Bot Name: ' . $data['result']['first_name']);
                $this->info('👤 Username: @' . $data['result']['username']);
            } else {
                $this->error('❌ Bot connection failed: ' . ($data['description'] ?? 'Unknown error'));
                return 1;
            }
        } else {
            $this->error('❌ Failed to connect to Telegram API');
            return 1;
        }

        // Jika ada command, test command tersebut
        if ($command) {
            $this->info("📤 Testing command: /{$command}");
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => "/{$command}",
                'parse_mode' => 'HTML'
            ]);

            if ($response->successful()) {
                $this->info("✅ Command /{$command} sent successfully!");
            } else {
                $this->error("❌ Failed to send command /{$command}");
            }
            return 0;
        }

        // Test keyboard jika diminta
        if ($showKeyboard) {
            $this->info('🎹 Testing keyboard...');
            $keyboard = [
                'keyboard' => [
                    ['🔍 Cari Arsip', '📊 Status Sistem'],
                    ['⏰ Retensi Mendekati', '📦 Kapasitas Storage'],
                    ['❓ Bantuan', '🔄 Status Website']
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
                'selective' => false
            ];

            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => "🎹 <b>Test Keyboard ARSIPIN Bot</b>\n\nIni adalah test keyboard. Tekan tombol di bawah untuk test fungsi!",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($keyboard)
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['ok']) && $data['ok']) {
                    $this->info('✅ Keyboard test message sent successfully!');
                    $this->info('📱 Message ID: ' . $data['result']['message_id']);
                } else {
                    $this->error('❌ Failed to send keyboard test message: ' . ($data['description'] ?? 'Unknown error'));
                    return 1;
                }
            } else {
                $this->error('❌ Failed to send keyboard test message to Telegram');
                return 1;
            }
        } else {
            // Kirim test message biasa
            $this->info('📤 Sending test message...');
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['ok']) && $data['ok']) {
                    $this->info('✅ Test message sent successfully!');
                    $this->info('📱 Message ID: ' . $data['result']['message_id']);
                } else {
                    $this->error('❌ Failed to send test message: ' . ($data['description'] ?? 'Unknown error'));
                    return 1;
                }
            } else {
                $this->error('❌ Failed to send test message to Telegram');
                return 1;
            }
        }

        $this->info('');
        $this->info('🎉 Telegram bot test completed successfully!');
        $this->info('');
        $this->info('📋 Next steps:');
        $this->info('1. Start your bot on Telegram by sending /start');
        $this->info('2. Test keyboard: php artisan telegram:test --keyboard');
        $this->info('3. Test command: php artisan telegram:test --command=help');
        $this->info('4. Test webhook: Set webhook URL to your domain');
        $this->info('5. Webhook URL: https://yourdomain.com/api/telegram/webhook');

        return 0;
    }
}
