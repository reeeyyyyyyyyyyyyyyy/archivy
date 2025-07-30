<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Classification;
use App\Models\Archive;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TestArchiveDinilaiKembaliCommand extends Command
{
    protected $signature = 'test:arsip-dinilai-kembali';
    protected $description = 'Test pembuatan klasifikasi dan arsip dengan nasib akhir Dinilai Kembali';

    public function handle()
    {
        $this->info('🧪 Test: Klasifikasi & Arsip Dinilai Kembali...');
        $user = User::where('email', 'admin@arsipin.id')->first();
        Auth::login($user);

        // Clean up old test data
        $oldClass = Classification::where('code', 'TEST-DINILAI')->first();
        if ($oldClass) {
            Archive::where('classification_id', $oldClass->id)->delete();
            $oldClass->delete();
        }

        // 1. Buat klasifikasi baru
        $category = Category::first();
        $classification = Classification::create([
            'category_id' => $category->id,
            'code' => 'TEST-DINILAI',
            'nama_klasifikasi' => 'Test Dinilai Kembali',
            'retention_aktif' => 1,
            'retention_inaktif' => 1,
            'nasib_akhir' => 'Dinilai Kembali',
        ]);
        $this->info('✅ Klasifikasi Dinilai Kembali berhasil dibuat!');

        // 2. Buat arsip dengan klasifikasi tersebut
        $archive = Archive::create([
            'classification_id' => $classification->id,
            'category_id' => $category->id,
            'index_number' => 'TEST/DINILAI/001',
            'description' => 'Arsip Dinilai Kembali',
            'lampiran_surat' => '-',
            'kurun_waktu_start' => '2025-01-01',
            'tingkat_perkembangan' => 'Asli',
            'skkad' => 'RAHASIA',
            'jumlah_berkas' => 1,
            'ket' => '-',
            'is_manual_input' => false,
            'retention_aktif' => 1,
            'retention_inaktif' => 1,
            'transition_active_due' => Carbon::parse('2025-01-01')->addYears(1),
            'transition_inactive_due' => Carbon::parse('2025-01-01')->addYears(2),
            'status' => 'Dinilai Kembali',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $this->info('✅ Arsip Dinilai Kembali berhasil dibuat!');

        // 3. Cek apakah arsip masuk ke fitur arsip dinilai kembali
        $count = Archive::where('status', 'Dinilai Kembali')->count();
        $this->info("🔎 Jumlah arsip status 'Dinilai Kembali': $count");
        if ($count > 0) {
            $this->info('✅ Arsip sudah masuk ke fitur Arsip Dinilai Kembali!');
        } else {
            $this->error('❌ Arsip TIDAK masuk ke fitur Arsip Dinilai Kembali!');
        }
    }
}
