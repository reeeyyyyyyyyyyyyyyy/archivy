<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncRelatedArchivesCommand extends Command
{
    protected $signature = 'archives:sync-related {--force : Force sync even if no changes detected}';
    protected $description = 'Synchronize related archives automatically';

    public function handle()
    {
        $this->info('Starting automatic synchronization of related archives...');
        $this->newLine();

        // Get all archives that need to be grouped
        $archives = Archive::orderBy('created_at')->get();

        // Group archives by category, classification, and lampiran_surat
        $groups = $archives->groupBy(function ($archive) {
            return $archive->category_id . '-' . $archive->classification_id . '-' . $archive->lampiran_surat;
        });

        $totalGroups = $groups->count();
        $this->info("Found {$totalGroups} groups of related archives");
        $this->newLine();

        $syncedCount = 0;
        $errors = [];

        foreach ($groups as $groupKey => $groupArchives) {
            if ($groupArchives->count() <= 1) {
                continue; // Skip single archives
            }

            try {
                $this->line("Processing group: {$groupKey} ({$groupArchives->count()} archives)");

                // Find the oldest archive as parent
                $parentArchive = $groupArchives->sortBy('kurun_waktu_start')->first();

                // Check if parent is already correctly set
                $currentParent = $groupArchives->where('is_parent', true)->first();

                if (!$currentParent || $currentParent->id !== $parentArchive->id) {
                    // Update parent
                    $parentArchive->update([
                        'is_parent' => true,
                        'parent_archive_id' => null
                    ]);

                    // Update all other archives as children
                    foreach ($groupArchives as $archive) {
                        if ($archive->id !== $parentArchive->id) {
                            $archive->update([
                                'is_parent' => false,
                                'parent_archive_id' => $parentArchive->id
                            ]);
                        }
                    }

                    $this->line("  ✓ Set archive ID {$parentArchive->id} as parent (year: {$parentArchive->kurun_waktu_start->format('Y')})");
                    $syncedCount++;
                } else {
                    $this->line("  - Parent already correctly set");
                }

            } catch (\Exception $e) {
                $error = "Error processing group {$groupKey}: " . $e->getMessage();
                $errors[] = $error;
                $this->error("  ✗ " . $error);
            }
        }

        $this->newLine();
        $this->info("Synchronization completed!");
        $this->info("  - Groups processed: {$totalGroups}");
        $this->info("  - Groups synced: {$syncedCount}");
        $this->info("  - Errors: " . count($errors));

        if (count($errors) > 0) {
            $this->newLine();
            $this->error("Errors encountered:");
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }

        // Show final statistics
        $this->newLine();
        $parentCount = Archive::where('is_parent', true)->count();
        $childCount = Archive::whereNotNull('parent_archive_id')->count();
        $standaloneCount = Archive::whereNull('parent_archive_id')->where('is_parent', false)->count();

        $this->info("Final Statistics:");
        $this->info("  - Parent archives: {$parentCount}");
        $this->info("  - Child archives: {$childCount}");
        $this->info("  - Standalone archives: {$standaloneCount}");

        return 0;
    }
}
