<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixFileNumbersCommand extends Command
{
    protected $signature = 'fix:file-numbers {--dry-run : Show what would be fixed without making changes}';
    protected $description = 'Fix file numbers to restart from 1 for each year within each box';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🔧 Fixing File Numbers (Restart from 1 per year)');
        $this->info('================================================');

        try {
            DB::beginTransaction();

            // Get all archives with storage locations
            $archivesWithLocation = Archive::whereNotNull('rack_number')
                ->whereNotNull('row_number')
                ->whereNotNull('box_number')
                ->whereNotNull('file_number')
                ->orderBy('rack_number')
                ->orderBy('row_number')
                ->orderBy('box_number')
                ->orderBy('kurun_waktu_start')
                ->get();

            if ($archivesWithLocation->count() === 0) {
                $this->warn('⚠️  No archives with storage locations found');
                return 0;
            }

            $this->info("📁 Found {$archivesWithLocation->count()} archives with storage locations");

            // Group by rack, row, box
            $groupedArchives = $archivesWithLocation->groupBy(function ($archive) {
                return "Rak {$archive->rack_number}, Baris {$archive->row_number}, Box {$archive->box_number}";
            });

            $fixedCount = 0;
            $totalBoxes = $groupedArchives->count();

            foreach ($groupedArchives as $location => $archives) {
                $this->info("\n📍 {$location}:");

                // Group by year within this box
                $yearGroups = $archives->groupBy(function ($archive) {
                    return $archive->kurun_waktu_start->format('Y');
                });

                foreach ($yearGroups as $year => $yearArchives) {
                    $this->info("   📅 Tahun {$year}:");

                    // Sort archives by creation date within the year
                    $sortedArchives = $yearArchives->sortBy('created_at');

                    $fileNumber = 1;

                    foreach ($sortedArchives as $archive) {
                        $oldFileNumber = $archive->file_number;

                        if ($isDryRun) {
                            $this->line("      Would fix: Archive {$archive->id} ({$archive->index_number})");
                            $this->line("         File number: {$oldFileNumber} → {$fileNumber}");
                        } else {
                            $archive->update(['file_number' => $fileNumber]);
                            $this->line("      ✅ Fixed: Archive {$archive->id} ({$archive->index_number})");
                            $this->line("         File number: {$oldFileNumber} → {$fileNumber}");
                            $fixedCount++;
                        }

                        $fileNumber++;
                    }
                }
            }

            if (!$isDryRun) {
                DB::commit();
                $this->info("\n✅ Successfully fixed {$fixedCount} file numbers!");
            } else {
                DB::rollback();
                $this->info("\n🔍 Dry run completed. Would fix {$fixedCount} file numbers.");
            }

            $this->info("\n📊 Summary:");
            $this->info("   - Archives processed: {$archivesWithLocation->count()}");
            $this->info("   - Boxes processed: {$totalBoxes}");
            $this->info("   - File numbers fixed: {$fixedCount}");

            return 0;

        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Error fixing file numbers: " . $e->getMessage());
            return 1;
        }
    }
}
