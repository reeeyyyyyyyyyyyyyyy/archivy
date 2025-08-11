<?php

namespace App\Console\Commands;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateTestArchivesCommand extends Command
{
    protected $signature = 'archives:generate-test {count=50}';
    protected $description = 'Generate test archives for testing related archives functionality';

    public function handle()
    {
        $count = (int) $this->argument('count');
        $this->info("Generating {$count} test archives...");

        // Get first category and classification
        $category = Category::first();
        $classification = Classification::first();
        $user = User::first();

        if (!$category || !$classification || !$user) {
            $this->error('Please ensure you have at least one category, classification, and user in the database.');
            return 1;
        }

        $years = [2016, 2017, 2018, 2019, 2020];
        $descriptions = [
            'Surat Permohonan Izin',
            'Laporan Keuangan Bulanan',
            'Dokumen Kontrak Kerja',
            'Surat Keputusan Direksi',
            'Laporan Audit Internal',
            'Dokumen Perizinan',
            'Surat Perjanjian Kerjasama',
            'Laporan Tahunan',
            'Dokumen Pengadaan',
            'Surat Edaran'
        ];

        $createdCount = 0;
        $parentArchive = null;

        for ($i = 1; $i <= $count; $i++) {
            $year = $years[array_rand($years)];
            $description = $descriptions[array_rand($descriptions)] . ' ' . $i;
            $indexNumber = 'TEST-' . str_pad($i, 3, '0', STR_PAD_LEFT);

            // Calculate retention dates
            $kurunWaktuStart = Carbon::createFromDate($year, rand(1, 12), rand(1, 28));
            $retentionAktif = $classification->retention_aktif ?? 5;
            $retentionInaktif = $classification->retention_inaktif ?? 10;
            $transitionActiveDue = $kurunWaktuStart->copy()->addYears($retentionAktif);
            $transitionInactiveDue = $transitionActiveDue->copy()->addYears($retentionInaktif);

            // Calculate status
            $now = Carbon::now();
            $status = 'Aktif';
            if ($now->gt($transitionInactiveDue)) {
                $status = 'Inaktif';
            } elseif ($now->gt($transitionActiveDue)) {
                $status = 'Inaktif';
            }

            // Determine if this should be parent or child
            $isParent = false;
            $parentArchiveId = null;

            if ($i === 1) {
                // First archive is always parent
                $isParent = true;
                $parentArchive = null;
            } elseif ($i <= 10) {
                // Archives 2-10 are children of first archive
                $isParent = false;
                $parentArchiveId = $parentArchive->id ?? null;
            } elseif ($i === 11) {
                // Archive 11 starts a new group
                $isParent = true;
                $parentArchive = null;
            } elseif ($i <= 20) {
                // Archives 12-20 are children of archive 11
                $isParent = false;
                $parentArchiveId = $parentArchive->id ?? null;
            } elseif ($i === 21) {
                // Archive 21 starts a new group
                $isParent = true;
                $parentArchive = null;
            } elseif ($i <= 30) {
                // Archives 22-30 are children of archive 21
                $isParent = false;
                $parentArchiveId = $parentArchive->id ?? null;
            } elseif ($i === 31) {
                // Archive 31 starts a new group
                $isParent = true;
                $parentArchive = null;
            } elseif ($i <= 40) {
                // Archives 32-40 are children of archive 31
                $isParent = false;
                $parentArchiveId = $parentArchive->id ?? null;
            } elseif ($i === 41) {
                // Archive 41 starts a new group
                $isParent = true;
                $parentArchive = null;
            } else {
                // Archives 42-50 are children of archive 41
                $isParent = false;
                $parentArchiveId = $parentArchive->id ?? null;
            }

            $archive = Archive::create([
                'category_id' => $category->id,
                'classification_id' => $classification->id,
                'index_number' => $indexNumber,
                'description' => $description,
                'lampiran_surat' => 'TEST-BULK-' . ceil($i / 10),
                'kurun_waktu_start' => $kurunWaktuStart,
                'tingkat_perkembangan' => 'Asli',
                'skkad' => 'BIASA/TERBUKA',
                'jumlah_berkas' => rand(1, 10),
                'ket' => 'Generated for testing',
                'retention_aktif' => $retentionAktif,
                'retention_inaktif' => $retentionInaktif,
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'status' => $status,
                'is_parent' => $isParent,
                'parent_archive_id' => $parentArchiveId,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            if ($isParent) {
                $parentArchive = $archive;
            }

            $createdCount++;
            $this->line("Created archive {$i}: {$description} (Year: {$year}, Status: {$status}, Parent: " . ($isParent ? 'Yes' : 'No') . ")");
        }

        $this->info("Successfully created {$createdCount} test archives!");
        $this->info("Parent archives: " . Archive::where('is_parent', true)->count());
        $this->info("Child archives: " . Archive::whereNotNull('parent_archive_id')->count());
        $this->info("Standalone archives: " . Archive::whereNull('parent_archive_id')->where('is_parent', false)->count());

        return 0;
    }
}
