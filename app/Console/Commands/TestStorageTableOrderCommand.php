<?php

namespace App\Console\Commands;

use App\Models\Archive;
use App\Models\StorageRack;
use Illuminate\Console\Command;

class TestStorageTableOrderCommand extends Command
{
    protected $signature = 'test:storage-table-order {rack_id? : Rack ID to test}';
    protected $description = 'Test storage table order to ensure proper sequence (box -> year -> file)';

    public function handle()
    {
        $rackId = $this->argument('rack_id') ?? 1;

        $this->info('🧪 Testing Storage Table Order');
        $this->info('==============================');
        $this->info("Testing Rack ID: {$rackId}");

        $rack = StorageRack::find($rackId);
        if (!$rack) {
            $this->error("❌ Rack with ID {$rackId} not found");
            return 1;
        }

        $this->info("📦 Rack: {$rack->name}");

        // Get archives with the same ordering as the view
        $archives = Archive::where('rack_number', $rack->id)
            ->with(['category', 'classification', 'createdByUser'])
            ->orderBy('box_number', 'asc')
            ->orderBy('kurun_waktu_start', 'asc')
            ->orderBy('file_number', 'asc')
            ->get();

        if ($archives->count() === 0) {
            $this->warn("⚠️  No archives found in {$rack->name}");
            return 0;
        }

        $this->info("📁 Found {$archives->count()} archives");

        // Group by box for better visualization
        $groupedByBox = $archives->groupBy('box_number');

        foreach ($groupedByBox as $boxNumber => $boxArchives) {
            $this->info("\n📦 Box {$boxNumber}:");

            // Group by year within this box
            $groupedByYear = $boxArchives->groupBy(function ($archive) {
                return $archive->kurun_waktu_start->format('Y');
            });

            foreach ($groupedByYear as $year => $yearArchives) {
                $this->info("   📅 Tahun {$year}:");

                foreach ($yearArchives as $archive) {
                    $this->line("      - File {$archive->file_number}: {$archive->index_number} ({$archive->kurun_waktu_start->format('Y-m-d')})");
                }
            }
        }

        // Show the actual sequence as it appears in the table
        $this->info("\n📋 Table Sequence (as displayed in view):");
        $this->info("==========================================");

        foreach ($archives as $index => $archive) {
            $rowNumber = $index + 1;
            $this->line("Row {$rowNumber}: Box {$archive->box_number}, Tahun {$archive->kurun_waktu_start->format('Y')}, File {$archive->file_number} - {$archive->index_number}");
        }

        // Verify the order is correct
        $this->info("\n✅ Order Verification:");
        $this->info("=====================");

        $previousBox = null;
        $previousYear = null;
        $previousFile = null;
        $orderIssues = 0;

        foreach ($archives as $archive) {
            $currentBox = $archive->box_number;
            $currentYear = $archive->kurun_waktu_start->format('Y');
            $currentFile = $archive->file_number;

            // Check box order
            if ($previousBox !== null && $currentBox < $previousBox) {
                $this->error("   ❌ Box order issue: Box {$currentBox} comes after Box {$previousBox}");
                $orderIssues++;
            }

            // Check year order within same box
            if ($previousBox === $currentBox && $previousYear !== null && $currentYear < $previousYear) {
                $this->error("   ❌ Year order issue in Box {$currentBox}: {$currentYear} comes after {$previousYear}");
                $orderIssues++;
            }

            // Check file order within same year and box
            if ($previousBox === $currentBox && $previousYear === $currentYear && $previousFile !== null && $currentFile < $previousFile) {
                $this->error("   ❌ File order issue in Box {$currentBox}, Year {$currentYear}: File {$currentFile} comes after File {$previousFile}");
                $orderIssues++;
            }

            $previousBox = $currentBox;
            $previousYear = $currentYear;
            $previousFile = $currentFile;
        }

        if ($orderIssues === 0) {
            $this->info("   ✅ All archives are in correct order!");
        } else {
            $this->error("   ❌ Found {$orderIssues} ordering issues");
        }

        return $orderIssues === 0 ? 0 : 1;
    }
}
