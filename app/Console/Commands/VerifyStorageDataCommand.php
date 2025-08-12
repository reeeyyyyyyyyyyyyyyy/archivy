<?php

namespace App\Console\Commands;

use App\Models\StorageBox;
use App\Models\Archive;
use App\Models\StorageRack;
use Illuminate\Console\Command;

class VerifyStorageDataCommand extends Command
{
    protected $signature = 'storage:verify';
    protected $description = 'Verify storage data consistency';

    public function handle()
    {
        $this->info('🔍 Verifying Storage Data Consistency');
        $this->info('=====================================');

        // Check storage boxes
        $this->info("\n📦 Storage Boxes Check:");
        $storageBoxes = StorageBox::all();
        $this->info("   Total storage boxes: {$storageBoxes->count()}");

        $inconsistentBoxes = 0;
        foreach ($storageBoxes as $box) {
            $actualCount = Archive::where('box_number', $box->box_number)->count();
            if ($box->archive_count !== $actualCount) {
                $inconsistentBoxes++;
                $this->warn("   ⚠️  Box {$box->box_number}: stored={$box->archive_count}, actual={$actualCount}");
            }
        }

        if ($inconsistentBoxes === 0) {
            $this->info("   ✅ All storage boxes are consistent!");
        } else {
            $this->warn("   ⚠️  Found {$inconsistentBoxes} inconsistent boxes");
        }

        // Check racks
        $this->info("\n🏗️  Storage Racks Check:");
        $racks = StorageRack::all();
        $this->info("   Total racks: {$racks->count()}");

        foreach ($racks as $rack) {
            $rackBoxes = $rack->boxes;
            $totalCapacity = $rackBoxes->sum('capacity');
            $totalStoredCount = $rackBoxes->sum('archive_count');
            $actualArchiveCount = Archive::where('rack_number', $rack->id)->count();

            $this->info("   📊 {$rack->name}:");
            $this->info("      - Boxes: {$rackBoxes->count()}");
            $this->info("      - Total capacity: {$totalCapacity}");
            $this->info("      - Stored archive count: {$totalStoredCount}");
            $this->info("      - Actual archive count: {$actualArchiveCount}");

            if ($totalStoredCount !== $actualArchiveCount) {
                $this->warn("      ⚠️  Inconsistent archive count!");
            } else {
                $this->info("      ✅ Archive count is consistent");
            }
        }

        // Check archives with location
        $this->info("\n📁 Archives with Location Check:");
        $archivesWithLocation = Archive::whereNotNull('box_number')->count();
        $archivesWithRack = Archive::whereNotNull('rack_number')->count();
        $archivesWithRow = Archive::whereNotNull('row_number')->count();
        $archivesWithFile = Archive::whereNotNull('file_number')->count();

        $this->info("   Archives with box_number: {$archivesWithLocation}");
        $this->info("   Archives with rack_number: {$archivesWithRack}");
        $this->info("   Archives with row_number: {$archivesWithRow}");
        $this->info("   Archives with file_number: {$archivesWithFile}");

        // Check for archives with incomplete location
        $incompleteLocation = Archive::whereNotNull('box_number')
            ->where(function($query) {
                $query->whereNull('rack_number')
                      ->orWhereNull('row_number')
                      ->orWhereNull('file_number');
            })
            ->count();

        if ($incompleteLocation > 0) {
            $this->warn("   ⚠️  Found {$incompleteLocation} archives with incomplete location data");
        } else {
            $this->info("   ✅ All archives with location have complete data");
        }

        // Check for orphaned archives (archives with box_number but no storage box)
        $orphanedArchives = Archive::whereNotNull('box_number')
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                      ->from('storage_boxes')
                      ->whereRaw('storage_boxes.box_number = archives.box_number');
            })
            ->count();

        if ($orphanedArchives > 0) {
            $this->error("   🚨 Found {$orphanedArchives} orphaned archives (have box_number but no storage box)");
        } else {
            $this->info("   ✅ No orphaned archives found");
        }

        // Summary
        $this->info("\n📋 Summary:");
        $this->info("   ✅ Storage boxes: " . ($inconsistentBoxes === 0 ? 'Consistent' : 'Inconsistent'));
        $this->info("   ✅ Archives location: " . ($incompleteLocation === 0 ? 'Complete' : 'Incomplete'));
        $this->info("   ✅ Orphaned archives: " . ($orphanedArchives === 0 ? 'None' : 'Found'));

        if ($inconsistentBoxes === 0 && $incompleteLocation === 0 && $orphanedArchives === 0) {
            $this->info("\n🎉 All storage data is consistent and correct!");
        } else {
            $this->warn("\n⚠️  Some issues found. Consider running 'php artisan storage:fix-box-counts' to fix inconsistencies.");
        }

        return 0;
    }
}
