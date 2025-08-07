<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TelegramWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook';
    protected $description = 'Handle Telegram webhook and chat interactions';

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    public function handle()
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            $this->error('Telegram bot token or chat ID not configured');
            return;
        }

        try {
            // Get updates from Telegram
            $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates");

            if (!$response->successful()) {
                $this->error('Failed to get updates from Telegram');
                return;
            }

            $updates = $response->json('result', []);

            foreach ($updates as $update) {
                $message = $update['message'] ?? null;

                if (!$message) continue;

                $chatId = $message['chat']['id'];
                $text = $message['text'] ?? '';
                $messageId = $message['message_id'];

                // Check if this message was already processed
                $processedKey = "telegram_processed_{$messageId}";
                if (Cache::has($processedKey)) {
                    continue;
                }

                // Mark as processed
                Cache::put($processedKey, true, now()->addHours(1));

                $this->processMessage($chatId, $text);
            }

            $this->info('Webhook processed successfully');

        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage()
            ]);
            $this->error('Webhook processing failed: ' . $e->getMessage());
        }
    }

    protected function processMessage($chatId, $text)
    {
        $text = trim($text);

        // Handle search command
        if (preg_match('/^\/cari\s+(.+)$/i', $text, $matches)) {
            $query = trim($matches[1]);
            $this->searchArchives($chatId, $query);
            return;
        }

        // Handle help command
        if (preg_match('/^\/help$/i', $text)) {
            $this->sendHelpMessage($chatId);
            return;
        }

        // Handle status command
        if (preg_match('/^\/status$/i', $text)) {
            $this->sendStatusMessage($chatId);
            return;
        }

        // Handle unknown commands
        if (str_starts_with($text, '/')) {
            $this->sendUnknownCommandMessage($chatId);
            return;
        }

        // If it's not a command, treat as search query
        if (!empty($text)) {
            $this->searchArchives($chatId, $text);
        }
    }

    protected function searchArchives($chatId, $query)
    {
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
                $message .= "• Coba kata kunci yang lebih umum\n\n";
                $message .= "🔧 <b>Perintah tersedia:</b>\n";
                $message .= "/help - Bantuan\n";
                $message .= "/status - Status sistem\n";
                $message .= "/cari [kata kunci] - Cari arsip";
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

            $this->telegramService->sendMessage($message, $chatId);

        } catch (\Exception $e) {
            Log::error('Telegram search error', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);

            $errorMessage = "❌ <b>ERROR PENCARIAN</b>\n\n";
            $errorMessage .= "Terjadi kesalahan saat mencari arsip.\n";
            $errorMessage .= "Silakan coba lagi nanti.";

            $this->telegramService->sendMessage($errorMessage, $chatId);
        }
    }

    protected function sendHelpMessage($chatId)
    {
        $message = "🤖 <b>BOT ARSIP - BANTUAN</b>\n\n";
        $message .= "🔧 <b>Perintah yang tersedia:</b>\n\n";
        $message .= "🔍 <b>Pencarian:</b>\n";
        $message .= "• Ketik kata kunci langsung untuk mencari arsip\n";
        $message .= "• /cari [kata kunci] - Cari arsip dengan perintah\n";
        $message .= "• Contoh: /cari 001/2024\n\n";
        $message .= "📊 <b>Informasi:</b>\n";
        $message .= "• /status - Status sistem arsip\n";
        $message .= "• /help - Tampilkan bantuan ini\n\n";
        $message .= "💡 <b>Tips pencarian:</b>\n";
        $message .= "• Gunakan nomor arsip (contoh: 001/2024)\n";
        $message .= "• Gunakan kata kunci dari uraian arsip\n";
        $message .= "• Gunakan nomor file\n";
        $message .= "• Coba kata kunci yang lebih spesifik\n\n";
        $message .= "📱 <b>Contoh penggunaan:</b>\n";
        $message .= "• Ketik: <code>surat keputusan</code>\n";
        $message .= "• Ketik: <code>/cari 001/2024</code>\n";
        $message .= "• Ketik: <code>kepegawaian</code>";

        $this->telegramService->sendMessage($message, $chatId);
    }

    protected function sendStatusMessage($chatId)
    {
        try {
            $totalArchives = \App\Models\Archive::count();
            $activeArchives = \App\Models\Archive::where('status', 'Aktif')->count();
            $inactiveArchives = \App\Models\Archive::where('status', 'Inaktif')->count();
            $permanentArchives = \App\Models\Archive::where('status', 'Permanen')->count();
            $destroyedArchives = \App\Models\Archive::where('status', 'Musnah')->count();

            $message = "📊 <b>STATUS SISTEM ARSIP</b>\n\n";
            $message .= "📁 <b>Total Arsip:</b> {$totalArchives}\n";
            $message .= "🟢 <b>Aktif:</b> {$activeArchives}\n";
            $message .= "🟡 <b>Inaktif:</b> {$inactiveArchives}\n";
            $message .= "🔵 <b>Permanen:</b> {$permanentArchives}\n";
            $message .= "🔴 <b>Musnah:</b> {$destroyedArchives}\n\n";
            $message .= "⏰ <b>Update:</b> " . now()->format('d/m/Y H:i:s') . "\n";
            $message .= "🟢 <b>Status:</b> Sistem berjalan normal";

            $this->telegramService->sendMessage($message, $chatId);

        } catch (\Exception $e) {
            Log::error('Telegram status error', [
                'error' => $e->getMessage()
            ]);

            $errorMessage = "❌ <b>ERROR STATUS</b>\n\n";
            $errorMessage .= "Terjadi kesalahan saat mengambil status sistem.\n";
            $errorMessage .= "Silakan coba lagi nanti.";

            $this->telegramService->sendMessage($errorMessage, $chatId);
        }
    }

    protected function sendUnknownCommandMessage($chatId)
    {
        $message = "❓ <b>PERINTAH TIDAK DIKENAL</b>\n\n";
        $message .= "Perintah yang Anda ketik tidak dikenali.\n\n";
        $message .= "🔧 <b>Perintah yang tersedia:</b>\n";
        $message .= "• /help - Bantuan\n";
        $message .= "• /status - Status sistem\n";
        $message .= "• /cari [kata kunci] - Cari arsip\n\n";
        $message .= "💡 <b>Atau ketik kata kunci langsung untuk mencari arsip!</b>";

        $this->telegramService->sendMessage($message, $chatId);
    }
}
