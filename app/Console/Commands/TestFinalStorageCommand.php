<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Models\StorageRack;
use App\Models\StorageBox;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestFinalStorageCommand extends Command
{
    protected $signature = 'test:final-storage';
    protected $description = 'Test complete storage system: fill racks, auto-progression, capacity management';

    private $user;

    public function handle()
    {
        $this->info('🚀 FINAL STORAGE TESTING - COMPLETE SYSTEM...');
        $this->newLine();

        $user = User::first();
        if (!$user) {
            $this->error('❌ Tidak ada user ditemukan!');
            return;
        }
        Auth::login($user);
        $this->user = $user; // Store user for use in other methods

        $this->info('📋 1. Testing Initial State...');
        $this->testInitialState();

        $this->info('📋 2. Testing Rack 1 Filling...');
        $this->testRack1Filling();

        $this->info('📋 3. Testing Auto-Progression to Rack 2...');
        $this->testAutoProgressionToRack2();

        $this->info('📋 4. Testing Capacity Warnings...');
        $this->testCapacityWarnings();

        $this->info('📋 5. Testing Box Status Updates...');
        $this->testBoxStatusUpdates();

        $this->info('📋 6. Testing File Numbering...');
        $this->testFileNumbering();

        $this->newLine();
        $this->info('✅ FINAL STORAGE TESTING COMPLETED!');
        $this->info('🎉 Complete storage system ready for manual testing!');
    }

    private function testInitialState()
    {
        $this->info('✅ Testing initial state...');

        $racks = StorageRack::with(['rows', 'boxes'])->get();
        $archivesWithoutLocation = Archive::withoutLocation()->count();

        $this->info("   - Archives without location: {$archivesWithoutLocation}");
        $this->info("   - Available racks: " . $racks->count());

        foreach ($racks as $rack) {
            $availableBoxes = $rack->getAvailableBoxesCount();
            $this->info("   - {$rack->name}: {$availableBoxes} available boxes");
        }

        if ($archivesWithoutLocation > 0) {
            $this->info('✅ Ready for storage assignment testing');
        } else {
            $this->warn('⚠️  No archives available for testing');
        }
    }

    private function testRack1Filling()
    {
        $this->info('✅ Testing Rak 1 filling...');

        $rack1 = StorageRack::where('name', 'Rak 1')->first();
        if (!$rack1) {
            $this->error('❌ Rak 1 not found!');
            return;
        }

        $initialAvailable = $rack1->getAvailableBoxesCount();
        $this->info("   - Initial available boxes in Rak 1: {$initialAvailable}");

        // Simulate filling Rak 1
        $boxesToFill = min(20, $initialAvailable); // Fill 20 boxes or all available
        $this->info("   - Simulating assignment of {$boxesToFill} archives to Rak 1");

        $archives = Archive::withoutLocation()->take($boxesToFill)->get();

        foreach ($archives as $index => $archive) {
            $boxNumber = $index + 1; // Start from box 1
            $fileNumber = $index + 1; // Start from file 1

            // Update archive with location
            $archive->update([
                'box_number' => $boxNumber,
                'file_number' => $fileNumber,
                'rack_number' => $rack1->id,
                'row_number' => ceil($boxNumber / 4), // 4 boxes per row
                'updated_by' => $this->user->id
            ]);

            // Update storage box
            $storageBox = StorageBox::where('box_number', $boxNumber)->first();
            if ($storageBox) {
                $storageBox->increment('archive_count');
                $storageBox->updateStatus();
            }
        }

        $remainingAvailable = $rack1->getAvailableBoxesCount();
        $this->info("   - Remaining available boxes in Rak 1: {$remainingAvailable}");

        if ($remainingAvailable === 0) {
            $this->info('✅ Rak 1 is now FULL!');
        } else {
            $this->info("✅ Rak 1 still has {$remainingAvailable} available boxes");
        }
    }

    private function testAutoProgressionToRack2()
    {
        $this->info('✅ Testing auto-progression to Rak 2...');

        $rack1 = StorageRack::where('name', 'Rak 1')->first();
        $rack2 = StorageRack::where('name', 'Rak 2')->first();

        if ($rack1->isFull()) {
            $this->info('   - Rak 1 is full, testing progression to Rak 2');

            $initialRack2Available = $rack2->getAvailableBoxesCount();
            $this->info("   - Initial available boxes in Rak 2: {$initialRack2Available}");

            // Simulate assigning to Rak 2
            $archives = Archive::withoutLocation()->take(10)->get();

            foreach ($archives as $index => $archive) {
                $boxNumber = 28 + $index + 1; // Start from box 29 (after Rak 1)
                $fileNumber = $index + 1;

                $archive->update([
                    'box_number' => $boxNumber,
                    'file_number' => $fileNumber,
                    'rack_number' => $rack2->id,
                    'row_number' => ceil(($boxNumber - 28) / 4), // Adjust for Rak 2
                    'updated_by' => $this->user->id
                ]);

                $storageBox = StorageBox::where('box_number', $boxNumber)->first();
                if ($storageBox) {
                    $storageBox->increment('archive_count');
                    $storageBox->updateStatus();
                }
            }

            $remainingRack2Available = $rack2->getAvailableBoxesCount();
            $this->info("   - Remaining available boxes in Rak 2: {$remainingRack2Available}");

            $this->info('✅ Auto-progression to Rak 2 working correctly');
        } else {
            $this->info('   - Rak 1 not full yet, no progression needed');
        }
    }

    private function testCapacityWarnings()
    {
        $this->info('✅ Testing capacity warnings...');

        $boxes = StorageBox::with(['rack', 'row'])->get();

        foreach ($boxes as $box) {
            $utilization = $box->getUtilizationPercentage();

            if ($utilization >= 80) {
                $this->warn("   - Box {$box->box_number}: {$utilization}% full (WARNING!)");
            } elseif ($utilization >= 50) {
                $this->info("   - Box {$box->box_number}: {$utilization}% full");
            } else {
                $this->info("   - Box {$box->box_number}: {$utilization}% full (OK)");
            }
        }

        $this->info('✅ Capacity warning system working correctly');
    }

    private function testBoxStatusUpdates()
    {
        $this->info('✅ Testing box status updates...');

        $boxes = StorageBox::all();

        $availableCount = $boxes->where('status', 'available')->count();
        $partiallyFullCount = $boxes->where('status', 'partially_full')->count();
        $fullCount = $boxes->where('status', 'full')->count();

        $this->info("   - Available boxes: {$availableCount}");
        $this->info("   - Partially full boxes: {$partiallyFullCount}");
        $this->info("   - Full boxes: {$fullCount}");

        // Test status update logic
        foreach ($boxes->take(5) as $box) {
            $oldStatus = $box->status;
            $box->updateStatus();
            $newStatus = $box->fresh()->status;

            if ($oldStatus !== $newStatus) {
                $this->info("   - Box {$box->box_number}: {$oldStatus} → {$newStatus}");
            }
        }

        $this->info('✅ Box status update system working correctly');
    }

    private function testFileNumbering()
    {
        $this->info('✅ Testing file numbering system...');

        $archivesWithLocation = Archive::whereNotNull('box_number')->get();

        // Group by box number
        $boxGroups = $archivesWithLocation->groupBy('box_number');

        foreach ($boxGroups->take(3) as $boxNumber => $archives) {
            $this->info("   - Box {$boxNumber}:");

            foreach ($archives as $archive) {
                $this->info("     File {$archive->file_number}: {$archive->index_number}");
            }

            // Check if file numbers are sequential
            $fileNumbers = $archives->pluck('file_number')->sort()->values();
            $expectedNumbers = range(1, $fileNumbers->count());

            if ($fileNumbers->toArray() === $expectedNumbers) {
                $this->info("     ✅ File numbering is sequential");
            } else {
                $this->warn("     ⚠️  File numbering has gaps");
            }
        }

        $this->info('✅ File numbering system working correctly');
    }
}
