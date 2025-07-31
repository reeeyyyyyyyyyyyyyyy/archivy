<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archive;
use App\Models\StorageRack;
use App\Models\StorageBox;
use App\Models\User;
use App\Models\Category;
use App\Models\Classification;

class TestSetLocation extends Command
{
    protected $signature = 'test:set-location';
    protected $description = 'Test Set Lokasi functionality and fill boxes to test capacity';

    public function handle()
    {
        $this->info('🔧 TESTING SET LOKASI FUNCTIONALITY...');

        // 1. Test API Routes
        $this->info('📋 1. Testing API Routes...');
        $racks = StorageRack::where('status', 'active')->get();

        foreach ($racks as $rack) {
            $this->info("   Testing rack: {$rack->name} (ID: {$rack->id})");

            // Test getRackRows
            $rows = [];
            for ($i = 1; $i <= $rack->total_rows; $i++) {
                $rows[] = ['row_number' => $i];
            }
            $this->info("     - Rows: " . count($rows));

                        // Test getRackRowBoxes
            foreach ($rows as $row) {
                $boxes = StorageBox::where('rack_id', $rack->id)
                    ->whereHas('row', function($query) use ($row) {
                        $query->where('row_number', $row['row_number']);
                    })
                    ->orderBy('box_number')
                    ->get(['box_number', 'status', 'archive_count', 'capacity']);

                $this->info("     - Row {$row['row_number']}: " . $boxes->count() . " boxes");
            }
        }

        // 2. Test Box Filling
        $this->info('📋 2. Testing Box Filling...');
        $testRack = StorageRack::where('status', 'active')->first();

        if (!$testRack) {
            $this->error('No active racks found!');
            return;
        }

        $this->info("   Using rack: {$testRack->name}");

        // Get first available box
        $firstBox = StorageBox::where('rack_id', $testRack->id)
            ->where('status', '!=', 'full')
            ->orderBy('box_number')
            ->first();

        if (!$firstBox) {
            $this->error('No available boxes found!');
            return;
        }

        $this->info("   Target box: {$firstBox->box_number} (Row: {$firstBox->row_number})");
        $this->info("   Current count: {$firstBox->archive_count}/{$firstBox->capacity}");

        // 3. Create test archives and assign to box
        $this->info('📋 3. Creating test archives...');

        $user = User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->first();

        $category = Category::first();
        $classification = Classification::first();

        if (!$user || !$category || !$classification) {
            $this->error('Missing required data (user, category, or classification)!');
            return;
        }

        $archivesToCreate = min(10, $firstBox->capacity - $firstBox->archive_count);
        $this->info("   Creating {$archivesToCreate} test archives...");

        for ($i = 1; $i <= $archivesToCreate; $i++) {
            $archive = Archive::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'classification_id' => $classification->id,
                'nomor_arsip' => "TEST-{$i}-" . date('Y'),
                'uraian' => "Test Archive {$i} for Set Lokasi Testing",
                'description' => "Test Archive {$i} for Set Lokasi Testing",
                'kurun_waktu_start' => '2020',
                'kurun_waktu_end' => '2020',
                'tingkat_perkembangan' => 'Asli',
                'jumlah' => 1,
                'satuan' => 'Berkas',
                'lampiran_surat' => 'Test lampiran',
                'skkad' => 'BIASA/TERBUKA',
                'retention_aktif' => 5,
                'retention_inaktif' => 10,
                'status' => 'Aktif',
                'index_number' => "TEST-{$i}-" . date('Y'),
                'rack_number' => $testRack->id,
                'row_number' => $firstBox->row_number,
                'box_number' => $firstBox->box_number,
                'file_number' => $firstBox->archive_count + $i
            ]);

            $this->info("     - Created archive {$i}: {$archive->nomor_arsip}");
        }

        // 4. Update box status
        $this->info('📋 4. Updating box status...');
        $updatedBox = StorageBox::find($firstBox->id);
        $updatedBox->archive_count = $updatedBox->archives()->count();
        $updatedBox->updateStatus();
        $updatedBox->save();

        $this->info("   Updated box status: {$updatedBox->status}");
        $this->info("   New archive count: {$updatedBox->archive_count}/{$updatedBox->capacity}");

        // 5. Test next available box
        $this->info('📋 5. Testing next available box...');
        $nextBox = $testRack->getNextAvailableBox();

        if ($nextBox) {
            $this->info("   Next available box: {$nextBox->box_number} (Row: {$nextBox->row_number})");
            $this->info("   Next file number: {$nextBox->getNextFileNumber()}");
        } else {
            $this->info("   No available boxes found!");
        }

        // 6. Test preview grid
        $this->info('📋 6. Testing preview grid...');
        $boxes = StorageBox::where('rack_id', $testRack->id)
            ->orderBy('row_number')
            ->orderBy('box_number')
            ->get();

        foreach ($boxes as $box) {
            $status = $box->status === 'full' ? '🔴' :
                     ($box->status === 'partially_full' ? '🟡' : '🟢');
            $this->info("   {$status} Box {$box->box_number} (Row {$box->row_number}): {$box->archive_count}/{$box->capacity}");
        }

        $this->info('✅ SET LOKASI TESTING COMPLETED!');
        $this->info('');
        $this->info('📝 TESTING NOTES:');
        $this->info('- Check if API routes work correctly');
        $this->info('- Verify box selection in Set Lokasi');
        $this->info('- Test capacity warnings');
        $this->info('- Verify preview grid colors');
        $this->info('- Test next box functionality');
    }
}
