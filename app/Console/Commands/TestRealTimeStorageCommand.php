<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Models\StorageRack;
use App\Models\StorageBox;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestRealTimeStorageCommand extends Command
{
    protected $signature = 'test:realtime-storage';
    protected $description = 'Test real-time storage issues and fixes';

    public function handle()
    {
        $this->info('🔧 TESTING REAL-TIME STORAGE ISSUES...');

        // Test 1: Check current storage state
        $this->testCurrentState();

        // Test 2: Simulate real user behavior
        $this->testRealUserBehavior();

        // Test 3: Test file numbering logic
        $this->testFileNumbering();

        // Test 4: Test rack availability
        $this->testRackAvailability();

        $this->info('✅ REAL-TIME STORAGE TESTING COMPLETED!');
    }

    private function testCurrentState()
    {
        $this->info('📋 1. Testing Current Storage State...');

        $racks = StorageRack::with(['rows', 'boxes'])->get();

        foreach ($racks as $rack) {
            $this->info("   Rak {$rack->name}:");
            $this->info("     - Total boxes: {$rack->total_boxes}");
            $this->info("     - Available boxes: {$rack->getAvailableBoxesCount()}");
            $this->info("     - Utilization: {$rack->getUtilizationPercentage()}%");

            // Check each box with archive count
            foreach ($rack->boxes as $box) {
                $archiveCount = Archive::where('box_number', $box->box_number)->count();
                $this->info("     - Box {$box->box_number}: {$archiveCount} archives, {$box->archive_count} count, {$box->status}");
            }
        }
    }

    private function testRealUserBehavior()
    {
        $this->info('📋 2. Testing Real User Behavior...');

        // Get admin user
        $user = User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->first();

        if (!$user) {
            $this->error('No admin user found!');
            return;
        }

        Auth::login($user);

        // Test: Get next available box for each rack
        $racks = StorageRack::where('status', 'active')->get();

        foreach ($racks as $rack) {
            $nextBox = $rack->getNextAvailableBox();

            if ($nextBox) {
                $this->info("   Rak {$rack->name}:");
                $this->info("     - Next available box: {$nextBox->box_number}");
                $this->info("     - Row: {$nextBox->row->row_number}");
                $this->info("     - Current archive count: {$nextBox->archive_count}");

                // Test file numbering
                $nextFileNumber = Archive::getNextFileNumber($nextBox->box_number);
                $this->info("     - Next file number: {$nextFileNumber}");

                // Check if box is really available
                $actualArchiveCount = Archive::where('box_number', $nextBox->box_number)->count();
                $this->info("     - Actual archives in box: {$actualArchiveCount}");

                if ($actualArchiveCount != $nextBox->archive_count) {
                    $this->warn("     ⚠️  MISMATCH: Box count {$nextBox->archive_count} vs actual {$actualArchiveCount}");
                }
            } else {
                $this->info("   Rak {$rack->name}: No available boxes");
            }
        }
    }

    private function testFileNumbering()
    {
        $this->info('📋 3. Testing File Numbering Logic...');

        // Test specific box
        $testBox = 28;
        $archives = Archive::where('box_number', $testBox)->orderBy('file_number')->get();

        $this->info("   Box {$testBox} archives:");
        foreach ($archives as $archive) {
            $this->info("     - Archive {$archive->id}: File {$archive->file_number} - {$archive->index_number}");
        }

        $nextFileNumber = Archive::getNextFileNumber($testBox);
        $this->info("     - Next file number should be: {$nextFileNumber}");

        // Check for gaps
        $fileNumbers = $archives->pluck('file_number')->sort()->values();
        $this->info("     - File numbers in box: " . $fileNumbers->implode(', '));

        // Find gaps
        $expectedNumbers = range(1, $fileNumbers->max() ?: 1);
        $missingNumbers = array_diff($expectedNumbers, $fileNumbers->toArray());

        if (!empty($missingNumbers)) {
            $this->warn("     ⚠️  Missing file numbers: " . implode(', ', $missingNumbers));
        }
    }

    private function testRackAvailability()
    {
        $this->info('📋 4. Testing Rack Availability Logic...');

        $racks = StorageRack::where('status', 'active')->get();

        foreach ($racks as $rack) {
            $availableBoxes = $rack->getAvailableBoxesCount();
            $isFull = $rack->isFull();

            $this->info("   Rak {$rack->name}:");
            $this->info("     - Available boxes: {$availableBoxes}");
            $this->info("     - Is full: " . ($isFull ? 'Yes' : 'No'));

            if ($availableBoxes == 0 && !$isFull) {
                $this->warn("     ⚠️  LOGIC ERROR: No available boxes but rack not marked as full");
            }

            if ($availableBoxes > 0 && $isFull) {
                $this->warn("     ⚠️  LOGIC ERROR: Available boxes but rack marked as full");
            }
        }
    }
}
