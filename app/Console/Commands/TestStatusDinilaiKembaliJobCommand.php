<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Classification;
use App\Models\Archive;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Jobs\UpdateArchiveStatusJob;

class TestStatusDinilaiKembaliJobCommand extends Command
{
    protected $signature = 'test:status-dinilai-job';
    protected $description = 'Test logic job status Dinilai Kembali';

    public function handle()
    {
        $this->info('🧪 Test: Status Dinilai Kembali via Job...');
        $user = User::where('email', 'admin@arsipin.id')->first();
        Auth::login($user);
        $category = Category::first();
        $classification = Classification::firstOrCreate([
            'category_id' => $category->id,
            'code' => 'TEST-DINILAI-JOB',
        ], [
            'nama_klasifikasi' => 'Test Dinilai Kembali Job',
            'retention_aktif' => 1,
            'retention_inaktif' => 1,
            'nasib_akhir' => 'Dinilai Kembali',
        ]);
        // Create archive with Dinilai Kembali classification and past dates
        $archive = Archive::create([
            'classification_id' => $classification->id,
            'category_id' => $classification->category_id,
            'index_number' => 'TEST/DINILAI/JOB/' . time(), // Make unique
            'description' => 'Arsip Dinilai Kembali Job',
            'lampiran_surat' => '-',
            'kurun_waktu_start' => '2020-01-01',
            'tingkat_perkembangan' => 'Asli',
            'skkad' => 'RAHASIA',
            'jumlah_berkas' => 1,
            'ket' => '-',
            'is_manual_input' => false,
            'retention_aktif' => 1,
            'retention_inaktif' => 1,
            'transition_active_due' => '2021-01-01',
            'transition_inactive_due' => '2022-01-01',
            'status' => 'Inaktif', // Should change to Dinilai Kembali after job
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        $this->info('✅ Arsip Inaktif dengan due date lewat dibuat!');
        // Jalankan job
        (new UpdateArchiveStatusJob())->handle();
        $archive->refresh();
        $this->info('Status arsip setelah job: ' . $archive->status);
        if ($archive->status === 'Dinilai Kembali') {
            $this->info('✅ LOGIC BENAR: Status berubah ke Dinilai Kembali!');
        } else {
            $this->error('❌ LOGIC SALAH: Status tidak berubah ke Dinilai Kembali!');
        }
    }
}
