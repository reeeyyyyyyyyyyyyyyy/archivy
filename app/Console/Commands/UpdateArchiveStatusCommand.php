<?php

namespace App\Console\Commands;

use App\Jobs\UpdateArchiveStatusJob;
use App\Models\Archive;
use Illuminate\Console\Command;

class UpdateArchiveStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:update-status {--test : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update archive statuses based on their retention dates or preview changes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('test')) {
            $this->info('Running in TEST mode - no changes will be made');
            $this->previewChanges();
        } else {
            UpdateArchiveStatusJob::dispatch();
            $this->info('Job to update archive statuses has been dispatched.');
        }
    }

    private function previewChanges()
    {
        $todayString = today()->toDateString();

        // Check active to inactive transitions (excluding manual overrides)
        $activeToInactive = Archive::aktif()
            ->whereRaw('DATE(transition_active_due) <= ?', [$todayString])
            ->where('manual_status_override', false)
            ->get();

        $this->info("Archives to transition from Aktif to Inaktif: " . $activeToInactive->count());
        foreach ($activeToInactive as $archive) {
            $this->line("  - Archive ID {$archive->id}: {$archive->description} (due: {$archive->transition_active_due->toDateString()})");
        }

        // Check inactive to final transitions (excluding manual overrides)
        $inactiveToFinal = Archive::inaktif()
            ->whereRaw('DATE(transition_inactive_due) <= ?', [$todayString])
            ->where('manual_status_override', false)
            ->with('category')
            ->get();

        $this->info("Archives to transition from Inaktif to final status: " . $inactiveToFinal->count());
        foreach ($inactiveToFinal as $archive) {
            $finalStatus = match (true) {
                str_starts_with($archive->category->nasib_akhir, 'Musnah') => 'Musnah',
                $archive->category->nasib_akhir === 'Permanen' => 'Permanen',
                $archive->category->nasib_akhir === 'Dinilai Kembali' => 'Permanen',
                default => 'Permanen'
            };
            $this->line("  - Archive ID {$archive->id}: {$archive->description} -> {$finalStatus} (due: {$archive->transition_inactive_due->toDateString()})");
        }

        // Show manually overridden archives
        $manualOverrides = Archive::where('manual_status_override', true)->count();
        if ($manualOverrides > 0) {
            $this->warn("Archives with manual status override (will be skipped): {$manualOverrides}");
        }

        if ($activeToInactive->count() === 0 && $inactiveToFinal->count() === 0) {
            $this->info("No archives need status updates at this time.");
            if ($manualOverrides > 0) {
                $this->info("Note: {$manualOverrides} archives have manual overrides and are protected from automatic changes.");
            }
        }
    }
}
