<?php

namespace App\Jobs;

use App\Models\Archive;
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
        }

        // 2. Promote Inaktif → Permanen / Musnah based on category's nasib_akhir (only for non-manually overridden archives)
        $inactiveToFinal = Archive::inaktif()
            ->whereRaw('DATE(transition_inactive_due) <= ?', [$todayString])
            ->where('manual_status_override', false) // Exclude manually overridden archives
            ->with('category')
            ->get();
            
        Log::info('UpdateArchiveStatusJob: Found ' . $inactiveToFinal->count() . ' archives to transition from Inaktif to final status (excluding manual overrides)');

        foreach ($inactiveToFinal as $archive) {
            // Use category's nasib_akhir to determine final status
            // Handle different types of musnah and permanen
            $finalStatus = match (true) {
                str_starts_with($archive->category->nasib_akhir, 'Musnah') => 'Musnah',
                $archive->category->nasib_akhir === 'Permanen' => 'Permanen',
                $archive->category->nasib_akhir === 'Dinilai Kembali' => 'Permanen', // Default to Permanen for manual review
                default => 'Permanen'
            };
            
            $oldStatus = $archive->status;
            $archive->update(['status' => $finalStatus]);
            Log::info('UpdateArchiveStatusJob: Archive ID ' . $archive->id . ' transitioned from ' . $oldStatus . ' to ' . $finalStatus . ' based on category nasib_akhir: ' . $archive->category->nasib_akhir . ' (due: ' . $archive->transition_inactive_due->toDateString() . ')');
        }
        
        Log::info('UpdateArchiveStatusJob: Archive status update job completed');
    }
}