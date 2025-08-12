<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;

class TestFormattedDefinitiveNumbersCommand extends Command
{
    protected $signature = 'test:formatted-definitive-numbers';
    protected $description = 'Test and verify formatted definitive numbers display';

    public function handle()
    {
        $this->info('🧪 Testing Formatted Definitive Numbers');
        $this->info('=====================================');

        // Get all archives with definitive numbers
        $archives = Archive::whereNotNull('definitive_number')
            ->orderBy('rack_number')
            ->orderBy('row_number')
            ->orderBy('box_number')
            ->orderBy('file_number')
            ->get();

        if ($archives->count() === 0) {
            $this->warn('⚠️  No archives with definitive numbers found');
            return 0;
        }

        $this->info("📁 Found {$archives->count()} archives with definitive numbers");

        // Group by storage location
        $archivesWithLocation = $archives->filter(function ($archive) {
            return $archive->rack_number && $archive->row_number && $archive->box_number && $archive->file_number;
        });

        $archivesWithoutLocation = $archives->filter(function ($archive) {
            return !$archive->rack_number || !$archive->row_number || !$archive->box_number || !$archive->file_number;
        });

        if ($archivesWithLocation->count() > 0) {
            $this->info("\n📍 Archives with Storage Locations ({$archivesWithLocation->count()}):");

            foreach ($archivesWithLocation as $archive) {
                $this->line("   📄 Archive {$archive->id}: {$archive->index_number}");
                $this->line("      Location: Rak {$archive->rack_number}, Row {$archive->row_number}, Box {$archive->box_number}, File {$archive->file_number}");
                $this->line("      Definitive Number: {$archive->definitive_number}");
                $this->line("      Formatted Display: {$archive->formatted_definitive_number}");

                // Show breakdown
                $breakdown = $archive->definitive_number_breakdown;
                if ($breakdown) {
                    $this->line("      Breakdown: " . json_encode($breakdown));
                }
                $this->newLine();
            }
        }

        if ($archivesWithoutLocation->count() > 0) {
            $this->info("\n❓ Archives without Storage Locations ({$archivesWithoutLocation->count()}):");

            foreach ($archivesWithoutLocation->take(10) as $archive) {
                $this->line("   📄 Archive {$archive->id}: {$archive->index_number}");
                $this->line("      Definitive Number: {$archive->definitive_number}");
                $this->line("      Formatted Display: {$archive->formatted_definitive_number}");
                $this->newLine();
            }

            if ($archivesWithoutLocation->count() > 10) {
                $this->line("   ... and " . ($archivesWithoutLocation->count() - 10) . " more");
            }
        }

        // Test format validation
        $this->newLine();
        $this->info('🔍 Testing Format Validation:');

        $allValid = true;
        foreach ($archivesWithLocation as $archive) {
            $expectedFormat = "R{$archive->rack_number}-R{$archive->row_number}-B{$archive->box_number}-F{$archive->file_number}";

            if ($archive->formatted_definitive_number === $expectedFormat) {
                $this->line("   ✅ Archive {$archive->id}: {$expectedFormat}");
            } else {
                $this->error("   ❌ Archive {$archive->id}: Expected {$expectedFormat}, got {$archive->formatted_definitive_number}");
                $allValid = false;
            }
        }

        if ($allValid) {
            $this->newLine();
            $this->info('🎉 All formatted definitive numbers are correct!');
        } else {
            $this->newLine();
            $this->error('❌ Some formatted definitive numbers are incorrect');
        }

        // Show summary statistics
        $this->newLine();
        $this->info('📊 Summary Statistics:');
        $this->info("   - Total archives: {$archives->count()}");
        $this->info("   - With storage locations: {$archivesWithLocation->count()}");
        $this->info("   - Without storage locations: {$archivesWithoutLocation->count()}");
        $this->info("   - Min definitive number: " . $archives->min('definitive_number'));
        $this->info("   - Max definitive number: " . $archives->max('definitive_number'));

        return $allValid ? 0 : 1;
    }
}
