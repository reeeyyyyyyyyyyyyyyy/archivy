<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupTestArchivesCommand extends Command
{
    protected $signature = 'archives:cleanup-test {--confirm : Skip confirmation prompt}';
    protected $description = 'Cleanup test archives created for bulk location testing';

    public function handle()
    {
        $this->info('🧹 Starting test archives cleanup...');

        // Count test archives
        $testArchives = Archive::where('index_number', 'like', 'TEST-%')->count();
        $masalahAArchives = Archive::where('lampiran_surat', 'SK-TANAMAN-REMPAH-001')->count();
        $masalahBArchives = Archive::where('lampiran_surat', 'SK-PPA-001')->count();

        if ($testArchives === 0) {
            $this->info('✅ No test archives found to cleanup.');
            return 0;
        }

        $this->warn("📊 Found test archives to cleanup:");
        $this->warn("   - Total test archives: {$testArchives}");
        $this->warn("   - Masalah A (Tanaman Rempah): {$masalahAArchives}");
        $this->warn("   - Masalah B (PPA): {$masalahBArchives}");

        if (!$this->option('confirm')) {
            if (!$this->confirm('Are you sure you want to delete all test archives? This action cannot be undone.')) {
                $this->info('❌ Cleanup cancelled.');
                return 0;
            }
        }

        try {
            DB::beginTransaction();

            // Delete test archives
            $deletedCount = Archive::where('index_number', 'like', 'TEST-%')->delete();

            DB::commit();

            $this->info("✅ Successfully deleted {$deletedCount} test archives!");
            $this->info("🧹 Cleanup completed successfully.");

            return 0;

        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Error during cleanup: " . $e->getMessage());
            return 1;
        }
    }
}
