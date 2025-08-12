<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;

class TestDefinitiveNumbersPerYearCommand extends Command
{
    protected $signature = 'test:definitive-numbers-per-year';
    protected $description = 'Test and verify definitive numbers per year are working correctly';

    public function handle()
    {
        $this->info('🧪 Testing Definitive Numbers Per Year');
        $this->info('=====================================');

        // Get all archives with definitive numbers
        $archives = Archive::whereNotNull('definitive_number')
            ->orderBy('category_id')
            ->orderBy('classification_id')
            ->orderBy('lampiran_surat')
            ->orderBy('kurun_waktu_start')
            ->get();

        if ($archives->count() === 0) {
            $this->warn('⚠️  No archives with definitive numbers found');
            return 0;
        }

        $this->info("📁 Found {$archives->count()} archives with definitive numbers");

        // Group by problem (category + classification + lampiran_surat)
        $problems = $archives->groupBy(function ($archive) {
            return $archive->category->nama_kategori . ' | ' .
                   $archive->classification->nama_klasifikasi . ' | ' .
                   $archive->lampiran_surat;
        });

        $totalProblems = $problems->count();
        $this->info("🔍 Found {$totalProblems} problems");

        $allCorrect = true;

        foreach ($problems as $problemName => $problemArchives) {
            $this->info("\n🔍 Testing problem: {$problemName}");
            $this->info("   Total archives: {$problemArchives->count()}");

            // Group by year
            $yearGroups = $problemArchives->groupBy(function ($archive) {
                return $archive->kurun_waktu_start->format('Y');
            });

            foreach ($yearGroups as $year => $yearArchives) {
                $this->info("   📅 Year {$year}: {$yearArchives->count()} archives");

                // Check if definitive numbers start from 1 and are sequential
                $definitiveNumbers = $yearArchives->pluck('definitive_number')->sort()->values();
                $expectedNumbers = range(1, $yearArchives->count());

                if ($definitiveNumbers->toArray() === $expectedNumbers) {
                    $this->info("      ✅ Correct: Definitive numbers are sequential (1 to {$yearArchives->count()})");
                    $this->info("         Numbers: " . $definitiveNumbers->implode(', '));
                } else {
                    $this->error("      ❌ Incorrect: Definitive numbers are not sequential");
                    $this->error("         Expected: " . implode(', ', $expectedNumbers));
                    $this->error("         Actual: " . $definitiveNumbers->implode(', '));
                    $allCorrect = false;
                }

                // Show individual archive details
                foreach ($yearArchives->sortBy('definitive_number') as $archive) {
                    $this->line("         - Archive {$archive->id}: {$archive->definitive_number} ({$archive->index_number})");
                }
            }
        }

        $this->newLine();
        if ($allCorrect) {
            $this->info('🎉 All definitive numbers per year are correct!');
            $this->info('✅ Logic is working properly');
        } else {
            $this->error('❌ Some definitive numbers are incorrect');
            $this->error('🔧 Please check the logic');
        }

        // Show summary statistics
        $this->newLine();
        $this->info('📊 Summary Statistics:');
        $this->info("   - Total archives: {$archives->count()}");
        $this->info("   - Total problems: {$totalProblems}");
        $this->info("   - Total years: " . $archives->groupBy(function ($archive) {
            return $archive->kurun_waktu_start->format('Y');
        })->count());
        $this->info("   - Min definitive number: " . $archives->min('definitive_number'));
        $this->info("   - Max definitive number: " . $archives->max('definitive_number'));

        return $allCorrect ? 0 : 1;
    }
}
