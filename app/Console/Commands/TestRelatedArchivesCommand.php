<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;

class TestRelatedArchivesCommand extends Command
{
    protected $signature = 'archives:test-related';
    protected $description = 'Test related archives functionality';

    public function handle()
    {
        $this->info('Testing Related Archives Functionality...');
        $this->newLine();

        // Test 1: Check parent archives
        $this->info('1. Checking Parent Archives:');
        $parentArchives = Archive::where('is_parent', true)->get();
        foreach ($parentArchives as $parent) {
            $this->line("  - Parent ID {$parent->id}: {$parent->description} (Year: {$parent->kurun_waktu_start->format('Y')})");

            $children = Archive::where('parent_archive_id', $parent->id)->get();
            foreach ($children as $child) {
                $this->line("    └─ Child ID {$child->id}: {$child->description} (Year: {$child->kurun_waktu_start->format('Y')})");
            }
        }

        $this->newLine();

        // Test 2: Check related archives for specific archive
        $this->info('2. Testing getAllRelatedArchives() method:');
        $testArchive = Archive::first();
        if ($testArchive) {
            $relatedArchives = $testArchive->getAllRelatedArchives();
            $this->line("  - Archive ID {$testArchive->id}: {$testArchive->description}");
            $this->line("  - Related archives count: {$relatedArchives->count()}");

            foreach ($relatedArchives as $related) {
                $type = $related->is_parent ? 'Parent' : ($related->parent_archive_id ? 'Child' : 'Standalone');
                $this->line("    └─ {$type} ID {$related->id}: {$related->description} (Year: {$related->kurun_waktu_start->format('Y')})");
            }
        }

        $this->newLine();

        // Test 3: Check statistics
        $this->info('3. Statistics:');
        $totalArchives = Archive::count();
        $parentCount = Archive::where('is_parent', true)->count();
        $childCount = Archive::whereNotNull('parent_archive_id')->count();
        $standaloneCount = Archive::whereNull('parent_archive_id')->where('is_parent', false)->count();

        $this->line("  - Total archives: {$totalArchives}");
        $this->line("  - Parent archives: {$parentCount}");
        $this->line("  - Child archives: {$childCount}");
        $this->line("  - Standalone archives: {$standaloneCount}");

        $this->newLine();
        $this->info('Related archives test completed!');
    }
}
