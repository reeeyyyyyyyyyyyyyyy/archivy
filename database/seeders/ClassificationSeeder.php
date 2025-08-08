<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Classification;

class ClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classification::truncate();

        $category1 = Category::where('nama_kategori', 'UMUM')->first();
        $category2 = Category::where('nama_kategori', 'KEUANGAN')->first();

        $classifications = [
            [
                'category_id' => $category1->id,
                'code' => '000.1',
                'nama_klasifikasi' => 'Surat Masuk',
                'retention_aktif' => 2,
                'retention_inaktif' => 3,
                'nasib_akhir' => 'Musnah',
            ],
            [
                'category_id' => $category1->id,
                'code' => '000.2',
                'nama_klasifikasi' => 'Surat Keluar',
                'retention_aktif' => 3,
                'retention_inaktif' => 2,
                'nasib_akhir' => 'Musnah',
            ],
            [
                'category_id' => $category2->id,
                'code' => '100.1',
                'nama_klasifikasi' => 'Laporan Keuangan',
                'retention_aktif' => 5,
                'retention_inaktif' => 5,
                'nasib_akhir' => 'Permanen',
            ],
        ];

        foreach ($classifications as $classification) {
            Classification::create($classification);
        }
    }
}
