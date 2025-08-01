<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            [
                'nama_kategori' => 'Penyelenggaraan Pemerintahan',
                'retention_aktif' => 5,
                'retention_inaktif' => 5,
                'nasib_akhir' => 'Musnah',
                'detailed_nasib_akhir' => 'Arsip penyelenggaraan pemerintahan rutin yang tidak memiliki nilai guna berkelanjutan'
            ],
            [
                'nama_kategori' => 'Kepegawaian',
                'retention_aktif' => 10,
                'retention_inaktif' => 10,
                'nasib_akhir' => 'Permanen',
                'detailed_nasib_akhir' => 'Arsip kepegawaian memiliki nilai guna berkelanjutan untuk administrasi dan hukum'
            ],
            [
                'nama_kategori' => 'Keuangan',
                'retention_aktif' => 7,
                'retention_inaktif' => 7,
                'nasib_akhir' => 'Permanen',
                'detailed_nasib_akhir' => 'Arsip keuangan memiliki nilai guna berkelanjutan untuk audit dan pelaporan'
            ],
            [
                'nama_kategori' => 'Perencanaan dan Pengembangan',
                'retention_aktif' => 8,
                'retention_inaktif' => 8,
                'nasib_akhir' => 'Permanen',
                'detailed_nasib_akhir' => 'Arsip perencanaan memiliki nilai guna berkelanjutan untuk evaluasi dan pengembangan'
            ],
            [
                'nama_kategori' => 'Pelayanan Publik',
                'retention_aktif' => 3,
                'retention_inaktif' => 3,
                'nasib_akhir' => 'Musnah',
                'detailed_nasib_akhir' => 'Arsip pelayanan publik rutin yang tidak memiliki nilai guna berkelanjutan'
            ],
            [
                'nama_kategori' => 'Pengawasan dan Pengendalian',
                'retention_aktif' => 6,
                'retention_inaktif' => 6,
                'nasib_akhir' => 'Permanen',
                'detailed_nasib_akhir' => 'Arsip pengawasan memiliki nilai guna berkelanjutan untuk audit dan evaluasi'
            ],
            [
                'nama_kategori' => 'Kerjasama dan Hubungan Luar',
                'retention_aktif' => 4,
                'retention_inaktif' => 4,
                'nasib_akhir' => 'Dinilai Kembali',
                'detailed_nasib_akhir' => 'Arsip kerjasama perlu dinilai kembali berdasarkan nilai guna dan kepentingan'
            ],
            [
                'nama_kategori' => 'Infrastruktur dan Sarana Prasarana',
                'retention_aktif' => 9,
                'retention_inaktif' => 9,
                'nasib_akhir' => 'Permanen',
                'detailed_nasib_akhir' => 'Arsip infrastruktur memiliki nilai guna berkelanjutan untuk perencanaan dan maintenance'
            ],
            [
                'nama_kategori' => 'Sosial dan Kesejahteraan',
                'retention_aktif' => 5,
                'retention_inaktif' => 5,
                'nasib_akhir' => 'Musnah',
                'detailed_nasib_akhir' => 'Arsip sosial rutin yang tidak memiliki nilai guna berkelanjutan'
            ],
            [
                'nama_kategori' => 'Lingkungan Hidup',
                'retention_aktif' => 7,
                'retention_inaktif' => 7,
                'nasib_akhir' => 'Permanen',
                'detailed_nasib_akhir' => 'Arsip lingkungan hidup memiliki nilai guna berkelanjutan untuk monitoring dan evaluasi'
            ]
        ];

        $category = $this->faker->unique()->randomElement($categories);

        return [
            'nama_kategori' => $category['nama_kategori'],
            'retention_aktif' => $category['retention_aktif'],
            'retention_inaktif' => $category['retention_inaktif'],
            'nasib_akhir' => $category['nasib_akhir'],
            'detailed_nasib_akhir' => $category['detailed_nasib_akhir'],
        ];
    }
}
