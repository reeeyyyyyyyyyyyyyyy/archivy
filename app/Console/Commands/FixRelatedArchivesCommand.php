<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixRelatedArchivesCommand extends Command
{
    protected $signature = 'archives:fix-related';
    protected $description = 'Fix related archives data by ensuring proper parent-child relationships';

    public function handle()
    {
        $this->info('Starting to fix related archives...');

        // Get all archives with same category, classification, and attachment
        $archives = Archive::select('category_id', 'classification_id', 'lampiran_surat')
            ->whereNotNull('category_id')
            ->whereNotNull('classification_id')
            ->whereNotNull('lampiran_surat')
            ->groupBy('category_id', 'classification_id', 'lampiran_surat')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $this->info("Found {$archives->count()} groups of related archives");

        foreach ($archives as $group) {
            $this->info("Processing group: Category {$group->category_id}, Classification {$group->classification_id}, Attachment {$group->lampiran_surat}");

            // Get all archives in this group
            $relatedArchives = Archive::where('category_id', $group->category_id)
                ->where('classification_id', $group->classification_id)
                ->where('lampiran_surat', $group->lampiran_surat)
                ->orderBy('kurun_waktu_start')
                ->get();

            if ($relatedArchives->count() > 1) {
                // Find the oldest archive (parent)
                $parentArchive = $relatedArchives->first();

                // Update parent archive
                $parentArchive->update([
                    'is_parent' => true,
                    'parent_archive_id' => null
                ]);

                $this->info("  - Set archive ID {$parentArchive->id} as parent (year: {$parentArchive->kurun_waktu_start->format('Y')})");

                // Update all other archives to be children
                foreach ($relatedArchives->skip(1) as $childArchive) {
                    $childArchive->update([
                        'is_parent' => false,
                        'parent_archive_id' => $parentArchive->id
                    ]);

                    $this->info("  - Set archive ID {$childArchive->id} as child of {$parentArchive->id} (year: {$childArchive->kurun_waktu_start->format('Y')})");
                }
            }
        }

        $this->info('Related archives fix completed!');

        // Show statistics
        $parentCount = Archive::where('is_parent', true)->count();
        $childCount = Archive::whereNotNull('parent_archive_id')->count();

        $this->info("Statistics:");
        $this->info("  - Parent archives: {$parentCount}");
        $this->info("  - Child archives: {$childCount}");
    }
}
