<?php

namespace App\Console\Commands;

use App\Models\Archive;
use App\Models\StorageRack;
use App\Models\StorageBox;
use Illuminate\Console\Command;

class TestAllFixesCommand extends Command
{
    protected $signature = 'test:all-fixes';
    protected $description = 'Test all fixes: definitive numbers, storage box counts, and formatting';

    public function handle()
    {
        $this->info('🧪 Testing All Fixes');
        $this->info('==================');

        $allTestsPassed = true;

        // Test 1: Storage Box Counts
        $this->info("\n1️⃣ Testing Storage Box Counts...");
        $boxCountTest = $this->testStorageBoxCounts();
        if (!$boxCountTest) {
            $allTestsPassed = false;
        }

        // Test 2: Definitive Numbers Format
        $this->info("\n2️⃣ Testing Definitive Numbers Format...");
        $definitiveNumberTest = $this->testDefinitiveNumbersFormat();
        if (!$definitiveNumberTest) {
            $allTestsPassed = false;
        }

        // Test 3: Rack Available Boxes Count
        $this->info("\n3️⃣ Testing Rack Available Boxes Count...");
        $rackCountTest = $this->testRackAvailableBoxesCount();
        if (!$rackCountTest) {
            $allTestsPassed = false;
        }

        // Test 4: Archive Counts
        $this->info("\n4️⃣ Testing Archive Counts...");
        $archiveCountTest = $this->testArchiveCounts();
        if (!$archiveCountTest) {
            $allTestsPassed = false;
        }

        // Final Summary
        $this->newLine();
        if ($allTestsPassed) {
            $this->info('🎉 All tests passed! All fixes are working correctly.');
        } else {
            $this->error('❌ Some tests failed. Please check the issues above.');
        }

        return $allTestsPassed ? 0 : 1;
    }

    private function testStorageBoxCounts(): bool
    {
        $storageBoxes = StorageBox::all();
        $issuesFound = 0;

        foreach ($storageBoxes as $box) {
            $actualArchiveCount = Archive::where('rack_number', $box->rack_id)
                ->where('box_number', $box->box_number)
                ->count();

            if ($box->archive_count !== $actualArchiveCount) {
                $this->error("   ❌ Box {$box->box_number} (Rack {$box->rack_id}): stored={$box->archive_count}, actual={$actualArchiveCount}");
                $issuesFound++;
            }
        }

        if ($issuesFound === 0) {
            $this->info("   ✅ All {$storageBoxes->count()} storage boxes have correct archive counts");
            return true;
        } else {
            $this->error("   ❌ Found {$issuesFound} storage boxes with incorrect archive counts");
            return false;
        }
    }

    private function testDefinitiveNumbersFormat(): bool
    {
        $archivesWithLocation = Archive::whereNotNull('rack_number')
            ->whereNotNull('row_number')
            ->whereNotNull('box_number')
            ->whereNotNull('file_number')
            ->get();

        if ($archivesWithLocation->count() === 0) {
            $this->info("   ℹ️  No archives with storage locations found");
            return true;
        }

        $issuesFound = 0;
        foreach ($archivesWithLocation as $archive) {
            $expectedFormat = "R{$archive->rack_number}-R{$archive->row_number}-B{$archive->box_number}-F{$archive->file_number}";

            if ($archive->formatted_definitive_number !== $expectedFormat) {
                $this->error("   ❌ Archive {$archive->id}: expected={$expectedFormat}, got={$archive->formatted_definitive_number}");
                $issuesFound++;
            }
        }

        if ($issuesFound === 0) {
            $this->info("   ✅ All {$archivesWithLocation->count()} archives have correct definitive number format");
            return true;
        } else {
            $this->error("   ❌ Found {$issuesFound} archives with incorrect definitive number format");
            return false;
        }
    }

    private function testRackAvailableBoxesCount(): bool
    {
        $racks = StorageRack::all();
        $issuesFound = 0;

        foreach ($racks as $rack) {
            $availableBoxes = $rack->getAvailableBoxesCount();
            $totalBoxes = $rack->total_boxes;
            $fullBoxes = $rack->getFullBoxesCount();
            $partiallyFullBoxes = $rack->getPartiallyFullBoxesCount();

            // Verify that available + partially full + full = total
            $calculatedTotal = $availableBoxes + $partiallyFullBoxes + $fullBoxes;

            if ($calculatedTotal !== $totalBoxes) {
                $this->error("   ❌ {$rack->name}: available({$availableBoxes}) + partially({$partiallyFullBoxes}) + full({$fullBoxes}) = {$calculatedTotal}, but total = {$totalBoxes}");
                $issuesFound++;
            }

            $this->info("   ℹ️  {$rack->name}: {$availableBoxes} available, {$partiallyFullBoxes} partially full, {$fullBoxes} full (total: {$totalBoxes})");
        }

        if ($issuesFound === 0) {
            $this->info("   ✅ All {$racks->count()} racks have correct available boxes count");
            return true;
        } else {
            $this->error("   ❌ Found {$issuesFound} racks with incorrect available boxes count");
            return false;
        }
    }

    private function testArchiveCounts(): bool
    {
        $totalArchives = Archive::count();
        $archivesWithLocation = Archive::whereNotNull('rack_number')
            ->whereNotNull('row_number')
            ->whereNotNull('box_number')
            ->whereNotNull('file_number')
            ->count();
        $archivesWithoutLocation = $totalArchives - $archivesWithLocation;

        $this->info("   ℹ️  Total archives: {$totalArchives}");
        $this->info("   ℹ️  With storage location: {$archivesWithLocation}");
        $this->info("   ℹ️  Without storage location: {$archivesWithoutLocation}");

        if ($totalArchives === 0) {
            $this->warn("   ⚠️  No archives found in database");
            return true;
        }

        return true;
    }
}
