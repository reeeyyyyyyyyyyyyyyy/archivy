<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archive;
use App\Models\Classification;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TestArchiveCreationCommand extends Command
{
    protected $signature = 'test:archive-create {--jra} {--lainnya}';
    protected $description = 'Test archive creation logic for JRA and LAINNYA';

    public function handle()
    {
        $this->info('🧪 Testing Archive Creation Logic...');

        // Get admin user
        $user = User::where('email', 'admin@arsipin.id')->first();
        if (!$user) {
            $this->error('❌ Admin user not found!');
            return 1;
        }

        Auth::login($user);

        // Test JRA Archive Creation
        $this->info('📋 Testing JRA Archive Creation...');
        try {
            $jraClassification = Classification::where('code', '!=', 'LAINNYA')->first();
            if (!$jraClassification) {
                $this->error('❌ Tidak ada klasifikasi JRA ditemukan!');
                return;
            }

            $userInput = '006/SKPD'; // User input format
            $kurunWaktuStart = '2025-01-01';

            // Generate unique index number
            $uniqueSuffix = time() . rand(100, 999);
            $indexNumber = $jraClassification->code . '/' . $userInput . '/' . date('Y') . '/' . $uniqueSuffix;

            $archive = Archive::create([
                'classification_id' => $jraClassification->id,
                'category_id' => $jraClassification->category_id,
                'index_number' => $indexNumber,
                'description' => 'Test JRA Archive via Command',
                'lampiran_surat' => 'Test lampiran JRA',
                'kurun_waktu_start' => $kurunWaktuStart,
                'tingkat_perkembangan' => 'Asli',
                'skkad' => 'BIASA/TERBUKA',
                'jumlah_berkas' => 1,
                'ket' => 'Test JRA via command',
                'is_manual_input' => false,
                'retention_aktif' => $jraClassification->retention_aktif,
                'retention_inaktif' => $jraClassification->retention_inaktif,
                'transition_active_due' => Carbon::parse($kurunWaktuStart)->addYears($jraClassification->retention_aktif),
                'transition_inactive_due' => Carbon::parse($kurunWaktuStart)->addYears($jraClassification->retention_aktif + $jraClassification->retention_inaktif),
                'status' => 'Aktif',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            $this->info('✅ JRA Archive berhasil dibuat!');
            $this->info("   - Index Number: {$archive->index_number}");
            $this->info("   - Status: {$archive->status}");
            $this->info("   - Retention Aktif: {$archive->retention_aktif} tahun");
            $this->info("   - Retention Inaktif: {$archive->retention_inaktif} tahun");

        } catch (\Exception $e) {
            $this->error("❌ JRA Archive creation failed: " . $e->getMessage());
        }

        // Test LAINNYA Archive Creation
        $this->info('📋 Testing LAINNYA Archive Creation...');
        try {
            $lainnyaClassification = Classification::where('code', 'LAINNYA')->first();
            if (!$lainnyaClassification) {
                $this->error('❌ Tidak ada klasifikasi LAINNYA ditemukan!');
                return;
            }

            $lainnyaCategory = Category::where('nama_kategori', 'LAINNYA')->first();
            if (!$lainnyaCategory) {
                $this->error('❌ Tidak ada kategori LAINNYA ditemukan!');
                return;
            }

            // Generate unique index number for LAINNYA
            $uniqueSuffix = time() . rand(100, 999);
            $indexNumber = 'DOK/' . $uniqueSuffix . '/SKPD/' . date('Y');

            $archive = Archive::create([
                'classification_id' => $lainnyaClassification->id,
                'category_id' => $lainnyaCategory->id,
                'index_number' => $indexNumber,
                'description' => 'Test LAINNYA Archive via Command',
                'lampiran_surat' => 'Test lampiran LAINNYA',
                'kurun_waktu_start' => '2025-01-01',
                'tingkat_perkembangan' => 'Manual Test',
                'skkad' => 'RAHASIA',
                'jumlah_berkas' => 1,
                'ket' => 'Test LAINNYA via command',
                'is_manual_input' => true,
                'manual_retention_aktif' => 2,
                'manual_retention_inaktif' => 3,
                'manual_nasib_akhir' => 'Permanen',
                'retention_aktif' => 2,
                'retention_inaktif' => 3,
                'transition_active_due' => Carbon::parse('2025-01-01')->addYears(2),
                'transition_inactive_due' => Carbon::parse('2025-01-01')->addYears(5),
                'status' => 'Aktif',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            $this->info('✅ LAINNYA Archive berhasil dibuat!');
            $this->info("   - Index Number: {$archive->index_number}");
            $this->info("   - Status: {$archive->status}");
            $this->info("   - Manual Retention Aktif: {$archive->manual_retention_aktif} tahun");
            $this->info("   - Manual Retention Inaktif: {$archive->manual_retention_inaktif} tahun");
            $this->info("   - Manual Nasib Akhir: {$archive->manual_nasib_akhir}");

        } catch (\Exception $e) {
            $this->error("❌ LAINNYA Archive creation failed: " . $e->getMessage());
        }

        $this->info('✅ Archive creation tests completed!');
        return 0;
    }

    private function testJRAArchive()
    {
        $this->info('📋 Testing JRA Archive Creation...');

        try {
            $classification = Classification::with('category')->find(1);
            if (!$classification) {
                $this->error('❌ Classification not found!');
                return;
            }

            $userInput = '006/SKPD';
            $kurunWaktuStart = '2025-01-01';

            // Simulate controller logic
            $indexNumber = $this->generateAutoIndexNumber($classification, $userInput, $kurunWaktuStart);

            $archiveData = [
                'classification_id' => $classification->id,
                'category_id' => $classification->category->id,
                'index_number' => $indexNumber,
                'description' => 'Test JRA Archive via Command',
                'lampiran_surat' => 'Test lampiran JRA',
                'kurun_waktu_start' => $kurunWaktuStart,
                'tingkat_perkembangan' => 'Asli',
                'skkad' => 'BIASA/TERBUKA',
                'jumlah_berkas' => 1,
                'ket' => 'Test JRA via command',
                'is_manual_input' => false,
                'retention_aktif' => $classification->retention_aktif,
                'retention_inaktif' => $classification->retention_inaktif,
                'transition_active_due' => Carbon::parse($kurunWaktuStart)->addYears($classification->retention_aktif),
                'transition_inactive_due' => Carbon::parse($kurunWaktuStart)->addYears($classification->retention_aktif + $classification->retention_inaktif),
                'status' => 'Aktif',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            $archive = Archive::create($archiveData);

            $this->info("✅ JRA Archive created successfully!");
            $this->info("   📄 Index Number: {$archive->index_number}");
            $this->info("   📝 Description: {$archive->description}");
            $this->info("   🏷️ Status: {$archive->status}");

        } catch (\Exception $e) {
            $this->error("❌ JRA Archive creation failed: " . $e->getMessage());
        }
    }

    private function testLainnyaArchive()
    {
        $this->info('📋 Testing LAINNYA Archive Creation...');

        try {
            // Find LAINNYA classification
            $lainnyaClassification = Classification::where('code', 'LAINNYA')->first();
            if (!$lainnyaClassification) {
                $this->error('❌ LAINNYA classification not found!');
                return;
            }

            $archiveData = [
                'classification_id' => $lainnyaClassification->id,
                'category_id' => $lainnyaClassification->category_id,
                'index_number' => 'DOK/004/SKPD/2025',
                'description' => 'Test LAINNYA Archive via Command',
                'lampiran_surat' => 'Test lampiran LAINNYA',
                'kurun_waktu_start' => '2025-01-01',
                'tingkat_perkembangan' => 'Manual Test',
                'skkad' => 'RAHASIA',
                'jumlah_berkas' => 1,
                'ket' => 'Test LAINNYA via command',
                'is_manual_input' => true,
                'manual_retention_aktif' => 2,
                'manual_retention_inaktif' => 3,
                'manual_nasib_akhir' => 'Permanen',
                'retention_aktif' => 2,
                'retention_inaktif' => 3,
                'transition_active_due' => Carbon::parse('2025-01-01')->addYears(2),
                'transition_inactive_due' => Carbon::parse('2025-01-01')->addYears(5),
                'status' => 'Aktif',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            $archive = Archive::create($archiveData);

            $this->info("✅ LAINNYA Archive created successfully!");
            $this->info("   📄 Index Number: {$archive->index_number}");
            $this->info("   📝 Description: {$archive->description}");
            $this->info("   🏷️ Status: {$archive->status}");
            $this->info("   📊 Manual Retention: {$archive->manual_retention_aktif}/{$archive->manual_retention_inaktif}");

        } catch (\Exception $e) {
            $this->error("❌ LAINNYA Archive creation failed: " . $e->getMessage());
        }
    }

    private function generateAutoIndexNumber($classification, $userInput, $kurunWaktuStart)
    {
        $year = Carbon::parse($kurunWaktuStart)->year;
        return "{$classification->code}/{$userInput}/{$year}";
    }
}
