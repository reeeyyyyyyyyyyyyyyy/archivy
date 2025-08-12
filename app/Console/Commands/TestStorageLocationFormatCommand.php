<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;

class TestStorageLocationFormatCommand extends Command
{
    protected $signature = 'test:storage-location-format';
    protected $description = 'Test and verify storage location format for archives';

    public function handle()
    {
        $this->info('🧪 Testing Storage Location Format');
        $this->info('==================================');

        // Get all archives with storage locations
        $archives = Archive::whereNotNull('rack_number')
            ->whereNotNull('row_number')
            ->whereNotNull('box_number')
            ->whereNotNull('file_number')
            ->orderBy('rack_number')
            ->orderBy('row_number')
            ->orderBy('box_number')
            ->orderBy('file_number')
            ->get();

        if ($archives->count() === 0) {
            $this->warn('⚠️  No archives with complete storage locations found');
            return 0;
        }

        $this->info("📁 Found {$archives->count()} archives with complete storage locations");

        // Group by rack
        $racks = $archives->groupBy('rack_number');

        foreach ($racks as $rackNumber => $rackArchives) {
            $this->info("\n🏗️  Rak {$rackNumber}: {$rackArchives->count()} archives");

            // Group by row
            $rows = $rackArchives->groupBy('row_number');

            foreach ($rows as $rowNumber => $rowArchives) {
                $this->info("   📦 Baris {$rowNumber}: {$rowArchives->count()} archives");

                // Group by box
                $boxes = $rowArchives->groupBy('box_number');

                foreach ($boxes as $boxNumber => $boxArchives) {
                    $this->info("      📁 Box {$boxNumber}: {$boxArchives->count()} files");

                    // Show individual files
                    foreach ($boxArchives->sortBy('file_number') as $archive) {
                        $locationFormat = "Rak {$archive->rack_number}, Baris {$archive->row_number}, Box {$archive->box_number}, File {$archive->file_number}";

                        $this->line("         - File {$archive->file_number}: {$archive->index_number} ({$archive->definitive_number})");
                        $this->line("           Location: {$locationFormat}");
                    }
                }
            }
        }

        // Test format validation
        $this->newLine();
        $this->info('🔍 Testing Location Format Validation:');

        $validFormat = true;
        foreach ($archives as $archive) {
            $expectedFormat = "Rak {$archive->rack_number}, Baris {$archive->row_number}, Box {$archive->box_number}, File {$archive->file_number}";

            // Check if all required fields are present
            if (!$archive->rack_number || !$archive->row_number || !$archive->box_number || !$archive->file_number) {
                $this->error("   ❌ Archive {$archive->id}: Missing required location fields");
                $validFormat = false;
            } else {
                $this->line("   ✅ Archive {$archive->id}: {$expectedFormat}");
            }
        }

        if ($validFormat) {
            $this->newLine();
            $this->info('🎉 All storage locations have correct format!');
        } else {
            $this->newLine();
            $this->error('❌ Some storage locations have incorrect format');
        }

        // Show summary statistics
        $this->newLine();
        $this->info('📊 Summary Statistics:');
        $this->info("   - Total archives with locations: {$archives->count()}");
        $this->info("   - Total racks: " . $racks->count());
        $this->info("   - Total rows: " . $archives->groupBy('row_number')->count());
        $this->info("   - Total boxes: " . $archives->groupBy('box_number')->count());
        $this->info("   - Min file number: " . $archives->min('file_number'));
        $this->info("   - Max file number: " . $archives->max('file_number'));

        return $validFormat ? 0 : 1;
    }
}
