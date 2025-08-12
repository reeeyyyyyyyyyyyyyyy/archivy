<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDefinitiveNumbersPerYearCommand extends Command
{
    protected $signature = 'generate:definitive-numbers-per-year {--dry-run : Show what would be generated without making changes}';
    protected $description = 'Generate definitive numbers per year for all archives (1, 2, 3, etc. for each year)';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🔧 Generating Definitive Numbers Per Year');
        $this->info('==========================================');

        try {
            DB::beginTransaction();

            // Get all archives with kurun_waktu_start
            $archives = Archive::whereNotNull('kurun_waktu_start')
                ->orderBy('category_id')
                ->orderBy('classification_id')
                ->orderBy('lampiran_surat')
                ->orderBy('kurun_waktu_start')
                ->orderBy('id')
                ->get();

            if ($archives->count() === 0) {
                $this->warn('⚠️  No archives found with kurun_waktu_start');
                return 0;
            }

            $this->info("📁 Found {$archives->count()} archives");

            // Group by problem (category + classification + lampiran_surat)
            $problems = $archives->groupBy(function ($archive) {
                return $archive->category->nama_kategori . ' | ' .
                       $archive->classification->nama_klasifikasi . ' | ' .
                       $archive->lampiran_surat;
            });

            $generatedCount = 0;
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

                    $sequentialNumber = 1;
                    foreach ($yearArchives as $archive) {
                        if ($isDryRun) {
                            $this->info("      Would generate: Archive {$archive->id} ({$archive->index_number})");
                            $this->info("         Year: {$year}, Definitive Number: {$sequentialNumber}");
                        } else {
                            // Update the archive
                            $archive->update(['definitive_number' => $sequentialNumber]);
                            $this->info("      ✅ Generated: Archive {$archive->id} ({$archive->index_number})");
                            $this->info("         Year: {$year}, Definitive Number: {$sequentialNumber}");
                            $generatedCount++;
                        }

                        $sequentialNumber++;
                    }
                }

                $problemsProcessed++;
            }

            if (!$isDryRun) {
                DB::commit();
                $this->info("\n✅ Successfully generated {$generatedCount} definitive numbers!");
            } else {
                DB::rollback();
                $this->info("\n🔍 Dry run completed. Would generate {$generatedCount} definitive numbers.");
            }

            $this->info("\n📊 Summary:");
            $this->info("   - Problems processed: {$problemsProcessed}");
            $this->info("   - Archives processed: {$archives->count()}");
            $this->info("   - Definitive numbers generated: {$generatedCount}");

            return 0;

        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Error generating definitive numbers: " . $e->getMessage());
            return 1;
        }
    }
}
