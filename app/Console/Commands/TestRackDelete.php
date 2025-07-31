<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StorageRack;
use App\Models\StorageBox;
use App\Models\Archive;

class TestRackDelete extends Command
{
    protected $signature = 'test:rack-delete';
    protected $description = 'Test rack deletion and debug issues';

    public function handle()
    {
        $this->info('🔧 TESTING RACK DELETION...');

        // 1. Check all racks
        $this->info('📋 1. Checking all racks...');
        $racks = StorageRack::all();

        foreach ($racks as $rack) {
            $this->info("   Rack: {$rack->name} (ID: {$rack->id})");

            // Check archives linked to rack_number
            $archiveCount = Archive::where('rack_number', $rack->id)->count();
            $this->info("     - Archives with rack_number: {$archiveCount}");

            // Check boxes with archive_count > 0
            $boxesWithArchives = StorageBox::where('rack_id', $rack->id)
                ->where('archive_count', '>', 0)
                ->count();
            $this->info("     - Boxes with archive_count > 0: {$boxesWithArchives}");

            // Check total boxes
            $totalBoxes = StorageBox::where('rack_id', $rack->id)->count();
            $this->info("     - Total boxes: {$totalBoxes}");

            // Check if can be deleted
            $canDelete = ($archiveCount === 0 && $boxesWithArchives === 0);
            $this->info("     - Can be deleted: " . ($canDelete ? 'YES' : 'NO'));

            if (!$canDelete) {
                if ($archiveCount > 0) {
                    $this->error("       ❌ Has {$archiveCount} archives linked to rack_number");

                    // Show sample archives
                    $sampleArchives = Archive::where('rack_number', $rack->id)
                        ->limit(3)
                        ->get(['id', 'index_number', 'description', 'rack_number', 'box_number']);

                    $this->info("       Sample archives:");
                    foreach ($sampleArchives as $archive) {
                        $this->info("         - ID: {$archive->id}, Index: {$archive->index_number}, Rack: {$archive->rack_number}, Box: {$archive->box_number}");
                    }
                }
                if ($boxesWithArchives > 0) {
                    $this->error("       ❌ Has {$boxesWithArchives} boxes with archive_count > 0");

                    // Show sample boxes
                    $sampleBoxes = StorageBox::where('rack_id', $rack->id)
                        ->where('archive_count', '>', 0)
                        ->limit(3)
                        ->get(['id', 'box_number', 'archive_count', 'capacity']);

                    $this->info("       Sample boxes:");
                    foreach ($sampleBoxes as $box) {
                        $this->info("         - Box: {$box->box_number}, Archive Count: {$box->archive_count}, Capacity: {$box->capacity}");
                    }
                }
            } else {
                $this->info("       ✅ Can be deleted safely");
            }
        }

        // 2. Test specific rack deletion
        $this->info('📋 2. Testing specific rack deletion...');
        $testRack = StorageRack::where('status', 'active')->first();

        if (!$testRack) {
            $this->error('No active racks found!');
            return;
        }

        $this->info("   Testing rack: {$testRack->name} (ID: {$testRack->id})");

        // Check if can be deleted
        $archiveCount = Archive::where('rack_number', $testRack->id)->count();
        $boxesWithArchives = StorageBox::where('rack_id', $testRack->id)
            ->where('archive_count', '>', 0)
            ->count();

        if ($archiveCount > 0) {
            $this->error("   Cannot delete: Has {$archiveCount} archives linked to rack_number");

            // Show sample archives
            $sampleArchives = Archive::where('rack_number', $testRack->id)
                ->limit(3)
                ->get(['id', 'index_number', 'description', 'rack_number', 'box_number']);

            $this->info("   Sample archives:");
            foreach ($sampleArchives as $archive) {
                $this->info("     - ID: {$archive->id}, Index: {$archive->index_number}, Rack: {$archive->rack_number}, Box: {$archive->box_number}");
            }
        } elseif ($boxesWithArchives > 0) {
            $this->error("   Cannot delete: Has {$boxesWithArchives} boxes with archive_count > 0");

            // Show sample boxes
            $sampleBoxes = StorageBox::where('rack_id', $testRack->id)
                ->where('archive_count', '>', 0)
                ->limit(3)
                ->get(['id', 'box_number', 'archive_count', 'capacity']);

            $this->info("   Sample boxes:");
            foreach ($sampleBoxes as $box) {
                $this->info("     - Box: {$box->box_number}, Archive Count: {$box->archive_count}, Capacity: {$box->capacity}");
            }
        } else {
            $this->info("   ✅ Can be deleted safely");

            // Test actual deletion (with confirmation)
            $this->info("   Testing deletion...");

            // Delete all boxes and rows first
            StorageBox::where('rack_id', $testRack->id)->delete();
            \App\Models\StorageRow::where('rack_id', $testRack->id)->delete();
            \App\Models\StorageCapacitySetting::where('rack_id', $testRack->id)->delete();

            // Delete the rack
            $testRack->delete();

            $this->info("   ✅ Rack deleted successfully!");
        }

        // 3. Check for orphaned data
        $this->info('📋 3. Checking for orphaned data...');

        $orphanedArchives = Archive::whereNotNull('rack_number')
            ->whereNotIn('rack_number', StorageRack::pluck('id'))
            ->count();

        $this->info("   Orphaned archives (rack_number not in racks): {$orphanedArchives}");

        if ($orphanedArchives > 0) {
            $this->warn("   Found {$orphanedArchives} archives with invalid rack_number");

            // Show sample orphaned archives
            $sampleOrphaned = Archive::whereNotNull('rack_number')
                ->whereNotIn('rack_number', StorageRack::pluck('id'))
                ->limit(3)
                ->get(['id', 'index_number', 'rack_number']);

            foreach ($sampleOrphaned as $archive) {
                $this->info("     - ID: {$archive->id}, Index: {$archive->index_number}, Rack: {$archive->rack_number}");
            }
        }

        // 4. Debug specific rack issue
        $this->info('📋 4. Debug specific rack issue...');
        $problemRack = StorageRack::find(1); // Rak 1

        if ($problemRack) {
            $this->info("   Debugging rack: {$problemRack->name} (ID: {$problemRack->id})");

            // Check archives count
            $archivesInRack = Archive::where('rack_number', $problemRack->id)->count();
            $this->info("     - Archives in rack: {$archivesInRack}");

            // Check boxes count
            $boxesInRack = StorageBox::where('rack_id', $problemRack->id)->count();
            $this->info("     - Boxes in rack: {$boxesInRack}");

            // Check boxes with archives
            $boxesWithArchives = StorageBox::where('rack_id', $problemRack->id)
                ->where('archive_count', '>', 0)
                ->count();
            $this->info("     - Boxes with archives: {$boxesWithArchives}");

            // Show all archives in this rack
            $allArchives = Archive::where('rack_number', $problemRack->id)
                ->get(['id', 'index_number', 'description', 'rack_number', 'box_number', 'file_number']);

            $this->info("     - All archives in this rack:");
            foreach ($allArchives as $archive) {
                $this->info("       * ID: {$archive->id}, Index: {$archive->index_number}, Box: {$archive->box_number}, File: {$archive->file_number}");
            }

            // Show all boxes in this rack
            $allBoxes = StorageBox::where('rack_id', $problemRack->id)
                ->get(['id', 'box_number', 'archive_count', 'capacity', 'status']);

            $this->info("     - All boxes in this rack:");
            foreach ($allBoxes as $box) {
                $this->info("       * Box: {$box->box_number}, Archive Count: {$box->archive_count}, Capacity: {$box->capacity}, Status: {$box->status}");
            }
        }

        $this->info('✅ RACK DELETION TESTING COMPLETED!');
    }
}
