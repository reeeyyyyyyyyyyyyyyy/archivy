<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Ketatausahaan dan Kerumahtanggaan'],
            ['nama_kategori' => 'Keuangan dan Aset'],
            ['nama_kategori' => 'Kepegawaian dan Umum'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
