<?php

namespace App\Console\Commands;

use App\Models\Archive;
use App\Models\StorageBox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDefinitiveNumberCommand extends Command
{
    protected $signature = 'test:definitive-numbers {--archive-id= : Test specific archive}';
    protected $description = 'Test definitive number generation and display archive locations';

    public function handle()
    {
        $this->info('🧪 Testing Definitive Number Generation');
        $this->info('=====================================');

        // Test archives with location
        $archivesWithLocation = Archive::whereNotNull('rack_number')
            ->whereNotNull('box_number')
            ->whereNotNull('definitive_number')
            ->orderBy('category_id')
            ->orderBy('classification_id')
            ->orderBy('lampiran_surat')
            ->orderBy('kurun_waktu_start')
            ->orderBy('definitive_number')
            ->get();

        if ($archivesWithLocation->count() === 0) {
            $this->warn('⚠️  No archives with location found. Please set some archives to location first.');
            return 0;
        }

        $this->info("\n📁 Archives with Location and Definitive Numbers:");
        $this->info("   Total archives with location: {$archivesWithLocation->count()}");

        // Group by problem (category + classification + lampiran_surat)
        $problems = $archivesWithLocation->groupBy(function ($archive) {
            return $archive->category->nama_kategori . ' | ' .
                   $archive->classification->nama_klasifikasi . ' | ' .
                   $archive->lampiran_surat;
        });

        foreach ($problems as $problemName => $problemArchives) {
            $this->info("\n🔍 Problem: {$problemName}");
            $this->info("   Total archives in this problem: {$problemArchives->count()}");

            // Group by year
            $yearGroups = $problemArchives->groupBy(function ($archive) {
                return $archive->kurun_waktu_start->format('Y');
            });

            foreach ($yearGroups as $year => $yearArchives) {
                $this->info("\n   📅 Year {$year}:");
                $this->info("      Archives count: {$yearArchives->count()}");

                // Check definitive numbers for this year
                $definitiveNumbers = $yearArchives->pluck('definitive_number')->sort()->values();
                $expectedNumbers = range(1, $yearArchives->count());

                $this->info("      Definitive numbers: " . implode(', ', $definitiveNumbers->toArray()));
                $this->info("      Expected numbers: " . implode(', ', $expectedNumbers));

                // Check if definitive numbers are sequential starting from 1
                if ($definitiveNumbers->toArray() === $expectedNumbers) {
                    $this->info("      ✅ Definitive numbers are correct (sequential from 1)");
                } else {
                    $this->warn("      ⚠️  Definitive numbers are NOT sequential from 1");

                    // Show details
                    foreach ($yearArchives as $archive) {
                        $this->warn("         - {$archive->index_number} ({$archive->kurun_waktu_start->format('Y')}): {$archive->definitive_number}");
                    }
                }

                // Check location consistency
                $locations = $yearArchives->groupBy(function ($archive) {
                    return "Rak {$archive->rack_number}, Box {$archive->box_number}";
                });

                foreach ($locations as $location => $locationArchives) {
                    $this->info("      📦 {$location}: {$locationArchives->count()} archives");
                }
            }
        }

        // Test specific archive if provided
        if ($archiveId = $this->option('archive-id')) {
            $this->info("\n🔍 Testing Specific Archive ID: {$archiveId}");

            $archive = Archive::with(['category', 'classification'])->find($archiveId);
            if (!$archive) {
                $this->error("❌ Archive with ID {$archiveId} not found");
                return 1;
            }

            $this->info("   Archive: {$archive->index_number}");
            $this->info("   Category: {$archive->category->nama_kategori}");
            $this->info("   Classification: {$archive->classification->nama_klasifikasi}");
            $this->info("   Lampiran: {$archive->lampiran_surat}");
            $this->info("   Year: {$archive->kurun_waktu_start->format('Y')}");

            if ($archive->rack_number) {
                $this->info("   Location: Rak {$archive->rack_number}, Box {$archive->box_number}, File {$archive->file_number}");
                $this->info("   Definitive Number: {$archive->definitive_number}");

                // Check if definitive number is correct for this problem and year
                $sameProblemArchives = Archive::where('category_id', $archive->category_id)
                    ->where('classification_id', $archive->classification_id)
                    ->where('lampiran_surat', $archive->lampiran_surat)
                    ->whereYear('kurun_waktu_start', $archive->kurun_waktu_start->format('Y'))
                    ->orderBy('definitive_number')
                    ->get();

                $this->info("   Same problem archives in {$archive->kurun_waktu_start->format('Y')}: {$sameProblemArchives->count()}");

                if ($sameProblemArchives->count() > 0) {
                    $definitiveNumbers = $sameProblemArchives->pluck('definitive_number')->sort()->values();
                    $this->info("   Definitive numbers in this year: " . implode(', ', $definitiveNumbers->toArray()));
                }
            } else {
                $this->info("   Location: Not set");
            }
        }

        // Summary
        $this->info("\n📊 Summary:");
        $this->info("   ✅ Total problems: " . $problems->count());
        $this->info("   ✅ Total archives with location: " . $archivesWithLocation->count());

        $yearsWithIssues = 0;
        foreach ($problems as $problemName => $problemArchives) {
            $yearGroups = $problemArchives->groupBy(function ($archive) {
                return $archive->kurun_waktu_start->format('Y');
            });

            foreach ($yearGroups as $year => $yearArchives) {
                $definitiveNumbers = $yearArchives->pluck('definitive_number')->sort()->values();
                $expectedNumbers = range(1, $yearArchives->count());

                if ($definitiveNumbers->toArray() !== $expectedNumbers) {
                    $yearsWithIssues++;
                }
            }
        }

        if ($yearsWithIssues === 0) {
            $this->info("   ✅ All definitive numbers are correct (sequential per year)");
        } else {
            $this->warn("   ⚠️  Found {$yearsWithIssues} year(s) with incorrect definitive numbers");
        }

        return 0;
    }
}
