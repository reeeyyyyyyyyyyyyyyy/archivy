<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramSetWebhookCommand extends Command
{
    protected $signature = 'telegram:set-webhook {url}';
    protected $description = 'Set Telegram webhook URL untuk real-time response';

    public function handle()
    {
        $url = $this->argument('url');
        $botToken = config('services.telegram.bot_token');

        if (!$botToken) {
            $this->error('❌ TELEGRAM_BOT_TOKEN tidak ditemukan di .env file');
            return 1;
        }

        $this->info('🔗 Setting Telegram webhook...');
        $this->info('URL: ' . $url);
        $this->info('Bot Token: ' . substr($botToken, 0, 10) . '...');

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
                'url' => $url
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['ok']) && $data['ok']) {
                    $this->info('✅ Webhook set successfully!');
                    $this->info('📱 Bot akan merespon pesan user secara real-time');

                    // Test webhook
                    $this->info('🧪 Testing webhook...');
                    $this->testWebhook($url);

                } else {
                    $this->error('❌ Failed to set webhook: ' . ($data['description'] ?? 'Unknown error'));
                    return 1;
                }
            } else {
                $this->error('❌ HTTP error: ' . $response->status());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        $this->info('');
        $this->info('🎉 Webhook setup completed!');
        $this->info('');
        $this->info('📋 Next steps:');
        $this->info('1. Buka Telegram');
        $this->info('2. Kirim /start ke @arsipin_bot');
        $this->info('3. Bot akan merespon dengan keyboard tombol');
        $this->info('4. Klik tombol untuk test fungsi');

        return 0;
    }

    protected function testWebhook($url)
    {
        try {
            $response = Http::post($url, [
                'message' => [
                    'chat' => ['id' => config('services.telegram.chat_id')],
                    'text' => '/start',
                    'from' => ['first_name' => 'Test']
                ]
            ]);

            if ($response->successful()) {
                $this->info('✅ Webhook test successful!');
            } else {
                $this->warn('⚠️ Webhook test failed: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->warn('⚠️ Webhook test failed: ' . $e->getMessage());
        }
    }
}
