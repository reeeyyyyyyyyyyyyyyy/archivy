<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $token;
    protected $chatId;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    public function sendMessage($message)
    {
        try {
            $response = Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            if ($response->successful()) {
                Log::info('Telegram message sent successfully', ['message' => $message]);
                return true;
            } else {
                Log::error('Failed to send Telegram message', [
                    'response' => $response->body(),
                    'message' => $message
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Telegram service error', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);
            return false;
        }
    }

    public function sendRetentionAlert($archives)
    {
        $message = "🚨 <b>ALERT: Arsip Mendekati Retensi</b>\n\n";

        foreach ($archives as $archive) {
            $message .= "📁 <b>Arsip:</b> {$archive->index_number}\n";
            $message .= "📝 <b>Uraian:</b> {$archive->description}\n";
            $message .= "📅 <b>Jatuh Tempo:</b> {$archive->transition_active_due->format('d/m/Y')}\n";
            $message .= "⏰ <b>Sisa Waktu:</b> {$archive->transition_active_due->diffForHumans()}\n\n";
        }

        return $this->sendMessage($message);
    }

    public function sendStorageAlert($rack)
    {
        $message = "📦 <b>ALERT: Kapasitas Storage</b>\n\n";
        $message .= "🏗️ <b>Rack:</b> {$rack->name}\n";
        $message .= "📊 <b>Kapasitas:</b> {$rack->getUtilizationPercentage()}%\n";
        $message .= "📦 <b>Box Tersedia:</b> {$rack->getAvailableBoxesCount()}\n";
        $message .= "⚠️ <b>Status:</b> " . ($rack->getUtilizationPercentage() > 80 ? 'HAMPIR PENUH' : 'NORMAL') . "\n";

        return $this->sendMessage($message);
    }

    public function sendMaintenanceNotification()
    {
        $message = "🛠️ <b>MAINTENANCE NOTIFICATION</b>\n\n";
        $message .= "Sistem akan offline untuk pemeliharaan rutin:\n";
        $message .= "⏰ <b>Waktu:</b> 00:00 - 04:00 WIB\n";
        $message .= "⏱️ <b>Durasi:</b> Maksimal 30 menit\n";
        $message .= "📅 <b>Tanggal:</b> " . now()->addDay()->format('d/m/Y') . "\n\n";
        $message .= "Terima kasih atas kesabaran Anda! 🙏";

        return $this->sendMessage($message);
    }

    public function sendStatusTransitionNotification($archive, $oldStatus, $newStatus)
    {
        $statusText = match($newStatus) {
            'Inaktif' => '🔄 AKTIF → INAKTIF',
            'Permanen' => '📦 INAKTIF → PERMANEN',
            'Musnah' => '🗑️ INAKTIF → MUSNAH',
            default => '📋 STATUS BERUBAH'
        };

        $message = "🔄 <b>TRANSISI STATUS ARSIP</b>\n\n";
        $message .= "📁 <b>No. Arsip:</b> {$archive->index_number}\n";
        $message .= "📝 <b>Uraian:</b> {$archive->description}\n";
        $message .= "📂 <b>Kategori:</b> {$archive->category->nama_kategori}\n";
        $message .= "🏷️ <b>Status Lama:</b> {$oldStatus}\n";
        $message .= "🆕 <b>Status Baru:</b> {$newStatus}\n";

        // Add location information if available
        if ($archive->rack_number && $archive->box_number && $archive->file_number) {
            $message .= "🏗️ <b>Rak:</b> {$archive->rack_number}\n";
            $message .= "📦 <b>Box:</b> {$archive->box_number}\n";
            $message .= "📄 <b>File:</b> {$archive->file_number}\n";
        }

        $message .= "⏰ <b>Waktu Transisi:</b> " . now()->format('d/m/Y H:i:s');
        $message .= "\n\n<i>Transisi otomatis berdasarkan JRA Pergub 1 & 30</i>";

        return $this->sendMessage($message);
    }
}
