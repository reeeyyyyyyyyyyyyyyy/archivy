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
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Running UpdateArchiveStatusJob...');

        // 1. Promote Active → Inactive
        Archive::aktif()
            ->whereDate('transition_active_due', '<=', today())
            ->update(['status' => 'inaktif']);

        // 2. Promote Inactive → Permanent / Destroyed
        Archive::inaktif()
            ->whereDate('transition_inactive_due', '<=', today())
            ->whereHas('category', function ($query) {
                $query->where('nasib_akhir', '!=', 'Dinilai Kembali');
            })
            ->with('category')
            ->each(function ($archive) {
                if ($archive->category->nasib_akhir === 'Permanen') {
                    $archive->status = 'inaktif_permanen';
                } elseif ($archive->category->nasib_akhir === 'Musnah') {
                    $archive->status = 'musnah';
                }
                $archive->save();
            });
            
        Log::info('UpdateArchiveStatusJob finished.');
    }
}
