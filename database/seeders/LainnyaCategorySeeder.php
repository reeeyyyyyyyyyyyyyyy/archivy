<?php

namespace Database\Seeders;

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
        // Buat kategori 'LAINNYA' jika belum ada
        $lainnyaCategory = Category::firstOrCreate(
            ['nama_kategori' => 'LAINNYA']
        );

        // Buat klasifikasi 'LAINNYA' jika belum ada
        Classification::firstOrCreate(
            [
                'category_id' => $lainnyaCategory->id,
                'code' => 'LAINNYA',
            ],
            [
                'nama_klasifikasi' => 'LAINNYA - Kategori di Luar JRA',
                'retention_aktif' => 0,
                'retention_inaktif' => 0,
                'nasib_akhir' => 'Dinilai Kembali',
            ]
        );

        $this->command->info('LAINNYA category and classification created successfully.');
    }
}
