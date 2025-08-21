<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archive;

class FixFileNumbersCommand extends Command
{
    protected $signature = 'fix:file-numbers {--dry-run : Show what would be changed without making changes}';
    protected $description = 'Fix existing file numbers to follow the correct definitive number rules';

    public function handle()
    {
        $this->info('🔧 Fixing existing file numbers to follow the correct definitive number rules...');

        if ($this->option('dry-run')) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        try {
            if ($this->option('dry-run')) {
                // For dry run, just show what would be changed
                $this->info('🔍 This would fix file numbers to restart at 1 for each year within same classification');
                $this->info('🔍 Example: Masalah A tahun 2020: No 1-25, Masalah A tahun 2021: No 1-25 (not continuing from 26)');
                return 0;
            }

            // Call the model method to fix file numbers
            $result = Archive::fixAllExistingFileNumbers();

            if ($result['success']) {
                $this->info("✅ File number fix completed successfully!");
                $this->info("Fixed {$result['fixed_count']} archives");

                if (!empty($result['errors'])) {
                    $this->warn("⚠️  " . count($result['errors']) . " errors occurred:");
                    foreach ($result['errors'] as $error) {
                        $this->error("   - {$error}");
                    }
                }

                // Update storage box counts
                $this->info('🔄 Updating storage box counts...');
                $this->call('fix:storage-box-counts');

            } else {
                $this->error('❌ Failed to fix file numbers');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
