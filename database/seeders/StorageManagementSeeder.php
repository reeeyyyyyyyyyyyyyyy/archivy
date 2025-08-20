<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StorageRack;
use App\Models\StorageRow;
use App\Models\StorageBox;
use App\Models\StorageCapacitySetting;

class StorageManagementSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏗️  Creating Storage Management System...');

        // Create Rack 1 (7 rows, 28 boxes)
        $rack1 = StorageRack::create([
            'name' => 'Rak 1',
            'description' => 'Rak utama untuk arsip aktif',
            'total_rows' => 7,
            'total_boxes' => 28,
            'capacity_per_box' => 50,
            'status' => 'active'
        ]);

        // Create capacity settings for Rack 1
        StorageCapacitySetting::create([
            'rack_id' => $rack1->id,
            'default_capacity_per_box' => 50,
            'warning_threshold' => 40,
            'auto_assign' => true
        ]);

        // Create rows for Rack 1
        for ($rowNumber = 1; $rowNumber <= 7; $rowNumber++) {
            $row = StorageRow::create([
                'rack_id' => $rack1->id,
                'row_number' => $rowNumber,
                'total_boxes' => 4,
                'available_boxes' => 4,
                'status' => 'available'
            ]);

            // Create boxes for each row
            for ($boxIndex = 1; $boxIndex <= 4; $boxIndex++) {
                $boxNumber = (($rowNumber - 1) * 4) + $boxIndex;
                StorageBox::create([
                    'rack_id' => $rack1->id,
                    'row_id' => $row->id,
                    'box_number' => $boxNumber,
                    'archive_count' => 0,
                    'capacity' => 50,
                    'status' => 'available'
                ]);
            }
        }

        // Create Rack 2 (8 rows, 32 boxes)
        $rack2 = StorageRack::create([
            'name' => 'Rak 2',
            'description' => 'Rak untuk arsip inaktif dan permanen',
            'total_rows' => 8,
            'total_boxes' => 32,
            'capacity_per_box' => 50,
            'status' => 'active'
        ]);

        // Create capacity settings for Rack 2
        StorageCapacitySetting::create([
            'rack_id' => $rack2->id,
            'default_capacity_per_box' => 50,
            'warning_threshold' => 40,
            'auto_assign' => true
        ]);

        // Create rows for Rack 2
        for ($rowNumber = 1; $rowNumber <= 8; $rowNumber++) {
            $row = StorageRow::create([
                'rack_id' => $rack2->id,
                'row_number' => $rowNumber,
                'total_boxes' => 4,
                'available_boxes' => 4,
                'status' => 'available'
            ]);

            // Create boxes for each row
            for ($boxIndex = 1; $boxIndex <= 4; $boxIndex++) {
                $boxNumber = (($rowNumber - 1) * 4) + $boxIndex; // Start from 1 for each rack
                StorageBox::create([
                    'rack_id' => $rack2->id,
                    'row_id' => $row->id,
                    'box_number' => $boxNumber,
                    'archive_count' => 0,
                    'capacity' => 50,
                    'status' => 'available'
                ]);
            }
        }

        // Create Rack 3 (4 rows, 16 boxes) - Smaller rack
        $rack3 = StorageRack::create([
            'name' => 'Rak 3',
            'description' => 'Rak kecil untuk arsip khusus',
            'total_rows' => 4,
            'total_boxes' => 16,
            'capacity_per_box' => 30,
            'status' => 'active'
        ]);

        // Create capacity settings for Rak 3
        StorageCapacitySetting::create([
            'rack_id' => $rack3->id,
            'default_capacity_per_box' => 30,
            'warning_threshold' => 24,
            'auto_assign' => true
        ]);

        // Create rows for Rack 3
        for ($rowNumber = 1; $rowNumber <= 4; $rowNumber++) {
            $row = StorageRow::create([
                'rack_id' => $rack3->id,
                'row_number' => $rowNumber,
                'total_boxes' => 4,
                'available_boxes' => 4,
                'status' => 'available'
            ]);

            // Create boxes for each row
            for ($boxIndex = 1; $boxIndex <= 4; $boxIndex++) {
                $boxNumber = (($rowNumber - 1) * 4) + $boxIndex; // Start from 1 for each rack
                StorageBox::create([
                    'rack_id' => $rack3->id,
                    'row_id' => $row->id,
                    'box_number' => $boxNumber,
                    'archive_count' => 0,
                    'capacity' => 30,
                    'status' => 'available'
                ]);
            }
        }

        $this->command->info('✅ Storage Management System created successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Rack 1: 7 rows, 28 boxes (capacity: 50 per box)');
        $this->command->info('   - Rak 2: 8 rows, 32 boxes (capacity: 50 per box)');
        $this->command->info('   - Rak 3: 4 rows, 16 boxes (capacity: 30 per box)');
        $this->command->info('   - Total: 76 boxes available for testing');
    }
}
