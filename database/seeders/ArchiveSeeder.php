<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Archive;
use App\Models\Classification;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ArchiveSeeder extends Seeder
{
    public function run(): void
    {
        Archive::truncate();

        $admin = User::where('email', 'admin@arsipin.id')->first();
        $staff = User::where('email', 'staff@arsipin.id')->first();
        $intern = User::where('email', 'intern@arsipin.id')->first();

        $users = [$admin, $staff, $intern];

        $classifications = Classification::with('category')->get();

        if ($classifications->count() === 0) {
            $this->command->warn('No classifications found. Please run ClassificationSeeder first.');
            return;
        }

        // Buat 50 arsip
        for ($i = 1; $i <= 50; $i++) {
            $classification = $classifications->random();
            $category = $classification->category;
            $user = collect($users)->random();

            $tanggal = Carbon::now()->subMonths(rand(1, 24))->startOfMonth();

            Archive::create([
                'category_id' => $category->id,
                'classification_id' => $classification->id,
                'index_number' => 'ARSIP-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'description' => 'Dokumen ke-' . $i . ' terkait ' . Str::lower($classification->nama_klasifikasi),
                'kurun_waktu_start' => $tanggal,
                'tingkat_perkembangan' => collect(['Asli', 'Salinan', 'Tembusan'])->random(),
                'jumlah_berkas' => rand(1, 10),
                'ket' => 'Keterangan tambahan untuk arsip ke-' . $i,
                'retention_aktif' => $classification->retention_aktif,
                'retention_inaktif' => $classification->retention_inaktif,
                'transition_active_due' => $tanggal->copy()->addYears($classification->retention_aktif),
                'transition_inactive_due' => $tanggal->copy()->addYears($classification->retention_aktif + $classification->retention_inaktif),
                'status' => collect(['Aktif', 'Inaktif', 'Permanen', 'Musnah'])->random(),
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }

        $this->command->info('50 arsip berhasil dibuat!');
    }
}
