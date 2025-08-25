<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramSetWebhookCommand extends Command
{
    protected $signature = 'telegram:set-webhook {url}';
    protected $description = 'Set Telegram webhook URL';

    public function handle()
    {
        $url = $this->argument('url');
        $botToken = config('services.telegram.bot_token');

        if (!$botToken) {
            $this->error('Telegram bot token not configured!');
            $this->info('Please add TELEGRAM_BOT_TOKEN to your .env file');
            return 1;
        }

        $this->info("Setting webhook for bot: {$botToken}");
        $this->info("Webhook URL: {$url}/api/telegram/webhook");

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
                'url' => $url . '/api/telegram/webhook'
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if ($result['ok']) {
                    $this->info('✅ Webhook set successfully!');
                    $this->info("Webhook URL: {$result['result']['url']}");
                    Log::info('Telegram webhook set successfully', ['url' => $url]);
                } else {
                    $this->error('❌ Failed to set webhook: ' . ($result['description'] ?? 'Unknown error'));
                    return 1;
                }
            } else {
                $this->error('❌ Failed to communicate with Telegram API');
                $this->error('Response: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error setting webhook: ' . $e->getMessage());
            Log::error('Error setting Telegram webhook', ['error' => $e->getMessage()]);
            return 1;
        }

        return 0;
    }
}
