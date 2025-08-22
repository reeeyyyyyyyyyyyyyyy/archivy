<?php

namespace App\Services;

use App\Models\Archive;
use App\Models\Classification;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TelegramService
{
    protected $botToken;
    protected $chatId;
    protected $baseUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
        $this->baseUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Kirim pesan dengan keyboard interaktif
     */
    public function sendMessageWithKeyboard($chatId, $text, $keyboard = null)
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }

        return $this->makeRequest('sendMessage', $data);
    }

    /**
     * Kirim pesan biasa
     */
    public function sendMessage($chatId, $text, $parseMode = 'HTML')
    {
        return $this->makeRequest('sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode
        ]);
    }

    /**
     * Buat keyboard utama
     */
    public function getMainKeyboard()
    {
        return [
            'keyboard' => [
                ['🔍 Cari Arsip', '📊 Status Sistem'],
                ['⏰ Retensi Mendekati', '📦 Kapasitas Storage'],
                ['❓ Bantuan', '🔄 Status Website']
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
            'selective' => false
        ];
    }

    /**
     * Buat keyboard pencarian
     */
    public function getSearchKeyboard()
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '📄 Surat Keputusan', 'callback_data' => 'search_surat_keputusan'],
                    ['text' => '📋 Kepegawaian', 'callback_data' => 'search_kepegawaian']
                ],
                [
                    ['text' => '📁 Keuangan', 'callback_data' => 'search_keuangan'],
                    ['text' => '🏢 Perizinan', 'callback_data' => 'search_perizinan']
                ],
                [
                    ['text' => '🔙 Kembali', 'callback_data' => 'main_menu']
                ]
            ]
        ];
    }

    /**
     * Handle webhook dari Telegram
     */
    public function handleWebhook($data)
    {
        try {
            if (isset($data['message'])) {
                $this->handleMessage($data['message']);
            } elseif (isset($data['callback_query'])) {
                $this->handleCallbackQuery($data['callback_query']);
            }
        } catch (\Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage());
        }
    }

    /**
     * Handle pesan masuk
     */
    protected function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $firstName = $message['from']['first_name'] ?? 'User';

        // Handle commands
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $text, $firstName);
            return;
        }

        // Handle keyboard buttons
        switch ($text) {
            case '🔍 Cari Arsip':
                $this->showSearchOptions($chatId);
                break;
            case '📊 Status Sistem':
                $this->sendSystemStatus($chatId);
                break;
            case '⏰ Retensi Mendekati':
                $this->sendRetentionAlerts($chatId);
                break;
            case '📦 Kapasitas Storage':
                $this->sendStorageStatus($chatId);
                break;
            case '❓ Bantuan':
                $this->sendHelp($chatId);
                break;
            case '🔄 Status Website':
                $this->sendWebsiteStatus($chatId);
                break;
            default:
                // Jika bukan command atau button, coba cari arsip
                if (strlen($text) > 2) {
                    $this->searchArchives($chatId, $text);
                } else {
                    $this->sendWelcomeMessage($chatId, $firstName);
                }
                break;
        }
    }

    /**
     * Handle callback query dari inline keyboard
     */
    protected function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];

        switch ($data) {
            case 'main_menu':
                $this->sendWelcomeMessage($chatId);
                break;
            case 'search_surat_keputusan':
                $this->searchArchives($chatId, 'surat keputusan');
                break;
            case 'search_kepegawaian':
                $this->searchArchives($chatId, 'kepegawaian');
                break;
            case 'search_keuangan':
                $this->searchArchives($chatId, 'keuangan');
                break;
            case 'search_perizinan':
                $this->searchArchives($chatId, 'perizinan');
                break;
        }
    }

    /**
     * Handle commands
     */
    protected function handleCommand($chatId, $command, $firstName)
    {
        switch ($command) {
            case '/start':
                $this->sendWelcomeMessage($chatId, $firstName);
                break;
            case '/help':
                $this->sendHelp($chatId);
                break;
            case '/status':
                $this->sendSystemStatus($chatId);
                break;
            case '/search':
                $this->showSearchOptions($chatId);
                break;
            case '/retention':
                $this->sendRetentionAlerts($chatId);
                break;
            case '/storage':
                $this->sendStorageStatus($chatId);
                break;
            case '/website':
                $this->sendWebsiteStatus($chatId);
                break;
            case '/keyboard':
            case '/menu':
                $this->sendWelcomeMessage($chatId, $firstName);
                break;
            default:
                $this->sendMessage($chatId, "❓ Command tidak dikenal. Gunakan <code>/help</code> untuk bantuan atau <code>/keyboard</code> untuk menampilkan menu.");
                break;
        }
    }

        /**
     * Kirim pesan selamat datang
     */
    public function sendWelcomeMessage($chatId, $firstName = null)
    {
        $greeting = $firstName ? "Halo {$firstName}! 👋" : "Halo! 👋";

        $text = "{$greeting}\n\n";
        $text .= "🤖 <b>ARSIPIN Bot</b> siap membantu Anda!\n\n";
        $text .= "📋 <b>Fitur yang tersedia:</b>\n";
        $text .= "• 🔍 <b>Cari Arsip</b> - Cari dokumen dengan kata kunci\n";
        $text .= "• 📊 <b>Status Sistem</b> - Informasi sistem arsip\n";
        $text .= "• ⏰ <b>Retensi Mendekati</b> - Arsip yang akan berubah status\n";
        $text .= "• 📦 <b>Kapasitas Storage</b> - Status penyimpanan\n";
        $text .= "• ❓ <b>Bantuan</b> - Panduan penggunaan\n\n";
        $text .= "⌨️ <b>Commands Cepat:</b>\n";
        $text .= "• <code>/start</code> - Mulai bot dengan keyboard\n";
        $text .= "• <code>/help</code> - Bantuan lengkap\n";
        $text .= "• <code>/status</code> - Status sistem\n";
        $text .= "• <code>/search</code> - Menu pencarian\n";
        $text .= "• <code>/retention</code> - Alert retensi\n";
        $text .= "• <code>/storage</code> - Status storage\n";
        $text .= "• <code>/website</code> - Status website\n\n";
        $text .= "💡 <b>Tips:</b> Ketik kata kunci langsung untuk mencari arsip!";

        $this->sendMessageWithKeyboard($chatId, $text, $this->getMainKeyboard());
    }

    /**
     * Tampilkan opsi pencarian
     */
    public function showSearchOptions($chatId)
    {
        $text = "🔍 <b>Pencarian Arsip</b>\n\n";
        $text .= "Pilih kategori pencarian atau ketik kata kunci langsung:\n\n";
        $text .= "💡 <b>Tips Pencarian:</b>\n";
        $text .= "• Ketik langsung: <code>surat keputusan</code>\n";
        $text .= "• Nomor arsip: <code>001/2024</code>\n";
        $text .= "• Kategori: <code>kepegawaian</code>\n";
        $text .= "• Klasifikasi: <code>surat menyurat</code>";

        $this->sendMessageWithKeyboard($chatId, $text, $this->getSearchKeyboard());
    }

    /**
     * Cari arsip berdasarkan kata kunci
     */
    public function searchArchives($chatId, $keyword)
    {
        try {
            $archives = Archive::where(function($query) use ($keyword) {
                $query->where('index_number', 'ILIKE', "%{$keyword}%")
                      ->orWhere('description', 'ILIKE', "%{$keyword}%")
                      ->orWhere('title', 'ILIKE', "%{$keyword}%")
                      ->orWhereHas('classification', function($q) use ($keyword) {
                          $q->where('nama_klasifikasi', 'ILIKE', "%{$keyword}%");
                      })
                      ->orWhereHas('category', function($q) use ($keyword) {
                          $q->where('nama_kategori', 'ILIKE', "%{$keyword}%");
                      });
            })
            ->with(['classification', 'category'])
            ->limit(10)
            ->get();

            if ($archives->count() > 0) {
                $text = "🔍 <b>Hasil Pencarian: \"{$keyword}\"</b>\n\n";

                foreach ($archives as $archive) {
                    $text .= "📄 <b>{$archive->index_number}</b>\n";
                    $text .= "📝 {$archive->description}\n";
                    $text .= "🏷️ " . ($archive->classification ? $archive->classification->nama_klasifikasi : 'N/A') . "\n";
                    $text .= "📂 " . ($archive->category ? $archive->category->nama_kategori : 'N/A') . "\n";
                    $text .= "📊 Status: {$archive->status}\n";

                    if ($archive->rack_number && $archive->box_number) {
                        $text .= "📍 Rak: {$archive->rack_number}, Box: {$archive->box_number}";
                        if ($archive->file_number) {
                            $text .= ", File: {$archive->file_number}";
                        }
                    }
                    $text .= "\n\n";
                }

                $text .= "💡 <b>Tips:</b> Gunakan kata kunci yang lebih spesifik untuk hasil yang lebih akurat.";
            } else {
                $text = "🔍 <b>Pencarian: \"{$keyword}\"</b>\n\n";
                $text .= "❌ Tidak ada arsip yang ditemukan.\n\n";
                $text .= "💡 <b>Saran pencarian:</b>\n";
                $text .= "• Coba kata kunci yang berbeda\n";
                $text .= "• Gunakan nomor arsip (contoh: 001/2024)\n";
                $text .= "• Gunakan kategori (Surat, Kepegawaian, dll)";
            }

            $this->sendMessage($chatId, $text);
        } catch (\Exception $e) {
            Log::error('Error searching archives: ' . $e->getMessage());
            $this->sendMessage($chatId, "❌ Terjadi kesalahan saat mencari arsip. Silakan coba lagi.");
        }
    }

    /**
     * Kirim status sistem
     */
    public function sendSystemStatus($chatId)
    {
        try {
            $totalArchives = Archive::count();
            $activeArchives = Archive::where('status', 'Aktif')->count();
            $inactiveArchives = Archive::where('status', 'Inaktif')->count();
            $permanentArchives = Archive::where('status', 'Permanen')->count();
            $destroyedArchives = Archive::where('status', 'Musnah')->count();

            $text = "📊 <b>Status Sistem ARSIPIN</b>\n\n";
            $text .= "📁 <b>Total Arsip:</b> {$totalArchives}\n";
            $text .= "🟢 <b>Aktif:</b> {$activeArchives}\n";
            $text .= "🟡 <b>Inaktif:</b> {$inactiveArchives}\n";
            $text .= "🔵 <b>Permanen:</b> {$permanentArchives}\n";
            $text .= "🔴 <b>Musnah:</b> {$destroyedArchives}\n\n";
            $text .= "⏰ <b>Update:</b> " . now()->format('d/m/Y H:i:s') . " WIB";

            $this->sendMessage($chatId, $text);
        } catch (\Exception $e) {
            Log::error('Error sending system status: ' . $e->getMessage());
            $this->sendMessage($chatId, "❌ Terjadi kesalahan saat mengambil status sistem.");
        }
    }

    /**
     * Kirim alert retensi
     */
    public function sendRetentionAlerts($chatId)
    {
        try {
            $nearRetention = Archive::where('status', 'Aktif')
                ->where('kurun_waktu_end', '<=', now()->addDays(30))
                ->where('kurun_waktu_end', '>', now())
                ->count();

            $text = "⏰ <b>Retensi Mendekati</b>\n\n";

            if ($nearRetention > 0) {
                $text .= "⚠️ <b>{$nearRetention} arsip</b> akan berubah status dalam 30 hari ke depan.\n\n";
                $text .= "📋 <b>Detail:</b>\n";

                $archives = Archive::where('status', 'Aktif')
                    ->where('kurun_waktu_end', '<=', now()->addDays(30))
                    ->where('kurun_waktu_end', '>', now())
                    ->with(['classification', 'category'])
                    ->limit(5)
                    ->get();

                foreach ($archives as $archive) {
                    $daysLeft = now()->diffInDays($archive->kurun_waktu_end, false);
                    $text .= "• {$archive->index_number} ({$daysLeft} hari lagi)\n";
                }

                if ($nearRetention > 5) {
                    $text .= "• ... dan " . ($nearRetention - 5) . " arsip lainnya\n";
                }
            } else {
                $text .= "✅ Tidak ada arsip yang akan berubah status dalam 30 hari ke depan.";
            }

            $this->sendMessage($chatId, $text);
        } catch (\Exception $e) {
            Log::error('Error sending retention alerts: ' . $e->getMessage());
            $this->sendMessage($chatId, "❌ Terjadi kesalahan saat mengambil data retensi.");
        }
    }

    /**
     * Kirim status storage
     */
    public function sendStorageStatus($chatId)
    {
        try {
            $text = "📦 <b>Status Storage</b>\n\n";
            $text .= "🏗️ <b>Rak:</b> " . \App\Models\StorageRack::count() . "\n";
            $text .= "📦 <b>Box:</b> " . \App\Models\StorageBox::count() . "\n";
            $text .= "📄 <b>Arsip dengan Lokasi:</b> " . Archive::whereNotNull('rack_number')->count() . "\n\n";
            $text .= "⏰ <b>Update:</b> " . now()->format('d/m/Y H:i:s') . " WIB";

            $this->sendMessage($chatId, $text);
        } catch (\Exception $e) {
            Log::error('Error sending storage status: ' . $e->getMessage());
            $this->sendMessage($chatId, "❌ Terjadi kesalahan saat mengambil status storage.");
        }
    }

    /**
     * Kirim bantuan
     */
    public function sendHelp($chatId)
    {
        $text = "❓ <b>Bantuan ARSIPIN Bot</b>\n\n";
        $text .= "🔍 <b>Cara Pencarian:</b>\n";
        $text .= "• Ketik kata kunci langsung (contoh: surat keputusan)\n";
        $text .= "• Gunakan nomor arsip (contoh: 001/2024)\n";
        $text .= "• Gunakan kategori (Surat, Kepegawaian, dll)\n\n";
        $text .= "⌨️ <b>Commands Cepat:</b>\n";
        $text .= "• <code>/start</code> - Mulai bot dengan keyboard\n";
        $text .= "• <code>/help</code> - Bantuan ini\n";
        $text .= "• <code>/status</code> - Status sistem arsip\n";
        $text .= "• <code>/search</code> - Menu pencarian\n";
        $text .= "• <code>/retention</code> - Alert retensi\n";
        $text .= "• <code>/storage</code> - Status storage\n";
        $text .= "• <code>/website</code> - Status website\n\n";
        $text .= "🎹 <b>Keyboard Tombol:</b>\n";
        $text .= "• <b>🔍 Cari Arsip</b> - Menu pencarian dengan tombol\n";
        $text .= "• <b>📊 Status Sistem</b> - Informasi sistem real-time\n";
        $text .= "• <b>⏰ Retensi Mendekati</b> - Alert arsip yang akan berubah status\n";
        $text .= "• <b>📦 Kapasitas Storage</b> - Status penyimpanan\n";
        $text .= "• <b>❓ Bantuan</b> - Panduan lengkap\n";
        $text .= "• <b>🔄 Status Website</b> - Status website dan sistem\n\n";
        $text .= "💡 <b>Tips:</b> Gunakan tombol keyboard untuk navigasi yang lebih mudah!";

        $this->sendMessageWithKeyboard($chatId, $text, $this->getMainKeyboard());
    }

    /**
     * Kirim status website
     */
    public function sendWebsiteStatus($chatId)
    {
        $text = "🔄 <b>Status Website ARSIPIN</b>\n\n";
        $text .= "✅ <b>Status:</b> Online\n";
        $text .= "🌐 <b>URL:</b> " . config('app.url') . "\n";
        $text .= "⏰ <b>Update:</b> " . now()->format('d/m/Y H:i:s') . " WIB\n";
        $text .= "🖥️ <b>Environment:</b> " . config('app.env') . "\n";
        $text .= "📱 <b>Versi:</b> 2.0";

        $this->sendMessage($chatId, $text);
    }

    /**
     * Kirim notifikasi status transition
     */
    public function sendStatusTransitionNotification($archive, $oldStatus, $newStatus)
    {
        if (!$this->chatId) return;

        $text = "🔄 <b>TRANSISI STATUS ARSIP</b>\n\n";
        $text .= "📁 <b>No. Arsip:</b> {$archive->index_number}\n";
        $text .= "📝 <b>Uraian:</b> " . substr($archive->description ?? 'N/A', 0, 100) . "\n";
        $text .= "📂 <b>Kategori:</b> " . ($archive->category->nama_kategori ?? 'N/A') . "\n";
        $text .= "🏷️ <b>Status Lama:</b> {$oldStatus}\n";
        $text .= "🆕 <b>Status Baru:</b> {$newStatus}\n";

        if ($archive->rack_number && $archive->box_number) {
            $text .= "🏗️ <b>Rak:</b> {$archive->rack_number}\n";
            $text .= "📦 <b>Box:</b> {$archive->box_number}\n";
            if ($archive->file_number) {
                $text .= "📄 <b>File:</b> {$archive->file_number}\n";
            }
        }

        $text .= "⏰ <b>Waktu Transisi:</b> " . now()->format('d/m/Y H:i:s') . "\n\n";
        $text .= "Transisi otomatis berdasarkan JRA Pergub 1 & 30";

        $this->sendMessage($this->chatId, $text);
    }

    /**
     * Test koneksi bot
     */
    public function testConnection()
    {
        try {
            $response = $this->makeRequest('getMe');
            if ($response && isset($response['ok']) && $response['ok']) {
                return [
                    'success' => true,
                    'bot_name' => $response['result']['first_name'],
                    'username' => $response['result']['username']
                ];
            }
            return ['success' => false, 'message' => 'Invalid response from Telegram'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Set webhook URL
     */
    public function setWebhook($url)
    {
        return $this->makeRequest('setWebhook', ['url' => $url]);
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook()
    {
        return $this->makeRequest('deleteWebhook');
    }

    /**
     * Make HTTP request to Telegram API
     */
    protected function makeRequest($method, $data = [])
    {
        try {
            $response = Http::post("{$this->baseUrl}/{$method}", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Telegram API error: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Telegram request error: " . $e->getMessage());
            return null;
        }
    }
}
