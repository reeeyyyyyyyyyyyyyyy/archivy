<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;

class TestFileNumberLogicCommand extends Command
{
    protected $signature = 'test:file-number-logic';
    protected $description = 'Test file number logic to ensure it restarts from 1 for each year';

    public function handle()
    {
        $this->info('🧪 Testing File Number Logic');
        $this->info('============================');

        // Get all archives with storage locations
        $archivesWithLocation = Archive::whereNotNull('rack_number')
            ->whereNotNull('row_number')
            ->whereNotNull('box_number')
            ->whereNotNull('file_number')
            ->orderBy('rack_number')
            ->orderBy('row_number')
            ->orderBy('box_number')
            ->orderBy('kurun_waktu_start')
            ->get();

        if ($archivesWithLocation->count() === 0) {
            $this->warn('⚠️  No archives with storage locations found');
            return 0;
        }

        $this->info("📁 Found {$archivesWithLocation->count()} archives with storage locations");

        // Group by rack, row, box
        $groupedArchives = $archivesWithLocation->groupBy(function ($archive) {
            return "Rak {$archive->rack_number}, Baris {$archive->row_number}, Box {$archive->box_number}";
        });

        $issuesFound = 0;

        foreach ($groupedArchives as $location => $archives) {
            $this->info("\n📍 {$location}:");

            // Group by year within this box
            $yearGroups = $archives->groupBy(function ($archive) {
                return $archive->kurun_waktu_start->format('Y');
            });

            foreach ($yearGroups as $year => $yearArchives) {
                $this->info("   📅 Tahun {$year}:");

                // Check if file numbers restart from 1 for each year
                $fileNumbers = $yearArchives->pluck('file_number')->sort()->values();
                $expectedFileNumbers = range(1, $yearArchives->count());

                if ($fileNumbers->toArray() !== $expectedFileNumbers) {
                    $this->error("      ❌ File numbers should restart from 1 for each year");
                    $this->error("         Expected: " . implode(', ', $expectedFileNumbers));
                    $this->error("         Actual: " . implode(', ', $fileNumbers->toArray()));
                    $issuesFound++;
                } else {
                    $this->info("      ✅ File numbers restart correctly: " . implode(', ', $fileNumbers->toArray()));
                }

                // Show individual archives
                foreach ($yearArchives as $archive) {
                    $this->line("         - Archive {$archive->id}: File {$archive->file_number} ({$archive->index_number})");
                }
            }
        }

        // Summary
        $this->newLine();
        if ($issuesFound === 0) {
            $this->info('🎉 All file numbers are correctly restarting from 1 for each year!');
        } else {
            $this->error("❌ Found {$issuesFound} issues with file number logic");
        }

        return $issuesFound === 0 ? 0 : 1;
    }
}
