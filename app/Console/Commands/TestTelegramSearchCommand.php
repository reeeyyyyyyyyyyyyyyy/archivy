<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class TestTelegramSearchCommand extends Command
{
    protected $signature = 'telegram:test-search {query}';
    protected $description = 'Test Telegram search functionality';

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    public function handle()
    {
        $query = $this->argument('query');

        $this->info("Testing search for: {$query}");

        try {
            $archives = \App\Models\Archive::where('index_number', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('file_number', 'like', "%{$query}%")
                ->with(['category', 'classification'])
                ->limit(10)
                ->get();

            if ($archives->isEmpty()) {
                $message = "🔍 <b>PENCARIAN ARSIP</b>\n\n";
                $message .= "❌ <b>Tidak ditemukan arsip</b> dengan kata kunci: <code>{$query}</code>\n\n";
                $message .= "💡 <b>Tips pencarian:</b>\n";
                $message .= "• Gunakan nomor arsip (contoh: 001/2024)\n";
                $message .= "• Gunakan kata kunci dari uraian\n";
                $message .= "• Gunakan nomor file\n";
                $message .= "• Coba kata kunci yang lebih umum";
            } else {
                $message = "🔍 <b>HASIL PENCARIAN ARSIP</b>\n\n";
                $message .= "🔎 <b>Kata kunci:</b> <code>{$query}</code>\n";
                $message .= "📊 <b>Ditemukan:</b> {$archives->count()} arsip\n\n";

                foreach ($archives as $archive) {
                    $message .= "📁 <b>No. Arsip:</b> {$archive->index_number}\n";
                    $message .= "📝 <b>Uraian:</b> {$archive->description}\n";
                    $message .= "📂 <b>Kategori:</b> {$archive->category->nama_kategori}\n";
                    $message .= "🏷️ <b>Status:</b> {$archive->status}\n";

                    if ($archive->rack_number && $archive->box_number && $archive->file_number) {
                        $message .= "📍 <b>Lokasi:</b> Rak {$archive->rack_number}, Box {$archive->box_number}, File {$archive->file_number}\n";
                    }

                    $message .= "📅 <b>Tanggal:</b> " . $archive->created_at->format('d/m/Y') . "\n";
                    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                }

                if ($archives->count() >= 10) {
                    $message .= "⚠️ <b>Note:</b> Menampilkan 10 hasil pertama. Gunakan kata kunci yang lebih spesifik untuk hasil yang lebih akurat.";
                }
            }

            $this->telegramService->sendMessage($message);
            $this->info('Search results sent to Telegram successfully');

        } catch (\Exception $e) {
            $this->error('Failed to send search results: ' . $e->getMessage());
        }
    }
}
