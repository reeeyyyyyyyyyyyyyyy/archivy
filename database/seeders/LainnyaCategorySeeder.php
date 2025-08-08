<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Classification;
use Illuminate\Support\Facades\DB;

class LainnyaCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Reset sequence categories (opsional, untuk jaga-jaga)
        DB::statement("SELECT setval('categories_id_seq', (SELECT MAX(id) FROM categories))");

        // Tambahkan kategori LAINNYA jika belum ada
        $lainnyaCategory = Category::firstOrCreate(
            ['nama_kategori' => 'LAINNYA'],
            ['nama_kategori' => 'LAINNYA']
        );

        // Tambahkan klasifikasi LAINNYA jika belum ada
        Classification::firstOrCreate(
            [
                'category_id' => $lainnyaCategory->id,
                'code' => 'LAINNYA'
            ],
            [
                'category_id' => $lainnyaCategory->id,
                'code' => 'LAINNYA',
                'nama_klasifikasi' => 'LAINNYA - Kategori di Luar JRA',
                'retention_aktif' => 0,
                'retention_inaktif' => 0,
                'nasib_akhir' => 'Dinilai Kembali'
            ]
        );

        // Reset sequence classification (opsional juga)
        DB::statement("SELECT setval('classifications_id_seq', (SELECT MAX(id) FROM classifications))");

        $this->command->info('LAINNYA category and classification created successfully.');
    }
}
