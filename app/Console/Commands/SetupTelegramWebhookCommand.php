<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SetupTelegramWebhookCommand extends Command
{
    protected $signature = 'telegram:setup-webhook {url?}';
    protected $description = 'Setup Telegram webhook URL';

    public function handle()
    {
        $token = config('services.telegram.bot_token');

        if (!$token) {
            $this->error('Telegram bot token not configured');
            return;
        }

        $url = $this->argument('url');

        if (!$url) {
            $url = $this->ask('Enter your webhook URL (e.g., https://yourdomain.com/api/telegram/webhook)');
        }

        if (!$url) {
            $this->error('Webhook URL is required');
            return;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", [
                'url' => $url
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if ($result['ok']) {
                    $this->info('✅ Webhook setup successful!');
                    $this->info("URL: {$url}");

                    // Test webhook
                    $this->info('Testing webhook...');
                    $testResponse = Http::get("https://api.telegram.org/bot{$token}/getWebhookInfo");

                    if ($testResponse->successful()) {
                        $webhookInfo = $testResponse->json();
                        $this->info('Webhook Info:');
                        $this->info('- URL: ' . ($webhookInfo['result']['url'] ?? 'Not set'));
                        $this->info('- Pending Updates: ' . ($webhookInfo['result']['pending_update_count'] ?? 0));
                    }

                } else {
                    $this->error('❌ Webhook setup failed: ' . ($result['description'] ?? 'Unknown error'));
                }
            } else {
                $this->error('❌ Failed to setup webhook: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Telegram webhook setup error', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);

            $this->error('❌ Error setting up webhook: ' . $e->getMessage());
        }
    }
}
