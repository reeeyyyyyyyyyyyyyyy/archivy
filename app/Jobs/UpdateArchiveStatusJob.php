<?php

namespace App\Jobs;

use App\Models\Archive;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateArchiveStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('UpdateArchiveStatusJob: Starting archive status update job');

        $todayString = today()->toDateString();
        Log::info('UpdateArchiveStatusJob: Today date string: ' . $todayString);

        // 1. Promote Aktif → Inaktif (only for non-manually overridden archives)
        $activeToInactive = Archive::aktif()
            ->whereRaw('DATE(transition_active_due) <= ?', [$todayString])
            ->where('manual_status_override', false) // Exclude manually overridden archives
            ->get();

        Log::info('UpdateArchiveStatusJob: Found ' . $activeToInactive->count() . ' archives to transition from Aktif to Inaktif (excluding manual overrides)');

        foreach ($activeToInactive as $archive) {
            $oldStatus = $archive->status;
            $archive->update(['status' => 'Inaktif']);
            Log::info('UpdateArchiveStatusJob: Archive ID ' . $archive->id . ' transitioned from ' . $oldStatus . ' to Inaktif (due: ' . $archive->transition_active_due->toDateString() . ')');

            // Send Telegram notification for status transition
            try {
                $telegramService = app(TelegramService::class);
                $telegramService->sendStatusTransitionNotification($archive, $oldStatus, 'Inaktif');
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification for status transition', [
                    'error' => $e->getMessage(),
                    'archive_id' => $archive->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'Inaktif'
                ]);
            }
        }

        // 2. Promote Inaktif → Permanen / Musnah based on classification's nasib_akhir (only for non-manually overridden archives)
        $inactiveToFinal = Archive::inaktif()
            ->whereRaw('DATE(transition_inactive_due) <= ?', [$todayString])
            ->where('manual_status_override', false) // Exclude manually overridden archives
            ->with('classification')
            ->get();

        Log::info('UpdateArchiveStatusJob: Found ' . $inactiveToFinal->count() . ' archives to transition from Inaktif to final status (excluding manual overrides)');

        foreach ($inactiveToFinal as $archive) {
            // Determine final status based on category and classification
            $finalStatus = 'Permanen'; // Default

            if ($archive->category && $archive->category->nama_kategori === 'LAINNYA') {
                // For LAINNYA category, use manual_nasib_akhir
                $finalStatus = match (true) {
                    str_starts_with($archive->manual_nasib_akhir, 'Musnah') => 'Musnah',
                    $archive->manual_nasib_akhir === 'Permanen' => 'Permanen',
                    $archive->manual_nasib_akhir === 'Dinilai Kembali' => 'Dinilai Kembali',
                    default => 'Permanen'
                };
            } else {
                // For JRA categories, use classification nasib_akhir
                $finalStatus = match (true) {
                    str_starts_with($archive->classification->nasib_akhir, 'Musnah') => 'Musnah',
                    $archive->classification->nasib_akhir === 'Permanen' => 'Permanen',
                    $archive->classification->nasib_akhir === 'Dinilai Kembali' => 'Permanen',
                    default => 'Permanen'
                };
            }

            $oldStatus = $archive->status;
            $archive->update(['status' => $finalStatus]);
            Log::info('UpdateArchiveStatusJob: Archive ID ' . $archive->id . ' transitioned from ' . $oldStatus . ' to ' . $finalStatus . ' based on classification nasib_akhir: ' . $archive->classification->nasib_akhir . ' (due: ' . $archive->transition_inactive_due->toDateString() . ')');

            // Send Telegram notification for status transition
            try {
                $telegramService = app(TelegramService::class);
                $telegramService->sendStatusTransitionNotification($archive, $oldStatus, $finalStatus);
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification for status transition', [
                    'error' => $e->getMessage(),
                    'archive_id' => $archive->id,
                    'old_status' => $oldStatus,
                    'new_status' => $finalStatus
                ]);
            }
        }

        Log::info('UpdateArchiveStatusJob: Archive status update job completed');
    }
}
