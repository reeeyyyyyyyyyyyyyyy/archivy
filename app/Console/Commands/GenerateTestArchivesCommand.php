<?php

namespace App\Console\Commands;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateTestArchivesCommand extends Command
{
    protected $signature = 'archives:generate-test {--count=60 : Number of archives per problem}';
    protected $description = 'Generate test archives for bulk location testing';

    public function handle()
    {
        $this->info('🚀 Starting test archives generation...');

        try {
            DB::beginTransaction();

            // Get or create categories
            $perekonomianCategory = Category::firstOrCreate(
                ['nama_kategori' => 'PEREKONOMIAN'],
                [
                    'nama_kategori' => 'PEREKONOMIAN',
                    'retention_aktif' => 2,
                    'retention_inaktif' => 3,
                    'nasib_akhir' => 'Musnah'
                ]
            );

            $keuanganCategory = Category::firstOrCreate(
                ['nama_kategori' => 'KEUANGAN'],
                [
                    'nama_kategori' => 'KEUANGAN',
                    'retention_aktif' => 2,
                    'retention_inaktif' => 3,
                    'nasib_akhir' => 'Musnah'
                ]
            );

            $this->info("✅ Categories ready: {$perekonomianCategory->nama_kategori}, {$keuanganCategory->nama_kategori}");

            // Get or create classifications
            $tanamanRempahClassification = Classification::firstOrCreate(
                ['code' => '500.8.3.3'],
                [
                    'code' => '500.8.3.3',
                    'nama_klasifikasi' => 'Budi Daya Tanaman Rempah dan Penyegar',
                    'category_id' => $perekonomianCategory->id,
                    'retention_aktif' => 2,
                    'retention_inaktif' => 3,
                    'nasib_akhir' => 'Musnah'
                ]
            );

            $ppaClassification = Classification::firstOrCreate(
                ['code' => '900.1.1.1'],
                [
                    'code' => '900.1.1.1',
                    'nama_klasifikasi' => 'Penyusunan Prioritas Plafon Anggaran (PPA)',
                    'category_id' => $keuanganCategory->id,
                    'retention_aktif' => 2,
                    'retention_inaktif' => 3,
                    'nasib_akhir' => 'Musnah'
                ]
            );

            $this->info("✅ Classifications ready: {$tanamanRempahClassification->code}, {$ppaClassification->code}");

            // Get admin user
            $adminUser = User::where('role_type', 'admin')->first();
            if (!$adminUser) {
                $this->error('❌ No admin user found! Please create an admin user first.');
                return 1;
            }

            $countPerProblem = (int) $this->option('count');
            $this->info("📊 Generating {$countPerProblem} archives per problem...");

            // Generate archives for Problem A: Tanaman Rempah
            $this->generateProblemArchives(
                'MASALAH_A_TANAMAN_REMPAH',
                $perekonomianCategory,
                $tanamanRempahClassification,
                'SK-TANAMAN-REMPAH-001',
                $countPerProblem,
                $adminUser
            );

            // Generate archives for Problem B: PPA
            $this->generateProblemArchives(
                'MASALAH_B_PPA',
                $keuanganCategory,
                $ppaClassification,
                'SK-PPA-001',
                $countPerProblem,
                $adminUser
            );

            DB::commit();

            $totalArchives = $countPerProblem * 2;
            $this->info("✅ Successfully generated {$totalArchives} test archives!");
            $this->info("📋 Problem A: {$countPerProblem} archives (Tanaman Rempah)");
            $this->info("📋 Problem B: {$countPerProblem} archives (PPA)");
            $this->info("🎯 Ready for bulk location testing!");

            return 0;

        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Error generating test archives: " . $e->getMessage());
            Log::error('GenerateTestArchivesCommand error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    private function generateProblemArchives(
        string $problemName,
        Category $category,
        Classification $classification,
        string $lampiranSurat,
        int $count,
        User $adminUser
    ) {
        $this->info("🔄 Generating archives for {$problemName}...");

        // Create parent archive (oldest year)
        $parentArchive = Archive::create([
            'category_id' => $category->id,
            'classification_id' => $classification->id,
            'lampiran_surat' => $lampiranSurat,
            'parent_archive_id' => null,
            'is_parent' => true,
            'index_number' => "TEST-{$problemName}-001",
            'description' => "Arsip Test {$problemName} - Tahun 2019",
            'kurun_waktu_start' => '2019-01-15',
            'tingkat_perkembangan' => 'Asli',
            'jumlah_berkas' => 1,
            'skkad' => 'BIASA/TERBUKA', // Use valid enum value
            'ket' => 'Arsip test untuk bulk location',
            'retention_aktif' => $classification->retention_aktif,
            'retention_inaktif' => $classification->retention_inaktif,
            'transition_active_due' => Carbon::parse('2019-01-15')->addYears($classification->retention_aktif),
            'transition_inactive_due' => Carbon::parse('2019-01-15')->addYears($classification->retention_aktif + $classification->retention_inaktif),
            'status' => 'Aktif', // Will be auto-calculated
            'manual_nasib_akhir' => $classification->nasib_akhir,
            'created_by' => $adminUser->id,
            'updated_by' => $adminUser->id,
        ]);

        $this->info("✅ Created parent archive: {$parentArchive->index_number}");

        // Generate related archives for years 2019-2025
        $years = [2019, 2020, 2021, 2022, 2023, 2024, 2025];
        $archivesPerYear = ceil($count / count($years));
        $currentCount = 1; // Start from 1 since parent is already created

        foreach ($years as $year) {
            $yearCount = min($archivesPerYear, $count - $currentCount + 1);

            for ($i = 1; $i <= $yearCount; $i++) {
                $archiveNumber = $currentCount + $i;
                $isParent = ($year === 2019 && $i === 1); // First archive of 2019 is parent

                // Random date within the year
                $randomDay = rand(1, 28);
                $randomMonth = rand(1, 12);
                $archiveDate = Carbon::create($year, $randomMonth, $randomDay);

                // Calculate transition dates
                $transitionActiveDue = $archiveDate->copy()->addYears($classification->retention_aktif);
                $transitionInactiveDue = $transitionActiveDue->copy()->addYears($classification->retention_inaktif);

                // Calculate status based on current date
                $now = Carbon::now();
                $status = 'Aktif';

                if ($now->gt($transitionInactiveDue)) {
                    $status = 'Musnah'; // Based on nasib_akhir
                } elseif ($now->gt($transitionActiveDue)) {
                    $status = 'Inaktif';
                }

                $archive = Archive::create([
                    'category_id' => $category->id,
                    'classification_id' => $classification->id,
                    'lampiran_surat' => $lampiranSurat,
                    'parent_archive_id' => $isParent ? null : $parentArchive->id,
                    'is_parent' => $isParent,
                    'index_number' => "TEST-{$problemName}-" . str_pad($archiveNumber, 3, '0', STR_PAD_LEFT),
                    'description' => "Arsip Test {$problemName} - Tahun {$year} - Nomor {$archiveNumber}",
                    'kurun_waktu_start' => $archiveDate->format('Y-m-d'),
                    'tingkat_perkembangan' => 'Asli',
                    'jumlah_berkas' => rand(1, 5),
                    'skkad' => 'BIASA/TERBUKA', // Use valid enum value
                    'ket' => "Arsip test untuk bulk location - {$year}",
                    'retention_aktif' => $classification->retention_aktif,
                    'retention_inaktif' => $classification->retention_inaktif,
                    'transition_active_due' => $transitionActiveDue,
                    'transition_inactive_due' => $transitionInactiveDue,
                    'status' => $status,
                    'manual_nasib_akhir' => $classification->nasib_akhir,
                    'created_by' => $adminUser->id,
                    'updated_by' => $adminUser->id,
                ]);

                if ($archiveNumber % 10 === 0) {
                    $this->info("   📄 Created archive {$archiveNumber}/{$count}: {$archive->index_number}");
                }
            }

            $currentCount += $yearCount;
        }

        $actualCount = Archive::where('lampiran_surat', $lampiranSurat)->count();
        $this->info("✅ Generated {$actualCount} archives for {$problemName}");

        // Show status distribution
        $statusCounts = Archive::where('lampiran_surat', $lampiranSurat)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        foreach ($statusCounts as $status => $count) {
            $this->info("   📊 Status {$status}: {$count} archives");
        }
    }
}
