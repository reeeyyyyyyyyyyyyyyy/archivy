<?php

namespace App\Console\Commands;

use App\Models\Archive;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class TestStatusTransitionNotification extends Command
{
    protected $signature = 'telegram:test-status-transition';
    protected $description = 'Test status transition notification';

    public function handle(TelegramService $telegramService)
    {
        $this->info('Testing status transition notification...');

        // Get a sample archive for testing
        $archive = Archive::with(['category', 'classification', 'createdByUser'])
            ->whereNotNull('rack_number')
            ->whereNotNull('box_number')
            ->whereNotNull('file_number')
            ->first();

        if (!$archive) {
            $this->error('No archive found with location information for testing');
            return;
        }

        $this->info("Testing with archive: {$archive->index_number}");

        // Test Aktif → Inaktif transition
        $this->info('Testing Aktif → Inaktif transition...');
        $telegramService->sendStatusTransitionNotification($archive, 'Aktif', 'Inaktif');

        // Test Inaktif → Permanen transition
        $this->info('Testing Inaktif → Permanen transition...');
        $telegramService->sendStatusTransitionNotification($archive, 'Inaktif', 'Permanen');

        // Test Inaktif → Musnah transition
        $this->info('Testing Inaktif → Musnah transition...');
        $telegramService->sendStatusTransitionNotification($archive, 'Inaktif', 'Musnah');

        $this->info('✅ Status transition notifications sent successfully!');
        $this->info('Check your Telegram for the test messages.');
    }
}
