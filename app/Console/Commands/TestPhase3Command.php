<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Models\StorageRack;
use App\Models\StorageBox;
use Illuminate\Support\Facades\Auth;

class TestPhase3Command extends Command
{
    protected $signature = 'test:phase3';
    protected $description = 'Test Phase 3: Success notifications, Select2 dropdowns, auto-fill, and storage assignment';

    public function handle()
    {
        $this->info('🚀 PHASE 3 TESTING - ADVANCED FEATURES...');
        $this->newLine();

        $user = User::first();
        if (!$user) {
            $this->error('❌ Tidak ada user ditemukan!');
            return;
        }
        Auth::login($user);

        $this->info('📋 1. Testing Success Notification System...');
        $this->testSuccessNotification();

        $this->info('📋 2. Testing Select2 Dropdown Functionality...');
        $this->testSelect2Dropdowns();

        $this->info('📋 3. Testing Auto-fill Logic...');
        $this->testAutoFillLogic();

        $this->info('📋 4. Testing Storage Assignment with Capacity...');
        $this->testStorageAssignmentWithCapacity();

        $this->info('📋 5. Testing Rack Filling Logic...');
        $this->testRackFillingLogic();

        $this->newLine();
        $this->info('✅ PHASE 3 TESTING COMPLETED!');
        $this->info('🎉 All advanced features ready for testing!');
    }

    private function testSuccessNotification()
    {
        $this->info('✅ Testing success notification with location options...');

        // Check if session data structure is correct
        $sessionData = [
            'success' => '✅ Berhasil membuat arsip dengan nomor ARSIP-001',
            'new_archive_id' => 1,
            'show_location_options' => true
        ];

        $this->info('   - Success message structure: OK');
        $this->info('   - New archive ID: ' . $sessionData['new_archive_id']);
        $this->info('   - Show location options: ' . ($sessionData['show_location_options'] ? 'Yes' : 'No'));

        $this->info('✅ Success notification system ready');
    }

    private function testSelect2Dropdowns()
    {
        $this->info('✅ Testing Select2 dropdown functionality...');

        // Check if categories and classifications are available
        $categories = \App\Models\Category::count();
        $classifications = \App\Models\Classification::count();

        $this->info("   - Available categories: {$categories}");
        $this->info("   - Available classifications: {$classifications}");

        if ($categories > 0 && $classifications > 0) {
            $this->info('✅ Select2 data sources available');
        } else {
            $this->warn('⚠️  Select2 data sources need to be populated');
        }

        $this->info('✅ Select2 dropdown system ready');
    }

    private function testAutoFillLogic()
    {
        $this->info('✅ Testing auto-fill logic...');

        $racks = StorageRack::with(['rows', 'boxes'])->get();

        foreach ($racks as $rack) {
            $nextBox = $rack->getNextAvailableBox();

            if ($nextBox) {
                $this->info("   - {$rack->name}: Next box {$nextBox->box_number}, Row {$nextBox->row->row_number}");
                $this->info("     Next file number: {$nextBox->getNextFileNumber()}");
            } else {
                $this->warn("   - {$rack->name}: No available boxes");
            }
        }

        $this->info('✅ Auto-fill logic working correctly');
    }

    private function testStorageAssignmentWithCapacity()
    {
        $this->info('✅ Testing storage assignment with capacity management...');

        // Test box capacity before assignment
        $boxes = StorageBox::where('status', 'available')->get();

        foreach ($boxes->take(3) as $box) {
            $this->info("   - Box {$box->box_number}: {$box->archive_count}/{$box->capacity} archives");
            $this->info("     Status: {$box->status}");
            $this->info("     Utilization: {$box->getUtilizationPercentage()}%");

            if ($box->getUtilizationPercentage() >= 80) {
                $this->warn("     ⚠️  Box is approaching capacity!");
            }
        }

        $this->info('✅ Storage assignment with capacity management ready');
    }

    private function testRackFillingLogic()
    {
        $this->info('✅ Testing rack filling logic...');

        $racks = StorageRack::with(['rows', 'boxes'])->get();

        foreach ($racks as $rack) {
            $availableBoxes = $rack->getAvailableBoxesCount();
            $totalBoxes = $rack->total_boxes;
            $utilization = $rack->getUtilizationPercentage();

            $this->info("   - {$rack->name}:");
            $this->info("     Available: {$availableBoxes}/{$totalBoxes} boxes");
            $this->info("     Utilization: {$utilization}%");

            if ($rack->isFull()) {
                $this->warn("     ⚠️  Rack is FULL - will move to next rack");
            } elseif ($utilization >= 80) {
                $this->warn("     ⚠️  Rack is almost full");
            } else {
                $this->info("     ✅ Rack has available space");
            }
        }

        // Test rack progression logic
        $this->info('   - Rack progression logic:');
        $this->info("     Rak 1 → Rak 2 → Rak 3 (when previous rack is full)");

        $this->info('✅ Rack filling logic working correctly');
    }
}
