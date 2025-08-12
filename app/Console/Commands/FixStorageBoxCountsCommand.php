<?php

namespace App\Console\Commands;

use App\Models\StorageBox;
use App\Models\Archive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixStorageBoxCountsCommand extends Command
{
    protected $signature = 'fix:storage-box-counts {--dry-run : Show what would be fixed without making changes}';
    protected $description = 'Fix storage box archive counts by recalculating based on actual archives';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🔧 Fixing Storage Box Archive Counts');
        $this->info('==================================');

        try {
            DB::beginTransaction();

            // Get all storage boxes
            $storageBoxes = StorageBox::all();

            if ($storageBoxes->count() === 0) {
                $this->warn('⚠️  No storage boxes found');
                return 0;
            }

            $this->info("📁 Found {$storageBoxes->count()} storage boxes");

            $fixedCount = 0;
            $totalArchives = 0;

            foreach ($storageBoxes as $box) {
                // Count actual archives in this box
                $actualArchiveCount = Archive::where('rack_number', $box->rack_id)
                    ->where('box_number', $box->box_number)
                    ->count();

                $oldCount = $box->archive_count;
                $newCount = $actualArchiveCount;

                if ($isDryRun) {
                    $this->info("   Would fix: Box {$box->box_number} (Rack {$box->rack_id})");
                    $this->info("      Old archive_count: {$oldCount}");
                    $this->info("      New archive_count: {$newCount} (actual archives: {$actualArchiveCount})");
                } else {
                    $box->update(['archive_count' => $newCount]);
                    $this->info("   ✅ Fixed: Box {$box->box_number} (Rack {$box->rack_id})");
                    $this->info("      {$oldCount} → {$newCount} (actual archives: {$actualArchiveCount})");
                    $fixedCount++;
                }

                $totalArchives += $newCount;
            }

            // Update box statuses
            if (!$isDryRun) {
                foreach ($storageBoxes as $box) {
                    $box->updateStatus();
                }
            }

            if (!$isDryRun) {
                DB::commit();
                $this->info("\n✅ Successfully fixed {$fixedCount} storage box counts!");
            } else {
                DB::rollback();
                $this->info("\n🔍 Dry run completed. Would fix {$fixedCount} storage box counts.");
            }

            $this->info("\n📊 Summary:");
            $this->info("   - Storage boxes processed: {$storageBoxes->count()}");
            $this->info("   - Total archives found: {$totalArchives}");
            $this->info("   - Box counts fixed: {$fixedCount}");

            // Show rack summary
            $this->newLine();
            $this->info('🏗️  Rack Summary:');

            $racks = \App\Models\StorageRack::all();
            foreach ($racks as $rack) {
                $availableBoxes = $rack->getAvailableBoxesCount();
                $partiallyFullBoxes = $rack->getPartiallyFullBoxesCount();
                $fullBoxes = $rack->getFullBoxesCount();
                $utilization = $rack->getUtilizationPercentage();

                $this->info("   {$rack->name}:");
                $this->info("      - Available boxes: {$availableBoxes}");
                $this->info("      - Partially full boxes: {$partiallyFullBoxes}");
                $this->info("      - Full boxes: {$fullBoxes}");
                $this->info("      - Utilization: {$utilization}%");
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Error fixing storage box counts: " . $e->getMessage());
            return 1;
        }
    }
}
