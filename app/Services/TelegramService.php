<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Archive;
use App\Models\User;

class TelegramService
{
    protected $botToken;
    protected $apiUrl;
    protected $stoppedUsers = [];
    protected $searchMode = [];

    /**
     * Get stopped users from cache
     */
    protected function getStoppedUsers()
    {
        return cache('telegram_stopped_users', []);
    }

    /**
     * Set stopped users to cache
     */
    protected function setStoppedUsers($stoppedUsers)
    {
        cache(['telegram_stopped_users' => $stoppedUsers], now()->addDays(30));
    }

    /**
     * Get search mode from cache
     */
    protected function getSearchMode($chatId)
    {
        return cache("telegram_search_mode_{$chatId}", null);
    }

    /**
     * Set search mode to cache
     */
    protected function setSearchMode($chatId, $mode)
    {
        cache(["telegram_search_mode_{$chatId}" => $mode], now()->addHours(1));
    }

    /**
     * Clear search mode from cache
     */
    protected function clearSearchMode($chatId)
    {
        cache()->forget("telegram_search_mode_{$chatId}");
    }

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";

        // Load stopped users from cache
        $this->stoppedUsers = $this->getStoppedUsers();
    }

    public function sendMessage($chatId, $text, $replyMarkup = null)
    {
        try {
            $data = [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ];

            if ($replyMarkup) {
                $data['reply_markup'] = $replyMarkup;
            }

            $response = Http::post("{$this->apiUrl}/sendMessage", $data);

            if ($response->successful()) {
                Log::info('Telegram message sent successfully', ['chat_id' => $chatId]);
                return true;
            } else {
                Log::error('Failed to send Telegram message', [
                    'chat_id' => $chatId,
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error sending Telegram message', [
                'chat_id' => $chatId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendMessageWithKeyboard($chatId, $text, $keyboard = null)
    {
        $replyMarkup = null;

        if ($keyboard && !empty($keyboard)) {
            $replyMarkup = [
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ];
        } else {
            // Jika keyboard kosong, hapus keyboard (remove_keyboard)
            $replyMarkup = [
                'remove_keyboard' => true
            ];
        }

        return $this->sendMessage($chatId, $text, json_encode($replyMarkup));
    }

    public function getMainKeyboard()
    {
        return [
            [['text' => '🔍 Cari Arsip']],
            [['text' => '📊 Status Sistem']],
            [['text' => '📋 Laporan Retensi']],
            [['text' => '❓ Bantuan']]
        ];
    }

    public function getSearchKeyboard()
    {
        return [
            [['text' => '🔙 Menu Utama']],
            [['text' => '📝 Cari berdasarkan deskripsi']],
            [['text' => '🏷️ Cari berdasarkan kategori']],
            [['text' => '📅 Cari berdasarkan tahun']]
        ];
    }

    public function handleWebhook($data)
    {
        try {
            if (isset($data['message'])) {
                $this->handleMessage($data['message']);
            } elseif (isset($data['callback_query'])) {
                $this->handleCallbackQuery($data['callback_query']);
            }
        } catch (\Exception $e) {
            Log::error('Error handling Telegram webhook', ['error' => $e->getMessage()]);
        }
    }

    protected function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $from = $message['from'] ?? [];

        if (empty($text)) {
            return;
        }

        // Check if user has stopped the bot (ONLY /start command allowed)
        if (in_array($chatId, $this->stoppedUsers)) {
            Log::info('User is stopped, checking command', [
                'chat_id' => $chatId,
                'text' => $text,
                'is_start' => str_starts_with($text, '/start')
            ]);

            if (str_starts_with($text, '/start')) {
                Log::info('Restarting bot for stopped user', ['chat_id' => $chatId]);
                // Remove from stopped users and reset search mode
                $this->stoppedUsers = array_diff($this->stoppedUsers, [$chatId]);
                $this->setStoppedUsers($this->stoppedUsers);
                $this->clearSearchMode($chatId);
                $this->handleCommand($chatId, $text, $from);
            } else {
                // Log ignored messages for stopped users
                Log::info('Message ignored for stopped user', [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'stopped_users' => $this->stoppedUsers
                ]);
                // Send warning that bot is stopped
                $this->sendMessage($chatId, "🚫 <b>Bot ARSIPIN Dihentikan</b>\n\nBot tidak akan merespon apapun kecuali command /start.\n\n💡 Ketik /start untuk memulai kembali bot.");
            }
            return; // Bot tidak merespon apapun untuk user yang sudah stop
        }

        // Handle commands FIRST (priority)
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $text, $from);
            return;
        }

        // Handle menu button clicks SECOND
        if ($this->isMenuButton($text)) {
            $this->handleMenuButton($chatId, $text);
            return;
        }

        // Handle regular messages (search) LAST - only if not command/button
        $this->handleRegularMessage($chatId, $text, $from);
    }

        protected function isMenuButton($text)
    {
        $menuButtons = [
            '🔍 Cari Arsip',
            '📊 Status Sistem',
            '📋 Laporan Retensi',
            '❓ Bantuan',
            '🏷️ Kategori',
            '📅 Tahun',
            '🔍 Kata Kunci',
            '🔙 Menu Utama'
        ];

        return in_array($text, $menuButtons);
    }

    protected function handleMenuButton($chatId, $text)
    {
        switch ($text) {
            case '🔍 Cari Arsip':
                $this->showSearchOptions($chatId);
                break;
            case '📊 Status Sistem':
                $this->sendSystemStatus($chatId);
                break;
            case '📋 Laporan Retensi':
                $this->sendRetentionAlerts($chatId);
                break;
            case '❓ Bantuan':
                $this->sendHelp($chatId);
                break;

            case '🏷️ Kategori':
                $this->setSearchMode($chatId, 'kategori');
                Log::info('Search mode set to kategori', ['chat_id' => $chatId, 'search_mode' => $this->getSearchMode($chatId)]);
                $this->sendMessage($chatId, "🏷️ <b>Pencarian berdasarkan Kategori</b>\n\nKetik nama kategori arsip yang ingin dicari (contoh: UMUM, PEREKONOMIAN):");
                break;
            case '📅 Tahun':
                $this->setSearchMode($chatId, 'tahun');
                Log::info('Search mode set to tahun', ['chat_id' => $chatId, 'search_mode' => $this->getSearchMode($chatId)]);
                $this->sendMessage($chatId, "📅 <b>Pencarian berdasarkan Tahun</b>\n\nKetik tahun arsip yang ingin dicari (contoh: 2023):");
                break;
            case '🔍 Kata Kunci':
                $this->setSearchMode($chatId, 'kata_kunci');
                Log::info('Search mode set to kata_kunci', ['chat_id' => $chatId, 'search_mode' => $this->getSearchMode($chatId)]);
                $this->sendMessage($chatId, "🔍 <b>Pencarian berdasarkan Kata Kunci</b>\n\nKetik kata kunci arsip yang ingin dicari (minimal 3 karakter):");
                break;
            case '🔙 Menu Utama':
                // Reset search mode when returning to main menu
                $this->clearSearchMode($chatId);
                $this->sendWelcomeMessage($chatId, ['first_name' => 'User']);
                break;
            default:
                $this->sendMessage($chatId, "❓ Menu tidak dikenal. Gunakan /help untuk bantuan.");
                break;
        }
    }

    protected function handleCommand($chatId, $text, $from)
    {
        $command = strtolower(trim($text));

        switch ($command) {
            case '/start':
                $this->sendWelcomeMessage($chatId, $from);
                break;
            case '/stop':
                $this->sendStopMessage($chatId);
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
                $this->sendWelcomeMessage($chatId, $from);
                break;
            default:
                $this->sendMessage($chatId, "❓ Command tidak dikenal. Gunakan /help untuk bantuan.");
                break;
        }
    }

    protected function handleRegularMessage($chatId, $text, $from)
    {
        // Debug: Log search mode status
        $currentSearchMode = $this->getSearchMode($chatId);
        Log::info('handleRegularMessage called', [
            'chat_id' => $chatId,
            'text' => $text,
            'search_mode' => $currentSearchMode ?? 'not_set',
            'stopped_users' => $this->stoppedUsers
        ]);

        // Only handle as search if user has selected search mode
        if ($currentSearchMode && !str_starts_with($text, '/') && !$this->isMenuButton($text)) {
            // Check minimum length based on search mode
            $minLength = ($currentSearchMode === 'kategori') ? 1 : 3;

            if (strlen($text) >= $minLength) {
                Log::info('Performing search with mode', [
                    'chat_id' => $chatId,
                    'mode' => $currentSearchMode,
                    'keyword' => $text,
                    'min_length' => $minLength
                ]);
                $this->searchArchives($chatId, $text);
                // Reset search mode after search
                $this->clearSearchMode($chatId);
            } else {
                $this->sendMessage($chatId, "🔍 Masukkan minimal {$minLength} karakter untuk pencarian ini.");
            }
        } else if (strlen($text) >= 3 && !str_starts_with($text, '/') && !$this->isMenuButton($text)) {
            // User typed keyword without selecting search mode
            Log::info('User typed keyword without search mode', [
                'chat_id' => $chatId,
                'keyword' => $text
            ]);
            $this->sendMessage($chatId, "🔍 <b>Pencarian Arsip</b>\n\nSilakan pilih jenis pencarian terlebih dahulu:\n\n• Klik '🔍 Cari Arsip' untuk memulai pencarian\n• Pilih jenis pencarian yang diinginkan\n• Kemudian ketik kata kunci yang ingin dicari");
        } else if (strlen($text) < 3 && !str_starts_with($text, '/') && !$this->isMenuButton($text)) {
            $this->sendMessage($chatId, "🔍 Masukkan minimal 3 karakter untuk mencari arsip.");
        }
        // If it's a command or menu button, it's already handled above
    }

    protected function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'] ?? '';

        switch ($data) {
            case 'search_description':
                $this->sendMessage($chatId, "🔍 Masukkan deskripsi arsip yang ingin dicari:");
                break;
            case 'search_category':
                $this->sendMessage($chatId, "🏷️ Masukkan nama kategori arsip:");
                break;
            case 'search_year':
                $this->sendMessage($chatId, "📅 Masukkan tahun arsip (contoh: 2023):");
                break;
            default:
                $this->sendMessage($chatId, "❓ Opsi tidak valid.");
                break;
        }
    }

    public function sendWelcomeMessage($chatId, $from)
    {
        // Remove user from stopped list when starting
        $this->stoppedUsers = array_diff($this->stoppedUsers, [$chatId]);
        $this->setStoppedUsers($this->stoppedUsers);

        // Reset search mode when starting
        $this->clearSearchMode($chatId);

        $firstName = $from['first_name'] ?? 'User';
        $text = "👋 <b>Selamat datang di ARSIPIN Bot!</b>\n\n";
        $text .= "Halo {$firstName}! Saya adalah bot asisten untuk sistem arsip DPMPTSP Jawa Timur.\n\n";
        $text .= "🔄 <b>Fitur yang tersedia:</b>\n";
        $text .= "• 🔍 Cari arsip berdasarkan kategori, tahun, atau kata kunci\n";
        $text .= "• 📊 Lihat status sistem dan laporan retensi\n";
        $text .= "• 📋 Dapatkan informasi arsip secara real-time\n\n";
        $text .= "💡 <b>Gunakan menu di bawah atau ketik /help untuk bantuan</b>";

        $this->sendMessageWithKeyboard($chatId, $text, $this->getMainKeyboard());
    }

    public function sendStopMessage($chatId)
    {
        // Add user to stopped list
        if (!in_array($chatId, $this->stoppedUsers)) {
            $this->stoppedUsers[] = $chatId;
            // Save to cache
            $this->setStoppedUsers($this->stoppedUsers);
        }

        // Clear search mode when stopping
        $this->clearSearchMode($chatId);

        $text = "🛑 <b>Bot ARSIPIN Dihentikan</b>\n\n";
        $text .= "Bot telah dihentikan dan tidak akan merespon pesan apapun.\n\n";
        $text .= "💡 <b>Untuk memulai kembali:</b>\n";
        $text .= "• Ketik /start untuk memulai bot\n";
        $text .= "• Bot akan menampilkan menu utama\n";
        $text .= "• Semua fitur akan tersedia kembali\n\n";
        $text .= "🚫 <b>Bot tidak akan merespon:</b>\n";
        $text .= "• Command apapun (kecuali /start)\n";
        $text .= "• Tombol menu\n";
        $text .= "• Kata kunci pencarian\n";
        $text .= "• Pesan apapun\n\n";
        $text .= "👋 <b>Terima kasih telah menggunakan ARSIPIN Bot!</b>";

        // Kirim pesan STOP dengan keyboard kosong (hapus semua tombol)
        $this->sendMessageWithKeyboard($chatId, $text, []);

        // Log bahwa bot dihentikan
        Log::info('Telegram bot stopped by user', ['chat_id' => $chatId]);
    }

    public function showSearchOptions($chatId)
    {
        $text = "🔍 <b>Pencarian Arsip</b>\n\n";
        $text .= "Pilih jenis pencarian yang ingin Anda lakukan:";

        $keyboard = [
            [['text' => '🏷️ Kategori']],
            [['text' => '📅 Tahun']],
            [['text' => '🔍 Kata Kunci']],
            [['text' => '🔙 Menu Utama']]
        ];

        $this->sendMessageWithKeyboard($chatId, $text, $keyboard);
    }

    public function searchArchives($chatId, $keyword)
    {
        try {
            $archives = Archive::with(['category', 'classification'])
                ->where('description', 'ILIKE', "%{$keyword}%")
                ->orWhere('index_number', 'ILIKE', "%{$keyword}%")
                ->orWhereHas('category', function ($query) use ($keyword) {
                    $query->where('nama_kategori', 'ILIKE', "%{$keyword}%");
                })
                ->orWhereHas('classification', function ($query) use ($keyword) {
                    $query->where('nama_klasifikasi', 'ILIKE', "%{$keyword}%");
                })
                ->limit(10)
                ->get();

            if ($archives->count() > 0) {
                $text = "🔍 <b>Hasil Pencarian: \"{$keyword}\"</b>\n\n";

                foreach ($archives as $archive) {
                    $text .= "📄 <b>{$archive->description}</b>\n";
                    $text .= "🏷️ Kategori: " . ($archive->category ? $archive->category->nama_kategori : 'Tidak ada') . "\n";
                    $text .= "📂 Klasifikasi: " . ($archive->classification ? $archive->classification->nama_klasifikasi : 'Tidak ada') . "\n";
                    $text .= "📅 Tahun: " . ($archive->year ?? 'Tidak ada') . "\n";
                    $text .= "📊 Status: " . ($archive->status ?? 'Tidak ada') . "\n";
                    $text .= "➖➖➖➖➖➖➖➖\n\n";
                }

                $text .= "📊 <b>Total ditemukan: {$archives->count()} arsip</b>";
            } else {
                $text = "❌ <b>Tidak ada arsip ditemukan</b>\n\n";
                $text .= "Kata kunci: \"{$keyword}\"\n";
                $text .= "💡 Coba gunakan kata kunci yang berbeda atau lebih spesifik.";
            }

            $this->sendMessage($chatId, $text);
        } catch (\Exception $e) {
            Log::error('Error searching archives', ['error' => $e->getMessage()]);
            $this->sendMessage($chatId, "❌ Terjadi kesalahan saat mencari arsip. Silakan coba lagi.");
        }
    }

    public function sendSystemStatus($chatId)
    {
        try {
            $totalArchives = Archive::count();
            $activeArchives = Archive::where('status', 'Aktif')->count();
            $inactiveArchives = Archive::where('status', 'Inaktif')->count();
            $permanentArchives = Archive::where('status', 'Permanen')->count();
            $destroyedArchives = Archive::where('status', 'Musnah')->count();

            $text = "📊 <b>Status Sistem ARSIPIN</b>\n\n";
            $text .= "📈 <b>Total Arsip:</b> {$totalArchives}\n";
            $text .= "🟢 <b>Arsip Aktif:</b> {$activeArchives}\n";
            $text .= "🟡 <b>Arsip Inaktif:</b> {$inactiveArchives}\n";
            $text .= "🟣 <b>Arsip Permanen:</b> {$permanentArchives}\n";
            $text .= "🔴 <b>Arsip Musnah:</b> {$destroyedArchives}\n\n";
            $text .= "🕐 <b>Update terakhir:</b> " . now()->format('d M Y H:i') . " WIB\n";
            $text .= "✅ <b>Status:</b> Sistem berjalan normal";

            $this->sendMessage($chatId, $text);
        } catch (\Exception $e) {
            Log::error('Error sending system status', ['error' => $e->getMessage()]);
            $this->sendMessage($chatId, "❌ Terjadi kesalahan saat mengambil status sistem.");
        }
    }

    public function sendRetentionAlerts($chatId)
    {
        try {
            $today = now();
            $period = 30; // 30 hari ke depan

            $text = "📋 <b>Laporan Retensi ARSIPIN</b>\n\n";
            $text .= "📅 <b>Periode:</b> {$today->format('d M Y')} - {$today->copy()->addDays($period)->format('d M Y')}\n\n";

            // Arsip yang akan berubah dari Aktif ke Inaktif
            $approachingInactive = Archive::where('status', 'Aktif')
                ->whereBetween('transition_active_due', [$today, $today->copy()->addDays($period)])
                ->with(['category', 'classification'])
                ->orderBy('transition_active_due')
                ->limit(5)
                ->get();

            // Arsip yang akan berubah dari Inaktif ke Permanen/Musnah
            $approachingFinal = Archive::where('status', 'Inaktif')
                ->whereBetween('transition_inactive_due', [$today, $today->copy()->addDays($period)])
                ->with(['category', 'classification'])
                ->orderBy('transition_inactive_due')
                ->limit(5)
                ->get();

            // Arsip yang akan masuk ke Berkas Perseorangan
            $approachingPersonalFiles = Archive::where('status', 'Inaktif')
                ->where('manual_nasib_akhir', 'Masuk ke Berkas Perseorangan')
                ->whereBetween('transition_inactive_due', [$today, $today->copy()->addDays($period)])
                ->with(['category', 'classification'])
                ->orderBy('transition_inactive_due')
                ->limit(5)
                ->get();

            $totalAlerts = $approachingInactive->count() + $approachingFinal->count() + $approachingPersonalFiles->count();

            if ($totalAlerts > 0) {
                $text .= "⚠️ <b>Total Alert:</b> {$totalAlerts} arsip memerlukan perhatian\n\n";

                // Aktif ke Inaktif
                if ($approachingInactive->count() > 0) {
                    $text .= "🔄 <b>Transisi Aktif → Inaktif:</b>\n";
                    foreach ($approachingInactive as $archive) {
                        $daysLeft = $today->diffInDays($archive->transition_active_due, false);
                        $text .= "• <b>{$archive->description}</b>\n";
                        $text .= "  📅 Jatuh tempo: {$archive->transition_active_due->format('d M Y')}\n";
                        $text .= "  ⏰ Sisa waktu: " . round($daysLeft) . " hari\n";
                        $text .= "  🏷️ Kategori: " . ($archive->category ? $archive->category->nama_kategori : 'N/A') . "\n";
                        $text .= "  ➖➖➖➖➖➖➖➖\n";
                    }
                    $text .= "\n";
                }

                // Inaktif ke Permanen/Musnah
                if ($approachingFinal->count() > 0) {
                    $text .= "🔄 <b>Transisi Inaktif → Final:</b>\n";
                    foreach ($approachingFinal as $archive) {
                        $daysLeft = $today->diffInDays($archive->transition_inactive_due, false);
                        $finalStatus = $this->getFinalStatus($archive);
                        $nasibAkhir = $this->getNasibAkhir($archive);

                        $text .= "• <b>{$archive->description}</b>\n";
                        $text .= "  📅 Jatuh tempo: {$archive->transition_inactive_due->format('d M Y')}\n";
                        $text .= "  ⏰ Sisa waktu: " . round($daysLeft) . " hari\n";
                        $text .= "  📊 Status berikutnya: {$finalStatus}\n";
                        $text .= "  🎯 Nasib Akhir: {$nasibAkhir}\n";
                        $text .= "  🏷️ Kategori: " . ($archive->category ? $archive->category->nama_kategori : 'N/A') . "\n";
                        $text .= "  ➖➖➖➖➖➖➖➖\n";
                    }
                    $text .= "\n";
                }

                // Masuk ke Berkas Perseorangan
                if ($approachingPersonalFiles->count() > 0) {
                    $text .= "📁 <b>Masuk ke Berkas Perseorangan:</b>\n";
                    foreach ($approachingPersonalFiles as $archive) {
                        $daysLeft = $today->diffInDays($archive->transition_inactive_due, false);
                        $text .= "• <b>{$archive->description}</b>\n";
                        $text .= "  📅 Jatuh tempo: {$archive->transition_inactive_due->format('d M Y')}\n";
                        $text .= "  ⏰ Sisa waktu: " . round($daysLeft) . " hari\n";
                        $text .= "  🏷️ Kategori: " . ($archive->category ? $archive->category->nama_kategori : 'N/A') . "\n";
                        $text .= "  ➖➖➖➖➖➖➖➖\n";
                    }
                }

                $text .= "\n💡 <b>Rekomendasi:</b> Segera evaluasi arsip yang akan berubah status!";
            } else {
                $text .= "✅ <b>Status Retensi:</b> Tidak ada arsip yang memerlukan evaluasi dalam {$period} hari ke depan.\n\n";
                $text .= "📊 <b>Ringkasan:</b>\n";
                $text .= "• Arsip Aktif: " . Archive::where('status', 'Aktif')->count() . "\n";
                $text .= "• Arsip Inaktif: " . Archive::where('status', 'Inaktif')->count() . "\n";
                $text .= "• Arsip Permanen: " . Archive::where('status', 'Permanen')->count() . "\n";
                $text .= "• Arsip Musnah: " . Archive::where('status', 'Musnah')->count() . "\n";
            }

            $this->sendMessage($chatId, $text);
        } catch (\Exception $e) {
            Log::error('Error sending retention alerts', ['error' => $e->getMessage()]);
            $this->sendMessage($chatId, "❌ Terjadi kesalahan saat mengambil laporan retensi.");
        }
    }

    protected function getFinalStatus($archive)
    {
        if ($archive->manual_nasib_akhir === 'Masuk ke Berkas Perseorangan') {
            return 'Masuk ke Berkas Perseorangan';
        }

        if ($archive->category && str_starts_with($archive->category->nasib_akhir, 'Musnah')) {
            return 'Musnah';
        }

        if ($archive->category && $archive->category->nasib_akhir === 'Permanen') {
            return 'Permanen';
        }

        return 'Permanen'; // Default
    }

    protected function getNasibAkhir($archive)
    {
        // Prioritas: manual_nasib_akhir > classification > category
        if ($archive->manual_nasib_akhir) {
            return $archive->manual_nasib_akhir;
        }

        if ($archive->classification && $archive->classification->nasib_akhir) {
            return $archive->classification->nasib_akhir;
        }

        if ($archive->category && $archive->category->nasib_akhir) {
            return $archive->category->nasib_akhir;
        }

        return 'Belum ditentukan';
    }

    public function sendHelp($chatId)
    {
        $text = "❓ <b>Bantuan Penggunaan Bot ARSIPIN</b>\n\n";
        $text .= "🔍 <b>Pencarian Arsip:</b>\n";
        $text .= "• Ketik deskripsi arsip yang ingin dicari\n";
        $text .= "• Minimal 3 karakter\n";
        $text .= "• Pencarian tidak membedakan huruf besar/kecil\n\n";

        $text .= "📱 <b>Menu Utama:</b>\n";
        $text .= "• 🔍 Cari Arsip - Mulai pencarian arsip\n";
        $text .= "• 📊 Status Sistem - Lihat status sistem\n";
        $text .= "• 📋 Laporan Retensi - Lihat peringatan retensi\n";
        $text .= "• ❓ Bantuan - Tampilkan bantuan ini\n\n";

        $text .= "⌨️ <b>Command Lengkap:</b>\n";
        $text .= "• /start - Mulai bot dengan keyboard interaktif\n";
        $text .= "• /stop - Hentikan bot (harus /start lagi)\n";
        $text .= "• /help - Tampilkan bantuan lengkap\n";
        $text .= "• /status - Status sistem arsip real-time\n";
        $text .= "• /search - Menu pencarian arsip dengan kategori\n";
        $text .= "• /retention - Alert arsip yang akan berubah status\n";
        $text .= "• /storage - Status kapasitas storage dan penyimpanan\n";
        $text .= "• /website - Status website dan sistem ARSIPIN\n";
        $text .= "• /keyboard - Tampilkan kembali keyboard tombol utama\n";
        $text .= "• /menu - Tampilkan menu utama dengan keyboard\n\n";

        $text .= "💡 <b>Tips Penggunaan:</b>\n";
        $text .= "• Gunakan menu tombol untuk navigasi cepat\n";
        $text .= "• Ketik langsung kata kunci untuk pencarian arsip\n";
        $text .= "• Command /keyboard untuk reset keyboard jika hilang\n";
        $text .= "• Semua data real-time dari sistem ARSIPIN";

        $this->sendMessage($chatId, $text);
    }

    public function sendStatusTransitionNotification($archive, $oldStatus, $newStatus)
    {
        // This method can be used to notify about status changes
        // Implementation depends on your notification requirements
        Log::info('Archive status transition', [
            'archive_id' => $archive->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);
    }

    public function sendStorageStatus($chatId)
    {
        try {
            $totalArchives = Archive::count();
            $activeArchives = Archive::where('status', 'Aktif')->count();
            $inactiveArchives = Archive::where('status', 'Inaktif')->count();
            $permanentArchives = Archive::where('status', 'Permanen')->count();
            $destroyedArchives = Archive::where('status', 'Musnah')->count();

            // Calculate storage usage (assuming each archive takes 1 unit)
            $totalCapacity = 10000; // Example total capacity
            $usedCapacity = $totalArchives;
            $availableCapacity = $totalCapacity - $usedCapacity;
            $usagePercentage = round(($usedCapacity / $totalCapacity) * 100, 1);

            $text = "🏗️ <b>Status Storage ARSIPIN</b>\n\n";
            $text .= "📊 <b>Kapasitas Penyimpanan:</b>\n";
            $text .= "• Total Kapasitas: {$totalCapacity} unit\n";
            $text .= "• Terpakai: {$usedCapacity} unit\n";
            $text .= "• Tersedia: {$availableCapacity} unit\n";
            $text .= "• Penggunaan: {$usagePercentage}%\n\n";

            $text .= "📁 <b>Distribusi Arsip:</b>\n";
            $text .= "• Aktif: {$activeArchives} arsip\n";
            $text .= "• Inaktif: {$inactiveArchives} arsip\n";
            $text .= "• Permanen: {$permanentArchives} arsip\n";
            $text .= "• Musnah: {$destroyedArchives} arsip\n\n";

            $text .= "🕐 <b>Update terakhir:</b> " . now()->format('d M Y H:i') . " WIB\n";

            if ($usagePercentage > 80) {
                $text .= "⚠️ <b>Peringatan:</b> Kapasitas storage hampir penuh!";
            } elseif ($usagePercentage > 60) {
                $text .= "💡 <b>Info:</b> Kapasitas storage sedang tinggi.";
            } else {
                $text .= "✅ <b>Status:</b> Kapasitas storage masih aman.";
            }

            $this->sendMessage($chatId, $text);
        } catch (\Exception $e) {
            Log::error('Error sending storage status', ['error' => $e->getMessage()]);
            $this->sendMessage($chatId, "❌ Terjadi kesalahan saat mengambil status storage.");
        }
    }

    public function sendWebsiteStatus($chatId)
    {
        try {
            $text = "🌐 <b>Status Website ARSIPIN</b>\n\n";
            $text .= "📱 <b>Fitur Utama:</b>\n";
            $text .= "• ✅ Dashboard Admin, Staff, Intern\n";
            $text .= "• ✅ Manajemen Arsip Lengkap\n";
            $text .= "• ✅ Sistem Storage Management\n";
            $text .= "• ✅ Laporan Retensi Otomatis\n";
            $text .= "• ✅ Export Excel & Label Generator\n";
            $text .= "• ✅ Operasi Massal & Bulk Update\n\n";

            $text .= "🔧 <b>Status Sistem:</b>\n";
            $text .= "• Website: Online & Berfungsi\n";
            $text .= "• Database: Terhubung & Aktif\n";
            $text .= "• Telegram Bot: Online & Responsif\n";
            $text .= "• Backup: Otomatis & Terjadwal\n\n";

            $text .= "👥 <b>User Aktif:</b>\n";
            $text .= "• Role Admin: Manajemen Sistem\n";
            $text .= "• Role Staff: Manajemen Arsip\n";
            $text .= "• Role Intern: Input & Pengelolaan\n\n";

            $text .= "🕐 <b>Update terakhir:</b> " . now()->format('d M Y H:i') . " WIB\n";
            $text .= "✅ <b>Status:</b> Sistem ARSIPIN berjalan normal";

            $this->sendMessage($chatId, $text);
        } catch (\Exception $e) {
            Log::error('Error sending website status', ['error' => $e->getMessage()]);
            $this->sendMessage($chatId, "❌ Terjadi kesalahan saat mengambil status website.");
        }
    }

    /**
     * Send automatic retention alerts for archives approaching retention dates (3 days)
     */
    public function sendAutomaticRetentionAlerts()
    {
        try {
            $today = now();
            $threeDaysFromNow = $today->copy()->addDays(3);

            $text = "🚨 <b>ALERT RETENSI OTOMATIS</b>\n\n";
            $text .= "⚠️ <b>ARSIP YANG MENDATANGI RETENSI (3 HARI LAGI)</b>\n\n";
            $text .= "📅 <b>Tanggal:</b> {$today->format('d M Y')} - {$threeDaysFromNow->format('d M Y')}\n\n";

            $totalAlerts = 0;

            // Arsip Aktif → Inaktif (3 hari lagi)
            $activeToInactive = Archive::where('status', 'Aktif')
                ->whereBetween('transition_active_due', [$today, $threeDaysFromNow])
                ->with(['category', 'classification', 'box.rack'])
                ->orderBy('transition_active_due')
                ->get();

            if ($activeToInactive->count() > 0) {
                $text .= "🔄 <b>Transisi Aktif → Inaktif:</b>\n";
                foreach ($activeToInactive as $archive) {
                    $daysLeft = $today->diffInDays($archive->transition_active_due, false);
                    $text .= "• <b>{$archive->description}</b>\n";
                    $text .= "  📅 Jatuh tempo: {$archive->transition_active_due->format('d M Y')}\n";
                    $text .= "  ⏰ Sisa waktu: " . round($daysLeft) . " hari\n";
                    $text .= "  🏷️ Kategori: " . ($archive->category ? $archive->category->nama_kategori : 'N/A') . "\n";
                    if ($archive->box && $archive->box->rack) {
                        $text .= "  📍 Lokasi: {$archive->box->rack->name} - Box {$archive->box->name}\n";
                    }
                    $text .= "  ➖➖➖➖➖➖➖➖\n";
                    $totalAlerts++;
                }
                $text .= "\n";
            }

            // Arsip Inaktif → Final (3 hari lagi)
            $inactiveToFinal = Archive::where('status', 'Inaktif')
                ->whereBetween('transition_inactive_due', [$today, $threeDaysFromNow])
                ->with(['category', 'classification', 'box.rack'])
                ->orderBy('transition_inactive_due')
                ->get();

            if ($inactiveToFinal->count() > 0) {
                $text .= "🔄 <b>Transisi Inaktif → Final:</b>\n";
                foreach ($inactiveToFinal as $archive) {
                    $daysLeft = $today->diffInDays($archive->transition_inactive_due, false);
                    $finalStatus = $this->getFinalStatus($archive);
                    $nasibAkhir = $this->getNasibAkhir($archive);

                    $text .= "• <b>{$archive->description}</b>\n";
                    $text .= "  📅 Jatuh tempo: {$archive->transition_inactive_due->format('d M Y')}\n";
                    $text .= "  ⏰ Sisa waktu: " . round($daysLeft) . " hari\n";
                    $text .= "  📊 Status berikutnya: {$finalStatus}\n";
                    $text .= "  🎯 Nasib Akhir: {$nasibAkhir}\n";
                    $text .= "  🏷️ Kategori: " . ($archive->category ? $archive->category->nama_kategori : 'N/A') . "\n";
                    if ($archive->box && $archive->box->rack) {
                        $text .= "  📍 Lokasi: {$archive->box->rack->name} - Box {$archive->box->name}\n";
                    }
                    $text .= "  ➖➖➖➖➖➖➖➖\n";
                    $totalAlerts++;
                }
                $text .= "\n";
            }

            // Berkas Perseorangan (3 hari lagi)
            $personalFiles = Archive::where('status', 'Inaktif')
                ->where('manual_nasib_akhir', 'Masuk ke Berkas Perseorangan')
                ->whereBetween('transition_inactive_due', [$today, $threeDaysFromNow])
                ->with(['category', 'classification', 'box.rack'])
                ->orderBy('transition_inactive_due')
                ->get();

            if ($personalFiles->count() > 0) {
                $text .= "📁 <b>Masuk ke Berkas Perseorangan:</b>\n";
                foreach ($personalFiles as $archive) {
                    $daysLeft = $today->diffInDays($archive->transition_inactive_due, false);
                    $text .= "• <b>{$archive->description}</b>\n";
                    $text .= "  📅 Jatuh tempo: {$archive->transition_inactive_due->format('d M Y')}\n";
                    $text .= "  ⏰ Sisa waktu: " . round($daysLeft) . " hari\n";
                    $text .= "  🏷️ Kategori: " . ($archive->category ? $archive->category->nama_kategori : 'N/A') . "\n";
                    if ($archive->box && $archive->box->rack) {
                        $text .= "  📍 Lokasi: {$archive->box->rack->name} - Box {$archive->box->name}\n";
                    }
                    $text .= "  ➖➖➖➖➖➖➖➖\n";
                    $totalAlerts++;
                }
                $text .= "\n";
            }

            if ($totalAlerts == 0) {
                $text .= "✅ <b>Tidak ada arsip yang perlu perhatian dalam 3 hari ke depan.</b>\n\n";
            } else {
                $text .= "🚨 <b>Total Alert:</b> {$totalAlerts} arsip memerlukan perhatian segera!\n\n";
                $text .= "💡 <b>Rekomendasi:</b>\n";
                $text .= "• Segera review arsip yang mendekati retensi\n";
                $text .= "• Lakukan perubahan status sesuai jadwal\n";
                $text .= "• Update lokasi jika diperlukan\n";
                $text .= "• Dokumentasikan semua perubahan\n\n";
            }

            $text .= "🕐 <b>Alert ini dikirim otomatis pada:</b> " . now()->format('d M Y H:i') . " WIB\n";
            $text .= "📱 <b>Untuk info lebih detail:</b> Gunakan command /retention";

            // Send to all users who haven't stopped the bot
            $this->sendToAllActiveUsers($text);

        } catch (\Exception $e) {
            Log::error('Error sending automatic retention alerts: ' . $e->getMessage());
        }
    }

    /**
     * Send message to all active users (not stopped)
     */
    protected function sendToAllActiveUsers($text)
    {
        // Get all users from database who have used the bot
        // This is a simplified version - you might want to store user IDs in database
        $activeUsers = [1251337229]; // Add your chat ID here

        foreach ($activeUsers as $chatId) {
            if (!in_array($chatId, $this->stoppedUsers)) {
                try {
                    $this->sendMessage($chatId, $text);
                    sleep(1); // Delay to avoid rate limiting
                } catch (\Exception $e) {
                    Log::error("Failed to send alert to user {$chatId}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Send maintenance notification via Telegram
     */
    public function sendMaintenanceNotification()
    {
        try {
            $text = "🔧 <b>NOTIFIKASI PEMELIHARAAN SISTEM</b>\n\n";
            $text .= "📱 <b>Status:</b> Sistem ARSIPIN sedang dalam pemeliharaan rutin\n\n";
            $text .= "⏰ <b>Waktu:</b> " . now()->format('d M Y H:i') . " WIB\n\n";
            $text .= "📋 <b>Kegiatan Pemeliharaan:</b>\n";
            $text .= "• ✅ Backup database otomatis\n";
            $text .= "• ✅ Update status arsip\n";
            $text .= "• ✅ Sinkronisasi data terkait\n";
            $text .= "• ✅ Pembersihan cache sistem\n";
            $text .= "• ✅ Verifikasi integritas data\n\n";
            $text .= "💡 <b>Info:</b> Sistem tetap dapat diakses selama pemeliharaan\n";
            $text .= "🔄 <b>Update:</b> Akan ada notifikasi lagi setelah selesai\n\n";
            $text .= "📞 <b>Support:</b> Hubungi admin jika ada masalah";

            // Send to all active users
            $this->sendToAllActiveUsers($text);

            Log::info('Maintenance notification sent successfully');
            return true;

        } catch (\Exception $e) {
            Log::error('Error sending maintenance notification: ' . $e->getMessage());
            return false;
        }
    }
}
