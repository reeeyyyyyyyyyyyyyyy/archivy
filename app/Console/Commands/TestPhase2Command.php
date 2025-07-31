<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Models\StorageRack;
use App\Models\StorageRow;
use App\Models\StorageBox;
use App\Models\StorageCapacitySetting;
use Illuminate\Support\Facades\Auth;

class TestPhase2Command extends Command
{
    protected $signature = 'test:phase2';
    protected $description = 'Test Phase 2: Storage Management System with Visual Grid and Auto-fill';

    public function handle()
    {
        $this->info('🚀 PHASE 2 TESTING - STORAGE MANAGEMENT SYSTEM...');
        $this->newLine();

        $user = User::first();
        if (!$user) {
            $this->error('❌ Tidak ada user ditemukan!');
            return;
        }
        Auth::login($user);

        $this->info('📋 1. Testing Storage Management Database...');
        $this->testStorageDatabase();

        $this->info('📋 2. Testing Visual Grid System...');
        $this->testVisualGridSystem();

        $this->info('📋 3. Testing Auto-fill Functionality...');
        $this->testAutoFillFunctionality();

        $this->info('📋 4. Testing Capacity Management...');
        $this->testCapacityManagement();

        $this->info('📋 5. Testing Storage Assignment...');
        $this->testStorageAssignment();

        $this->newLine();
        $this->info('✅ PHASE 2 TESTING COMPLETED!');
        $this->info('🎉 Storage Management System ready for testing!');
    }

    private function testStorageDatabase()
    {
        $this->info('✅ Testing storage tables...');

        $racks = StorageRack::count();
        $rows = StorageRow::count();
        $boxes = StorageBox::count();
        $settings = StorageCapacitySetting::count();

        $this->info("   - Storage Racks: {$racks}");
        $this->info("   - Storage Rows: {$rows}");
        $this->info("   - Storage Boxes: {$boxes}");
        $this->info("   - Capacity Settings: {$settings}");

        if ($racks > 0 && $rows > 0 && $boxes > 0) {
            $this->info('✅ Storage database populated successfully');
        } else {
            $this->warn('⚠️  Storage database needs to be seeded');
        }
    }

    private function testVisualGridSystem()
    {
        $this->info('✅ Testing visual grid system...');

        $racks = StorageRack::with(['rows', 'boxes'])->get();

        foreach ($racks as $rack) {
            $this->info("   - {$rack->name}:");
            $this->info("     Rows: {$rack->total_rows}");
            $this->info("     Boxes: {$rack->total_boxes}");
            $this->info("     Available: {$rack->getAvailableBoxesCount()}");
            $this->info("     Utilization: {$rack->getUtilizationPercentage()}%");

            if ($rack->isFull()) {
                $this->warn("     ⚠️  Rack is FULL!");
            }
        }
    }

    private function testAutoFillFunctionality()
    {
        $this->info('✅ Testing auto-fill functionality...');

        $racks = StorageRack::with(['rows', 'boxes'])->get();

        foreach ($racks as $rack) {
            $nextBox = $rack->getNextAvailableBox();
            $nextRow = $rack->getNextAvailableRow();

            if ($nextBox) {
                $this->info("   - {$rack->name}: Next available box {$nextBox->box_number}");
            } else {
                $this->warn("   - {$rack->name}: No available boxes");
            }

            if ($nextRow) {
                $this->info("     Next available row: {$nextRow->row_number}");
            } else {
                $this->warn("     No available rows");
            }
        }
    }

    private function testCapacityManagement()
    {
        $this->info('✅ Testing capacity management...');

        $boxes = StorageBox::with(['rack', 'row'])->get();

        $availableBoxes = $boxes->where('status', 'available')->count();
        $partiallyFullBoxes = $boxes->where('status', 'partially_full')->count();
        $fullBoxes = $boxes->where('status', 'full')->count();

        $this->info("   - Available boxes: {$availableBoxes}");
        $this->info("   - Partially full boxes: {$partiallyFullBoxes}");
        $this->info("   - Full boxes: {$fullBoxes}");

        // Test capacity settings
        $settings = StorageCapacitySetting::with('rack')->get();
        foreach ($settings as $setting) {
            $this->info("   - {$setting->rack->name}:");
            $this->info("     Default capacity: {$setting->default_capacity_per_box}");
            $this->info("     Warning threshold: {$setting->warning_threshold}");
            $this->info("     Auto assign: " . ($setting->auto_assign ? 'Yes' : 'No'));
        }
    }

    private function testStorageAssignment()
    {
        $this->info('✅ Testing storage assignment...');

        // Get archives without location
        $archivesWithoutLocation = Archive::withoutLocation()->count();
        $this->info("   - Archives without location: {$archivesWithoutLocation}");

        // Get available boxes
        $availableBoxes = StorageBox::where('status', 'available')->count();
        $this->info("   - Available boxes: {$availableBoxes}");

        if ($archivesWithoutLocation > 0 && $availableBoxes > 0) {
            $this->info('✅ Ready for storage assignment testing');

            // Test assignment logic
            $firstArchive = Archive::withoutLocation()->first();
            $firstBox = StorageBox::where('status', 'available')->first();

            if ($firstArchive && $firstBox) {
                $this->info("   - Test assignment: Archive {$firstArchive->index_number} -> Box {$firstBox->box_number}");
                $this->info("   - Next file number: {$firstBox->getNextFileNumber()}");
            }
        } else {
            $this->warn('⚠️  No archives or boxes available for testing');
        }
    }
}
