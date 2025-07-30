<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Classification;

class LainnyaCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create LAINNYA category for non-JRA archives
        $lainnyaCategory = Category::firstOrCreate(
            ['nama_kategori' => 'LAINNYA'],
            ['nama_kategori' => 'LAINNYA']
        );

        // Create LAINNYA classification under LAINNYA category
        Classification::firstOrCreate(
            [
                'category_id' => $lainnyaCategory->id,
                'code' => 'LAINNYA'
            ],
            [
                'category_id' => $lainnyaCategory->id,
                'code' => 'LAINNYA',
                'nama_klasifikasi' => 'LAINNYA - Kategori di Luar JRA',
                'retention_aktif' => 0, // Will be set manually
                'retention_inaktif' => 0, // Will be set manually
                'nasib_akhir' => 'Dinilai Kembali' // Default for manual entries
            ]
        );

        $this->command->info('LAINNYA category and classification created successfully.');
    }
}
