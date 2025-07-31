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

class TestStorageIssuesCommand extends Command
{
    protected $signature = 'test:storage-issues';
    protected $description = 'Test and fix storage issues';

    public function handle()
    {
        $this->info('🔧 TESTING STORAGE ISSUES...');

        // Test 1: Check box number uniqueness issue
        $this->testBoxNumberUniqueness();

        // Test 2: Check auto-fill logic
        $this->testAutoFillLogic();

        // Test 3: Check preview grid
        $this->testPreviewGrid();

        // Test 4: Check capacity management
        $this->testCapacityManagement();

        $this->info('✅ STORAGE ISSUES TESTING COMPLETED!');
    }

    private function testBoxNumberUniqueness()
    {
        $this->info('📋 1. Testing Box Number Uniqueness...');

        // Check for duplicate box numbers
        $duplicates = StorageBox::select('box_number')
            ->groupBy('box_number')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isNotEmpty()) {
            $this->error("   ⚠️  Found duplicate box numbers: " . $duplicates->pluck('box_number')->implode(', '));

            // Fix duplicates by updating box numbers
            foreach ($duplicates as $duplicate) {
                $boxes = StorageBox::where('box_number', $duplicate->box_number)->orderBy('id')->get();
                $counter = 1;

                foreach ($boxes as $box) {
                    if ($counter > 1) {
                        // Find next available box number
                        $nextBoxNumber = StorageBox::max('box_number') + $counter;
                        $box->update(['box_number' => $nextBoxNumber]);
                        $this->info("     - Fixed Box ID {$box->id}: {$duplicate->box_number} → {$nextBoxNumber}");
                    }
                    $counter++;
                }
            }
        } else {
            $this->info("   ✅ No duplicate box numbers found");
        }
    }

    private function testAutoFillLogic()
    {
        $this->info('📋 2. Testing Auto-fill Logic...');

        $racks = StorageRack::where('status', 'active')->get();

        foreach ($racks as $rack) {
            $this->info("   Rak {$rack->name}:");

            // Check if rack has available boxes
            $availableBoxes = $rack->getAvailableBoxesCount();
            $this->info("     - Available boxes: {$availableBoxes}");

            if ($availableBoxes > 0) {
                $nextBox = $rack->getNextAvailableBox();
                $this->info("     - Next available box: {$nextBox->box_number}");
                $this->info("     - Row: {$nextBox->row->row_number}");
                $this->info("     - Current archive count: {$nextBox->archive_count}");
                $this->info("     - Capacity: {$nextBox->capacity}");
                $this->info("     - Status: {$nextBox->status}");
            } else {
                $this->info("     - No available boxes (rack is full)");
            }
        }
    }

    private function testPreviewGrid()
    {
        $this->info('📋 3. Testing Preview Grid...');

        $racks = StorageRack::with(['rows', 'boxes'])->get();

        foreach ($racks as $rack) {
            $this->info("   Rak {$rack->name}:");

            foreach ($rack->rows as $row) {
                $this->info("     Baris {$row->row_number}:");

                foreach ($row->boxes as $box) {
                    $statusColor = match($box->status) {
                        'available' => 'green',
                        'partially_full' => 'yellow',
                        'full' => 'red',
                        default => 'gray'
                    };

                    $this->info("       - Box {$box->box_number}: {$box->archive_count}/{$box->capacity} ({$statusColor})");
                }
            }
        }
    }

    private function testCapacityManagement()
    {
        $this->info('📋 4. Testing Capacity Management...');

        // Test soft limit warning (20 archives)
        $boxes = StorageBox::where('archive_count', '>=', 20)->get();

        if ($boxes->isNotEmpty()) {
            $this->warn("   ⚠️  Boxes with 20+ archives:");
            foreach ($boxes as $box) {
                $this->warn("     - Box {$box->box_number}: {$box->archive_count}/{$box->capacity} archives");
            }
        } else {
            $this->info("   ✅ No boxes with 20+ archives");
        }

        // Test boxes that are full but not marked as full
        $fullBoxes = StorageBox::where('archive_count', '>=', DB::raw('capacity'))
            ->where('status', '!=', 'full')
            ->get();

        if ($fullBoxes->isNotEmpty()) {
            $this->error("   ⚠️  Boxes that should be marked as full:");
            foreach ($fullBoxes as $box) {
                $this->error("     - Box {$box->box_number}: {$box->archive_count}/{$box->capacity} (status: {$box->status})");
                // Fix status
                $box->updateStatus();
                $this->info("     - Fixed status to: {$box->fresh()->status}");
            }
        } else {
            $this->info("   ✅ All box statuses are correct");
        }
    }
}
