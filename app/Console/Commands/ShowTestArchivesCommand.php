<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;

class ShowTestArchivesCommand extends Command
{
    protected $signature = 'archives:show-test';
    protected $description = 'Show test archives status for bulk location testing';

    public function handle()
    {
        $this->info('📊 Test Archives Status Report');
        $this->info('=============================');

        // Count total test archives
        $totalTestArchives = Archive::where('index_number', 'like', 'TEST-%')->count();
        $this->info("📋 Total Test Archives: {$totalTestArchives}");

        if ($totalTestArchives === 0) {
            $this->warn('❌ No test archives found. Run "php artisan archives:generate-test" first.');
            return 0;
        }

        // Problem A: Tanaman Rempah
        $masalahAArchives = Archive::where('lampiran_surat', 'SK-TANAMAN-REMPAH-001')->get();
        $this->info("\n🌿 MASALAH A: Tanaman Rempah (SK-TANAMAN-REMPAH-001)");
        $this->info("   📊 Total: {$masalahAArchives->count()} archives");

        $statusCountsA = $masalahAArchives->groupBy('status')->map->count();
        foreach ($statusCountsA as $status => $count) {
            $this->info("   📈 Status {$status}: {$count} archives");
        }

        // Show parent archive
        $parentA = $masalahAArchives->where('is_parent', true)->first();
        if ($parentA) {
            $this->info("   👑 Parent Archive: {$parentA->index_number} ({$parentA->kurun_waktu_start->format('Y')})");
        }

        // Problem B: PPA
        $masalahBArchives = Archive::where('lampiran_surat', 'SK-PPA-001')->get();
        $this->info("\n💰 MASALAH B: PPA (SK-PPA-001)");
        $this->info("   📊 Total: {$masalahBArchives->count()} archives");

        $statusCountsB = $masalahBArchives->groupBy('status')->map->count();
        foreach ($statusCountsB as $status => $count) {
            $this->info("   📈 Status {$status}: {$count} archives");
        }

        // Show parent archive
        $parentB = $masalahBArchives->where('is_parent', true)->first();
        if ($parentB) {
            $this->info("   👑 Parent Archive: {$parentB->index_number} ({$parentB->kurun_waktu_start->format('Y')})");
        }

        // Year distribution
        $this->info("\n📅 Year Distribution:");
        $yearDistribution = Archive::where('index_number', 'like', 'TEST-%')
            ->selectRaw('EXTRACT(YEAR FROM kurun_waktu_start) as year, count(*) as count')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        foreach ($yearDistribution as $yearData) {
            $this->info("   📆 {$yearData->year}: {$yearData->count} archives");
        }

        // Related archives summary
        $this->info("\n🔗 Related Archives Summary:");
        $relatedArchives = Archive::where('index_number', 'like', 'TEST-%')
            ->whereNotNull('parent_archive_id')
            ->count();
        $parentArchives = Archive::where('index_number', 'like', 'TEST-%')
            ->where('is_parent', true)
            ->count();

        $this->info("   👑 Parent Archives: {$parentArchives}");
        $this->info("   🔗 Related Archives: {$relatedArchives}");

        // Ready for testing
        $this->info("\n🎯 Ready for Testing:");
        $this->info("   ✅ Bulk Location Assignment");
        $this->info("   ✅ Update Location (for archives with existing location)");
        $this->info("   ✅ Related Archives Management");
        $this->info("   ✅ Definitive Number Generation");

        return 0;
    }
}
