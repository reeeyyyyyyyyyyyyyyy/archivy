<?php

namespace App\Jobs;

use App\Models\Archive;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        // 1. Promote Active → Inaktif
        Archive::aktif() // Using scope defined in Archive model
            ->whereDate('transition_active_due', '<=', today())
            ->update(['status' => 'Inaktif']); // Make sure 'Inaktif' matches enum value

        // 2. Promote Inaktif → Permanen / Musnah
        Archive::inaktif() // Using scope defined in Archive model
            ->whereDate('transition_inactive_due', '<=', today())
            ->each(function ($archive) {
                // Ensure status values match enum: 'Permanen' and 'Musnah'
                $archive->status = ($archive->retention_inactive === 0)
                    ? 'Musnah' // 'Musnah' matches enum
                    : 'Permanen'; // Changed from 'inaktif_permanen' to 'Permanen'
                $archive->save();
            });
    }
}