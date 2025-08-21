<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archive;
use App\Models\StorageBox;
use Illuminate\Support\Facades\DB;

class FixExistingFileNumbersCommand extends Command
{
    protected $signature = 'fix:existing-file-numbers {--dry-run : Show what would be changed without making changes}';
    protected $description = 'Fix existing file numbers to follow year-based numbering rules';

    public function handle()
    {
        $this->info('🔧 Fixing existing file numbers to follow year-based numbering rules...');

        if ($this->option('dry-run')) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        // Get all archives with storage locations
        $archives = Archive::whereNotNull('rack_number')
            ->whereNotNull('box_number')
            ->whereNotNull('file_number')
            ->orderBy('rack_number')
            ->orderBy('box_number')
            ->orderBy('classification_id')
            ->orderBy('kurun_waktu_start')
            ->get();

        $this->info("Found {$archives->count()} archives with storage locations");

        // Group archives by rack, box, classification, and year
        $groupedArchives = $archives->groupBy(function($archive) {
            return $archive->rack_number . '-' . $archive->box_number . '-' . $archive->classification_id . '-' . $archive->kurun_waktu_start->year;
        });

        $this->info("Grouped into " . $groupedArchives->count() . " groups");

        $fixedCount = 0;
        $errors = [];

        foreach ($groupedArchives as $groupKey => $groupArchives) {
            $this->info("Processing group: {$groupKey}");

            // Sort archives within group by kurun_waktu_start
            $sortedArchives = $groupArchives->sortBy('kurun_waktu_start');

            $expectedFileNumber = 1;

            foreach ($sortedArchives as $archive) {
                $oldFileNumber = $archive->file_number;

                if ($oldFileNumber !== $expectedFileNumber) {
                    if ($this->option('dry-run')) {
                        $this->line("   Would change: Archive {$archive->index_number} from file_number {$oldFileNumber} to {$expectedFileNumber}");
                    } else {
                        try {
                            $archive->update(['file_number' => $expectedFileNumber]);
                            $this->line("   ✅ Changed: Archive {$archive->index_number} from file_number {$oldFileNumber} to {$expectedFileNumber}");
                            $fixedCount++;
                        } catch (\Exception $e) {
                            $errorMsg = "Failed to update archive {$archive->index_number}: " . $e->getMessage();
                            $this->error("   ❌ {$errorMsg}");
                            $errors[] = $errorMsg;
                        }
                    }
                } else {
                    $this->line("   ✓ Archive {$archive->index_number} already has correct file_number {$expectedFileNumber}");
                }

                $expectedFileNumber++;
            }
        }

        if ($this->option('dry-run')) {
            $this->info("🔍 DRY RUN COMPLETED");
            $this->info("Would fix {$fixedCount} archives");
        } else {
            $this->info("✅ File number fix completed!");
            $this->info("Fixed {$fixedCount} archives");
        }

        if (!empty($errors)) {
            $this->warn("⚠️  " . count($errors) . " errors occurred:");
            foreach ($errors as $error) {
                $this->error("   - {$error}");
            }
        }

        // Update storage box counts
        if (!$this->option('dry-run')) {
            $this->info('🔄 Updating storage box counts...');
            $this->call('fix:storage-box-counts');
        }

        return 0;
    }
}
