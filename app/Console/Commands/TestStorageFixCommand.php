<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Models\StorageRack;
use App\Models\StorageBox;
use App\Models\StorageRow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestStorageFixCommand extends Command
{
    protected $signature = 'test:storage-fix';
    protected $description = 'Test and fix storage system issues';

    public function handle()
    {
        $this->info('🔧 TESTING STORAGE FIXES...');

        // Test 1: Check current storage state
        $this->testCurrentStorageState();

        // Test 2: Fix auto-fill logic
        $this->testAutoFillLogic();

        // Test 3: Test box progression
        $this->testBoxProgression();

        // Test 4: Test capacity management
        $this->testCapacityManagement();

        $this->info('✅ STORAGE FIX TESTING COMPLETED!');
    }

    private function testCurrentStorageState()
    {
        $this->info('📋 1. Testing Current Storage State...');

        $racks = StorageRack::with(['rows', 'boxes'])->get();

        foreach ($racks as $rack) {
            $this->info("   Rak {$rack->name}:");
            $this->info("     - Total boxes: {$rack->total_boxes}");
            $this->info("     - Available boxes: {$rack->getAvailableBoxesCount()}");
            $this->info("     - Utilization: {$rack->getUtilizationPercentage()}%");

            // Check each box
            foreach ($rack->boxes as $box) {
                $this->info("     - Box {$box->box_number}: {$box->archive_count}/{$box->capacity} ({$box->status})");
            }
        }
    }

    private function testAutoFillLogic()
    {
        $this->info('📋 2. Testing Auto-fill Logic...');

        $racks = StorageRack::where('status', 'active')->get();

        foreach ($racks as $rack) {
            $nextBox = $rack->getNextAvailableBox();

            if ($nextBox) {
                $this->info("   Rak {$rack->name}:");
                $this->info("     - Next available box: {$nextBox->box_number}");
                $this->info("     - Row: {$nextBox->row->row_number}");
                $this->info("     - Next file number: {$nextBox->getNextFileNumber()}");
                $this->info("     - Current archive count: {$nextBox->archive_count}");
            } else {
                $this->info("   Rak {$rack->name}: No available boxes");
            }
        }
    }

    private function testBoxProgression()
    {
        $this->info('📋 3. Testing Box Progression...');

        // Simulate adding archives to test progression
        $user = User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->first();

        if (!$user) {
            $this->error('No admin user found!');
            return;
        }

        Auth::login($user);

        // Test adding archives to see if boxes progress correctly
        $rack = StorageRack::where('status', 'active')->first();

        if (!$rack) {
            $this->error('No active rack found!');
            return;
        }

        $this->info("   Testing progression in Rak {$rack->name}...");

        // Get current state
        $initialBox = $rack->getNextAvailableBox();
        if ($initialBox) {
            $this->info("     - Initial next box: {$initialBox->box_number}");
            $this->info("     - Initial file number: {$initialBox->getNextFileNumber()}");

            // Simulate adding archives
            for ($i = 1; $i <= 5; $i++) {
                $nextBox = $rack->getNextAvailableBox();
                if ($nextBox) {
                    $this->info("     - Step {$i}: Box {$nextBox->box_number}, File {$nextBox->getNextFileNumber()}");

                    // Increment archive count to simulate adding archive
                    $nextBox->increment('archive_count');
                    $nextBox->updateStatus();
                }
            }
        }
    }

    private function testCapacityManagement()
    {
        $this->info('📋 4. Testing Capacity Management...');

        $boxes = StorageBox::with(['rack', 'row'])->get();

        foreach ($boxes as $box) {
            $utilization = $box->getUtilizationPercentage();
            $status = $box->status;

            $this->info("   Box {$box->box_number} (Rak {$box->rack->name}, Row {$box->row->row_number}):");
            $this->info("     - Archive count: {$box->archive_count}/{$box->capacity}");
            $this->info("     - Utilization: {$utilization}%");
            $this->info("     - Status: {$status}");

            // Check if status is correct
            if ($box->isFull() && $status !== 'full') {
                $this->warn("     ⚠️  Status should be 'full' but is '{$status}'");
            } elseif ($box->isPartiallyFull() && $status !== 'partially_full') {
                $this->warn("     ⚠️  Status should be 'partially_full' but is '{$status}'");
            } elseif ($box->isAvailable() && $status !== 'available') {
                $this->warn("     ⚠️  Status should be 'available' but is '{$status}'");
            }
        }
    }
}
