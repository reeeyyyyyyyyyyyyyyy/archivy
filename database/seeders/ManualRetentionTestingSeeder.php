<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Archive;
use App\Models\User;
use Carbon\Carbon;

class ManualRetentionTestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧪 Creating Manual Retention Testing Data...');

        // Get LAINNYA category
        $lainnyaCategory = Category::where('nama_kategori', 'LAINNYA')->first();

        if (!$lainnyaCategory) {
            $this->command->error('LAINNYA category not found! Please run LainnyaCategorySeeder first.');
            return;
        }

        // Create test classifications for LAINNYA
        $classifications = [
            [
                'category_id' => $lainnyaCategory->id,
                'code' => 'LAINNYA.001',
                'nama_klasifikasi' => 'Dokumen Khusus - Musnah',
                'retention_aktif' => 0,
                'retention_inaktif' => 0,
                'nasib_akhir' => 'Musnah',
            ],
            [
                'category_id' => $lainnyaCategory->id,
                'code' => 'LAINNYA.002',
                'nama_klasifikasi' => 'Dokumen Khusus - Permanen',
                'retention_aktif' => 5,
                'retention_inaktif' => 10,
                'nasib_akhir' => 'Permanen',
            ],
            [
                'category_id' => $lainnyaCategory->id,
                'code' => 'LAINNYA.003',
                'nama_klasifikasi' => 'Dokumen Khusus - Dinilai Kembali',
                'retention_aktif' => 3,
                'retention_inaktif' => 5,
                'nasib_akhir' => 'Dinilai Kembali',
            ],
        ];

        foreach ($classifications as $classification) {
            Classification::firstOrCreate(
                ['code' => $classification['code']],
                $classification
            );
        }

        $this->command->info('✅ Test classifications created for LAINNYA category');

        // Get admin user
        $adminUser = User::where('email', 'admin@arsipin.id')->first();

        // Create test archives with manual retention
        $testArchives = [
            [
                'index_number' => 'MANUAL-001',
                'description' => 'Dokumen testing manual retention - Musnah',
                'kurun_waktu_start' => '2020-01-01',
                'tingkat_perkembangan' => 'Asli',
                'skkad' => 'BIASA/TERBUKA',
                'jumlah_berkas' => 1,
                'is_manual_input' => true,
                'manual_retention_aktif' => 2,
                'manual_retention_inaktif' => 3,
                'manual_nasib_akhir' => 'Musnah',
                'status' => 'Aktif',
            ],
            [
                'index_number' => 'MANUAL-002',
                'description' => 'Dokumen testing manual retention - Permanen',
                'kurun_waktu_start' => '2018-01-01',
                'tingkat_perkembangan' => 'Asli',
                'skkad' => 'BIASA/TERBUKA',
                'jumlah_berkas' => 1,
                'is_manual_input' => true,
                'manual_retention_aktif' => 5,
                'manual_retention_inaktif' => 10,
                'manual_nasib_akhir' => 'Permanen',
                'status' => 'Aktif',
            ],
            [
                'index_number' => 'MANUAL-003',
                'description' => 'Dokumen testing manual retention - Dinilai Kembali',
                'kurun_waktu_start' => '2022-01-01',
                'tingkat_perkembangan' => 'Asli',
                'skkad' => 'BIASA/TERBUKA',
                'jumlah_berkas' => 1,
                'is_manual_input' => true,
                'manual_retention_aktif' => 3,
                'manual_retention_inaktif' => 5,
                'manual_nasib_akhir' => 'Dinilai Kembali',
                'status' => 'Aktif',
            ],
        ];

        foreach ($testArchives as $archiveData) {
            // Get classification for this archive
            $classification = Classification::where('code', 'LAINNYA.001')->first();

            // Calculate transition dates based on manual retention
            $kurunWaktuStart = Carbon::parse($archiveData['kurun_waktu_start']);
            $transitionActiveDue = $kurunWaktuStart->copy()->addYears($archiveData['manual_retention_aktif']);
            $transitionInactiveDue = $transitionActiveDue->copy()->addYears($archiveData['manual_retention_inaktif']);

            // Create archive
            Archive::create([
                'category_id' => $lainnyaCategory->id,
                'classification_id' => $classification->id,
                'index_number' => $archiveData['index_number'],
                'description' => $archiveData['description'],
                'kurun_waktu_start' => $archiveData['kurun_waktu_start'],
                'tingkat_perkembangan' => $archiveData['tingkat_perkembangan'],
                'skkad' => $archiveData['skkad'],
                'jumlah_berkas' => $archiveData['jumlah_berkas'],
                'is_manual_input' => $archiveData['is_manual_input'],
                'manual_retention_aktif' => $archiveData['manual_retention_aktif'],
                'manual_retention_inaktif' => $archiveData['manual_retention_inaktif'],
                'manual_nasib_akhir' => $archiveData['manual_nasib_akhir'],
                'retention_aktif' => $archiveData['manual_retention_aktif'],
                'retention_inaktif' => $archiveData['manual_retention_inaktif'],
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'status' => $archiveData['status'],
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ]);
        }

        $this->command->info('✅ Test archives created with manual retention settings');
        $this->command->info('📋 Manual Retention Testing Data Summary:');
        $this->command->info('   - 3 test classifications for LAINNYA category');
        $this->command->info('   - 3 test archives with manual retention');
        $this->command->info('   - Different nasib_akhir: Musnah, Permanen, Dinilai Kembali');
        $this->command->info('   - Ready for testing manual input features');
    }
}
