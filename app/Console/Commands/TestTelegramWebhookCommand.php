<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestTelegramWebhookCommand extends Command
{
    protected $signature = 'telegram:test-webhook {message}';
    protected $description = 'Test Telegram webhook by sending a message';

    public function handle()
    {
        $message = $this->argument('message');
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            $this->error('Telegram bot token or chat ID not configured');
            return;
        }

        $this->info("Testing webhook with message: {$message}");

        try {
            // Simulate webhook call
            $webhookData = [
                'update_id' => time(),
                'message' => [
                    'message_id' => time(),
                    'from' => [
                        'id' => $chatId,
                        'first_name' => 'Test User',
                        'username' => 'testuser'
                    ],
                    'chat' => [
                        'id' => $chatId,
                        'type' => 'private'
                    ],
                    'date' => time(),
                    'text' => $message
                ]
            ];

            // Call our webhook endpoint
            $response = Http::post('http://127.0.0.1:8000/api/telegram/webhook', $webhookData);

            if ($response->successful()) {
                $this->info('✅ Webhook test successful!');
                $this->info('Response: ' . $response->body());
            } else {
                $this->error('❌ Webhook test failed: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Telegram webhook test error', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);

            $this->error('❌ Error testing webhook: ' . $e->getMessage());
        }
    }
}
