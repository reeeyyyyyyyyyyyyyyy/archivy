<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Carbon\Carbon;

class TestRelatedArchiveNasibAkhirCommand extends Command
{
    protected $signature = 'test:related-archive-nasib-akhir';
    protected $description = 'Test nasib akhir pada arsip terkait dengan kategori LAINNYA';

    public function handle()
    {
        $this->info('🧪 Test: Nasib Akhir pada Arsip Terkait (LAINNYA Category)...');

        // 1. Cari atau buat kategori LAINNYA
        $category = Category::where('nama_kategori', 'LAINNYA')->first();
        if (!$category) {
            $category = Category::create([
                'nama_kategori' => 'LAINNYA',
                'code' => 'LAINNYA'
            ]);
            $this->info('✅ Kategori LAINNYA berhasil dibuat!');
        } else {
            $this->info('✅ Kategori LAINNYA sudah ada!');
        }

        // 2. Cari atau buat klasifikasi LAINNYA
        $classification = Classification::where('code', 'LAINNYA')->first();
        if (!$classification) {
            $classification = Classification::create([
                'category_id' => $category->id,
                'code' => 'LAINNYA',
                'nama_klasifikasi' => 'LAINNYA',
                'retention_aktif' => 1,
                'retention_inaktif' => 1,
                'nasib_akhir' => 'Dinilai Kembali' // Default untuk JRA
            ]);
            $this->info('✅ Klasifikasi LAINNYA berhasil dibuat!');
        } else {
            $this->info('✅ Klasifikasi LAINNYA sudah ada!');
        }

        // 3. Cari user admin
        $user = User::where('role_type', 'admin')->first();
        if (!$user) {
            $this->error('❌ User admin tidak ditemukan!');
            return;
        }

        // 4. Buat arsip parent dengan nasib akhir MUSNAH
        $parentArchive = Archive::create([
            'category_id' => $category->id,
            'classification_id' => $classification->id,
            'index_number' => 'TEST/LAINNYA/PARENT/' . time(),
            'description' => 'Arsip Parent LAINNYA - Nasib Akhir Musnah',
            'lampiran_surat' => 'SK-TEST-001',
            'kurun_waktu_start' => '2004-01-01', // Tahun 2004 untuk test nasib akhir
            'tingkat_perkembangan' => 'Asli',
            'skkad' => 'RAHASIA',
            'jumlah_berkas' => 1,
            'ket' => 'Test arsip parent',
            'is_manual_input' => true,
            'manual_retention_aktif' => 3,
            'manual_retention_inaktif' => 7,
            'manual_nasib_akhir' => 'Musnah', // Manual nasib akhir MUSNAH
            'retention_aktif' => 3,
            'retention_inaktif' => 7,
            'transition_active_due' => Carbon::parse('2004-01-01')->addYears(3), // 2007
            'transition_inactive_due' => Carbon::parse('2004-01-01')->addYears(10), // 2014
            'status' => 'Musnah', // Harusnya masuk nasib akhir karena sudah lewat 2014
            'is_parent' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $this->info('✅ Arsip Parent berhasil dibuat dengan nasib akhir MUSNAH!');
        $this->info("   - ID: {$parentArchive->id}");
        $this->info("   - Manual Nasib Akhir: {$parentArchive->manual_nasib_akhir}");
        $this->info("   - Status: {$parentArchive->status}");

        // 5. Buat arsip terkait (simulasi createRelated)
        $relatedArchive = Archive::create([
            'category_id' => $category->id,
            'classification_id' => $classification->id,
            'lampiran_surat' => 'SK-TEST-001', // Sama dengan parent
            'parent_archive_id' => $parentArchive->id,
            'is_parent' => false,
            'index_number' => 'TEST/LAINNYA/RELATED/' . time(),
            'description' => 'Arsip Terkait LAINNYA - Test Nasib Akhir',
            'kurun_waktu_start' => '2005-01-01', // Tahun 2005 untuk test nasib akhir
            'tingkat_perkembangan' => 'Asli',
            'jumlah_berkas' => 1,
            'skkad' => 'RAHASIA',
            'ket' => 'Test arsip terkait',
            'retention_aktif' => $parentArchive->retention_aktif,
            'retention_inaktif' => $parentArchive->retention_inaktif,
            'transition_active_due' => Carbon::parse('2005-01-01')->addYears($parentArchive->retention_aktif), // 2008
            'transition_inactive_due' => Carbon::parse('2005-01-01')->addYears($parentArchive->retention_aktif + $parentArchive->retention_inaktif), // 2015
            'status' => 'Musnah', // Harusnya masuk nasib akhir karena sudah lewat 2015
            'manual_nasib_akhir' => $parentArchive->manual_nasib_akhir, // Ambil dari parent
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $this->info('✅ Arsip Terkait berhasil dibuat!');
        $this->info("   - ID: {$relatedArchive->id}");
        $this->info("   - Manual Nasib Akhir: {$relatedArchive->manual_nasib_akhir}");
        $this->info("   - Status: {$relatedArchive->status}");

        // 6. Cek nasib akhir pada kedua arsip
        $this->info('🔍 Mengecek nasib akhir pada kedua arsip...');

        $parentArchive->refresh();
        $relatedArchive->refresh();

        $this->info("📋 Arsip Parent:");
        $this->info("   - Manual Nasib Akhir: {$parentArchive->manual_nasib_akhir}");
        $this->info("   - Status: {$parentArchive->status}");
        $this->info("   - Is Parent: " . ($parentArchive->is_parent ? 'Ya' : 'Tidak'));

        $this->info("📋 Arsip Terkait:");
        $this->info("   - Manual Nasib Akhir: {$relatedArchive->manual_nasib_akhir}");
        $this->info("   - Status: {$relatedArchive->status}");
        $this->info("   - Parent Archive ID: {$relatedArchive->parent_archive_id}");

        // 7. Test apakah nasib akhir sesuai
        if ($parentArchive->manual_nasib_akhir === 'Musnah' && $relatedArchive->manual_nasib_akhir === 'Musnah') {
            $this->info('✅ SUCCESS: Kedua arsip memiliki nasib akhir MUSNAH!');
        } else {
            $this->error('❌ ERROR: Nasib akhir tidak sesuai!');
            $this->error("   Parent: {$parentArchive->manual_nasib_akhir}");
            $this->error("   Related: {$relatedArchive->manual_nasib_akhir}");
        }

        // 8. Cek apakah ada arsip dengan status Dinilai Kembali yang tidak seharusnya
        $dinilaiKembaliCount = Archive::where('manual_nasib_akhir', 'Dinilai Kembali')
            ->whereIn('id', [$parentArchive->id, $relatedArchive->id])
            ->count();

        if ($dinilaiKembaliCount > 0) {
            $this->error("❌ ERROR: Ada {$dinilaiKembaliCount} arsip dengan nasib akhir 'Dinilai Kembali' yang seharusnya 'Musnah'!");
        } else {
            $this->info('✅ SUCCESS: Tidak ada arsip dengan nasib akhir yang salah!');
        }

        $this->info('🎯 Test selesai!');
    }
}
