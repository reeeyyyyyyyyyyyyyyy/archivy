<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDefinitiveNumbersPerYearCommand extends Command
{
    protected $signature = 'fix:definitive-numbers-per-year {--dry-run : Show what would be fixed without making changes}';
    protected $description = 'Fix definitive numbers to be sequential per year starting from 1';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🔧 Fixing Definitive Numbers Per Year');
        $this->info('=====================================');

        try {
            DB::beginTransaction();

            // Get all archives with location
            $archivesWithLocation = Archive::whereNotNull('rack_number')
                ->whereNotNull('box_number')
                ->whereNotNull('definitive_number')
                ->orderBy('category_id')
                ->orderBy('classification_id')
                ->orderBy('lampiran_surat')
                ->orderBy('kurun_waktu_start')
                ->get();

            if ($archivesWithLocation->count() === 0) {
                $this->warn('⚠️  No archives with definitive numbers found');
                return 0;
            }

            $this->info("📁 Found {$archivesWithLocation->count()} archives with definitive numbers");

            // Group by problem (category + classification + lampiran_surat)
            $problems = $archivesWithLocation->groupBy(function ($archive) {
                return $archive->category->nama_kategori . ' | ' .
                       $archive->classification->nama_klasifikasi . ' | ' .
                       $archive->lampiran_surat;
            });

            $fixedCount = 0;
            $problemsProcessed = 0;

            foreach ($problems as $problemName => $problemArchives) {
                $this->info("\n🔍 Processing problem: {$problemName}");
                $this->info("   Total archives: {$problemArchives->count()}");

                // Group by year
                $yearGroups = $problemArchives->groupBy(function ($archive) {
                    return $archive->kurun_waktu_start->format('Y');
                });

                foreach ($yearGroups as $year => $yearArchives) {
                    $this->info("   📅 Year {$year}: {$yearArchives->count()} archives");

                    // Sort archives by current definitive number to maintain order
                    $sortedArchives = $yearArchives->sortBy('definitive_number');

                    $sequentialNumber = 1;
                    foreach ($sortedArchives as $archive) {
                        // Generate new definitive number with sequential number starting from 1 for each year
                        $newDefinitiveNumber = $this->generateDefinitiveNumber($archive, $sequentialNumber);

                        if ($isDryRun) {
                            $this->info("      Would fix: Archive {$archive->id} ({$archive->index_number})");
                            $this->info("         Old: {$archive->definitive_number}");
                            $this->info("         New: {$newDefinitiveNumber}");
                        } else {
                            // Update the archive
                            $archive->update(['definitive_number' => $newDefinitiveNumber]);
                            $this->info("      ✅ Fixed: Archive {$archive->id} ({$archive->index_number})");
                            $this->info("         {$archive->definitive_number} → {$newDefinitiveNumber}");
                            $fixedCount++;
                        }

                        $sequentialNumber++;
                    }
                }

                $problemsProcessed++;
            }

            if (!$isDryRun) {
                DB::commit();
                $this->info("\n✅ Successfully fixed {$fixedCount} definitive numbers!");
            } else {
                DB::rollback();
                $this->info("\n🔍 Dry run completed. Would fix {$fixedCount} definitive numbers.");
            }

            $this->info("\n📊 Summary:");
            $this->info("   - Problems processed: {$problemsProcessed}");
            $this->info("   - Archives processed: {$archivesWithLocation->count()}");
            $this->info("   - Definitive numbers fixed: {$fixedCount}");

            return 0;

        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Error fixing definitive numbers: " . $e->getMessage());
            return 1;
        }
    }

    private function generateDefinitiveNumber(Archive $archive, int $sequentialNumber): int
    {
        if (!$archive->rack_number || !$archive->row_number || !$archive->box_number || !$archive->kurun_waktu_start) {
            return 0;
        }

        // Format: RRBBSSS (Rack-Row-Box-Sequential)
        $rackNumber = str_pad($archive->rack_number, 2, '0', STR_PAD_LEFT);
        $rowNumber = str_pad($archive->row_number, 2, '0', STR_PAD_LEFT);
        $boxNumber = str_pad($archive->box_number, 2, '0', STR_PAD_LEFT);
        $sequentialStr = str_pad($sequentialNumber, 3, '0', STR_PAD_LEFT);

        // Convert to integer format: RRBBSSS (max 9999999)
        $definitiveNumber = (int) ($rackNumber . $rowNumber . $boxNumber . $sequentialStr);

        return $definitiveNumber;
    }
}
