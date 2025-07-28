<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classification>
 */
class ClassificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classifications = [
            // Penyelenggaraan Pemerintahan
            [
                'nama_klasifikasi' => 'Rapat Koordinasi Pimpinan',
                'code' => '01.01',
                'retention_aktif' => 5,
                'retention_inaktif' => 5,
                'category_name' => 'Penyelenggaraan Pemerintahan'
            ],
            [
                'nama_klasifikasi' => 'Rapat Staf',
                'code' => '01.02',
                'retention_aktif' => 3,
                'retention_inaktif' => 3,
                'category_name' => 'Penyelenggaraan Pemerintahan'
            ],
            [
                'nama_klasifikasi' => 'Laporan Bulanan',
                'code' => '01.03',
                'retention_aktif' => 2,
                'retention_inaktif' => 2,
                'category_name' => 'Penyelenggaraan Pemerintahan'
            ],

            // Kepegawaian
            [
                'nama_klasifikasi' => 'Pengangkatan Pegawai',
                'code' => '02.01',
                'retention_aktif' => 10,
                'retention_inaktif' => 10,
                'category_name' => 'Kepegawaian'
            ],
            [
                'nama_klasifikasi' => 'Kenaikan Pangkat',
                'code' => '02.02',
                'retention_aktif' => 10,
                'retention_inaktif' => 10,
                'category_name' => 'Kepegawaian'
            ],
            [
                'nama_klasifikasi' => 'Daftar Urut Kepangkatan',
                'code' => '02.03',
                'retention_aktif' => 5,
                'retention_inaktif' => 5,
                'category_name' => 'Kepegawaian'
            ],
            [
                'nama_klasifikasi' => 'Penilaian Prestasi Kerja',
                'code' => '02.04',
                'retention_aktif' => 10,
                'retention_inaktif' => 10,
                'category_name' => 'Kepegawaian'
            ],

            // Keuangan
            [
                'nama_klasifikasi' => 'Anggaran Pendapatan dan Belanja',
                'code' => '03.01',
                'retention_aktif' => 7,
                'retention_inaktif' => 7,
                'category_name' => 'Keuangan'
            ],
            [
                'nama_klasifikasi' => 'Laporan Keuangan',
                'code' => '03.02',
                'retention_aktif' => 7,
                'retention_inaktif' => 7,
                'category_name' => 'Keuangan'
            ],
            [
                'nama_klasifikasi' => 'Pengadaan Barang dan Jasa',
                'code' => '03.03',
                'retention_aktif' => 5,
                'retention_inaktif' => 5,
                'category_name' => 'Keuangan'
            ],
            [
                'nama_klasifikasi' => 'Perbendaharaan',
                'code' => '03.04',
                'retention_aktif' => 7,
                'retention_inaktif' => 7,
                'category_name' => 'Keuangan'
            ],

            // Perencanaan dan Pengembangan
            [
                'nama_klasifikasi' => 'Rencana Pembangunan Jangka Panjang',
                'code' => '04.01',
                'retention_aktif' => 8,
                'retention_inaktif' => 8,
                'category_name' => 'Perencanaan dan Pengembangan'
            ],
            [
                'nama_klasifikasi' => 'Rencana Pembangunan Jangka Menengah',
                'code' => '04.02',
                'retention_aktif' => 8,
                'retention_inaktif' => 8,
                'category_name' => 'Perencanaan dan Pengembangan'
            ],
            [
                'nama_klasifikasi' => 'Rencana Kerja Pemerintah',
                'code' => '04.03',
                'retention_aktif' => 5,
                'retention_inaktif' => 5,
                'category_name' => 'Perencanaan dan Pengembangan'
            ],

            // Pelayanan Publik
            [
                'nama_klasifikasi' => 'Pelayanan Perizinan',
                'code' => '05.01',
                'retention_aktif' => 3,
                'retention_inaktif' => 3,
                'category_name' => 'Pelayanan Publik'
            ],
            [
                'nama_klasifikasi' => 'Pelayanan Sertifikasi',
                'code' => '05.02',
                'retention_aktif' => 3,
                'retention_inaktif' => 3,
                'category_name' => 'Pelayanan Publik'
            ],
            [
                'nama_klasifikasi' => 'Pelayanan Informasi',
                'code' => '05.03',
                'retention_aktif' => 2,
                'retention_inaktif' => 2,
                'category_name' => 'Pelayanan Publik'
            ],

            // Pengawasan dan Pengendalian
            [
                'nama_klasifikasi' => 'Audit Internal',
                'code' => '06.01',
                'retention_aktif' => 6,
                'retention_inaktif' => 6,
                'category_name' => 'Pengawasan dan Pengendalian'
            ],
            [
                'nama_klasifikasi' => 'Inspeksi dan Evaluasi',
                'code' => '06.02',
                'retention_aktif' => 6,
                'retention_inaktif' => 6,
                'category_name' => 'Pengawasan dan Pengendalian'
            ],
            [
                'nama_klasifikasi' => 'Laporan Pengawasan',
                'code' => '06.03',
                'retention_aktif' => 6,
                'retention_inaktif' => 6,
                'category_name' => 'Pengawasan dan Pengendalian'
            ],

            // Kerjasama dan Hubungan Luar
            [
                'nama_klasifikasi' => 'Perjanjian Kerjasama',
                'code' => '07.01',
                'retention_aktif' => 4,
                'retention_inaktif' => 4,
                'category_name' => 'Kerjasama dan Hubungan Luar'
            ],
            [
                'nama_klasifikasi' => 'Kunjungan Kerja',
                'code' => '07.02',
                'retention_aktif' => 3,
                'retention_inaktif' => 3,
                'category_name' => 'Kerjasama dan Hubungan Luar'
            ],

            // Infrastruktur dan Sarana Prasarana
            [
                'nama_klasifikasi' => 'Perencanaan Infrastruktur',
                'code' => '08.01',
                'retention_aktif' => 9,
                'retention_inaktif' => 9,
                'category_name' => 'Infrastruktur dan Sarana Prasarana'
            ],
            [
                'nama_klasifikasi' => 'Pembangunan Infrastruktur',
                'code' => '08.02',
                'retention_aktif' => 9,
                'retention_inaktif' => 9,
                'category_name' => 'Infrastruktur dan Sarana Prasarana'
            ],
            [
                'nama_klasifikasi' => 'Pemeliharaan Infrastruktur',
                'code' => '08.03',
                'retention_aktif' => 7,
                'retention_inaktif' => 7,
                'category_name' => 'Infrastruktur dan Sarana Prasarana'
            ],

            // Sosial dan Kesejahteraan
            [
                'nama_klasifikasi' => 'Bantuan Sosial',
                'code' => '09.01',
                'retention_aktif' => 5,
                'retention_inaktif' => 5,
                'category_name' => 'Sosial dan Kesejahteraan'
            ],
            [
                'nama_klasifikasi' => 'Pelayanan Kesehatan',
                'code' => '09.02',
                'retention_aktif' => 5,
                'retention_inaktif' => 5,
                'category_name' => 'Sosial dan Kesejahteraan'
            ],

            // Lingkungan Hidup
            [
                'nama_klasifikasi' => 'Pengelolaan Lingkungan Hidup',
                'code' => '10.01',
                'retention_aktif' => 7,
                'retention_inaktif' => 7,
                'category_name' => 'Lingkungan Hidup'
            ],
            [
                'nama_klasifikasi' => 'Pengendalian Pencemaran',
                'code' => '10.02',
                'retention_aktif' => 7,
                'retention_inaktif' => 7,
                'category_name' => 'Lingkungan Hidup'
            ],
            [
                'nama_klasifikasi' => 'Konservasi Sumber Daya Alam',
                'code' => '10.03',
                'retention_aktif' => 7,
                'retention_inaktif' => 7,
                'category_name' => 'Lingkungan Hidup'
            ]
        ];

        $classification = $this->faker->unique()->randomElement($classifications);

        // Find category by name
        $category = Category::where('nama_kategori', $classification['category_name'])->first();

        if (!$category) {
            // If category doesn't exist, create it
            $category = Category::factory()->create([
                'nama_kategori' => $classification['category_name'],
                'retention_aktif' => $classification['retention_aktif'],
                'retention_inaktif' => $classification['retention_inaktif'],
                'nasib_akhir' => $this->getNasibAkhir($classification['category_name']),
                'detailed_nasib_akhir' => $this->getDetailedNasibAkhir($classification['category_name'])
            ]);
        }

        return [
            'category_id' => $category->id,
            'nama_klasifikasi' => $classification['nama_klasifikasi'],
            'code' => $classification['code'],
            'retention_aktif' => $classification['retention_aktif'],
            'retention_inaktif' => $classification['retention_inaktif'],
        ];
    }

    private function getNasibAkhir($categoryName): string
    {
        $nasibAkhirMap = [
            'Penyelenggaraan Pemerintahan' => 'Musnah',
            'Kepegawaian' => 'Permanen',
            'Keuangan' => 'Permanen',
            'Perencanaan dan Pengembangan' => 'Permanen',
            'Pelayanan Publik' => 'Musnah',
            'Pengawasan dan Pengendalian' => 'Permanen',
            'Kerjasama dan Hubungan Luar' => 'Dinilai Kembali',
            'Infrastruktur dan Sarana Prasarana' => 'Permanen',
            'Sosial dan Kesejahteraan' => 'Musnah',
            'Lingkungan Hidup' => 'Permanen'
        ];

        return $nasibAkhirMap[$categoryName] ?? 'Permanen';
    }

    private function getDetailedNasibAkhir($categoryName): string
    {
        $detailedMap = [
            'Penyelenggaraan Pemerintahan' => 'Arsip penyelenggaraan pemerintahan rutin yang tidak memiliki nilai guna berkelanjutan',
            'Kepegawaian' => 'Arsip kepegawaian memiliki nilai guna berkelanjutan untuk administrasi dan hukum',
            'Keuangan' => 'Arsip keuangan memiliki nilai guna berkelanjutan untuk audit dan pelaporan',
            'Perencanaan dan Pengembangan' => 'Arsip perencanaan memiliki nilai guna berkelanjutan untuk evaluasi dan pengembangan',
            'Pelayanan Publik' => 'Arsip pelayanan publik rutin yang tidak memiliki nilai guna berkelanjutan',
            'Pengawasan dan Pengendalian' => 'Arsip pengawasan memiliki nilai guna berkelanjutan untuk audit dan evaluasi',
            'Kerjasama dan Hubungan Luar' => 'Arsip kerjasama perlu dinilai kembali berdasarkan nilai guna dan kepentingan',
            'Infrastruktur dan Sarana Prasarana' => 'Arsip infrastruktur memiliki nilai guna berkelanjutan untuk perencanaan dan maintenance',
            'Sosial dan Kesejahteraan' => 'Arsip sosial rutin yang tidak memiliki nilai guna berkelanjutan',
            'Lingkungan Hidup' => 'Arsip lingkungan hidup memiliki nilai guna berkelanjutan untuk monitoring dan evaluasi'
        ];

        return $detailedMap[$categoryName] ?? 'Arsip memiliki nilai guna berkelanjutan';
    }
}
