<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateLocationBasedDefinitiveNumbersCommand extends Command
{
    protected $signature = 'generate:location-based-definitive-numbers {--dry-run : Show what would be generated without making changes}';
    protected $description = 'Generate definitive numbers based on storage location (Rack-Row-Box-File format)';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🔧 Generating Location-Based Definitive Numbers');
        $this->info('==============================================');

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
                ->orderBy('file_number')
                ->get();

            if ($archivesWithLocation->count() === 0) {
                $this->warn('⚠️  No archives with complete storage locations found');
                return 0;
            }

            $this->info("📁 Found {$archivesWithLocation->count()} archives with storage locations");

            $generatedCount = 0;

            foreach ($archivesWithLocation as $archive) {
                $oldDefinitiveNumber = $archive->definitive_number;
                $newDefinitiveNumber = $this->generateLocationBasedDefinitiveNumber($archive);

                if ($isDryRun) {
                    $this->info("   Would generate: Archive {$archive->id} ({$archive->index_number})");
                    $this->info("      Location: Rak {$archive->rack_number}, Row {$archive->row_number}, Box {$archive->box_number}, File {$archive->file_number}");
                    $this->info("      Old: {$oldDefinitiveNumber}");
                    $this->info("      New: {$newDefinitiveNumber} (Format: RRBBFF)");
                } else {
                    $archive->update(['definitive_number' => $newDefinitiveNumber]);
                    $this->info("   ✅ Generated: Archive {$archive->id} ({$archive->index_number})");
                    $this->info("      Location: Rak {$archive->rack_number}, Row {$archive->row_number}, Box {$archive->box_number}, File {$archive->file_number}");
                    $this->info("      {$oldDefinitiveNumber} → {$newDefinitiveNumber}");
                    $generatedCount++;
                }
            }

            // Get archives without storage locations
            $archivesWithoutLocation = Archive::whereNull('rack_number')
                ->orWhereNull('row_number')
                ->orWhereNull('box_number')
                ->orWhereNull('file_number')
                ->get();

            if ($archivesWithoutLocation->count() > 0) {
                $this->warn("\n⚠️  Found {$archivesWithoutLocation->count()} archives without storage locations");
                $this->warn("   These will keep their current definitive numbers");

                foreach ($archivesWithoutLocation as $archive) {
                    $this->line("      - Archive {$archive->id}: {$archive->definitive_number} (no location)");
                }
            }

            if (!$isDryRun) {
                DB::commit();
                $this->info("\n✅ Successfully generated {$generatedCount} location-based definitive numbers!");
            } else {
                DB::rollback();
                $this->info("\n🔍 Dry run completed. Would generate {$generatedCount} location-based definitive numbers.");
            }

            $this->info("\n📊 Summary:");
            $this->info("   - Archives with locations: {$archivesWithLocation->count()}");
            $this->info("   - Archives without locations: {$archivesWithoutLocation->count()}");
            $this->info("   - Definitive numbers generated: {$generatedCount}");

            return 0;

        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Error generating location-based definitive numbers: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Generate definitive number based on storage location (Rack-Row-Box-File format)
     */
    private function generateLocationBasedDefinitiveNumber(Archive $archive): int
    {
        $rackNumber = str_pad($archive->rack_number, 2, '0', STR_PAD_LEFT);
        $rowNumber = str_pad($archive->row_number, 2, '0', STR_PAD_LEFT);
        $boxNumber = str_pad($archive->box_number, 2, '0', STR_PAD_LEFT);
        $fileNumber = str_pad($archive->file_number, 2, '0', STR_PAD_LEFT);

        // Format: RRBBFF (Rack-Row-Box-File)
        // Example: Rak 1, Row 1, Box 1, File 1 = 010101
        // Example: Rak 1, Row 1, Box 1, File 10 = 010110
        $definitiveNumber = (int) ($rackNumber . $rowNumber . $boxNumber . $fileNumber);

        return $definitiveNumber;
    }
}
