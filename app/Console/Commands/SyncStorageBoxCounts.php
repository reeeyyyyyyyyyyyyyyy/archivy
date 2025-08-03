<?php

namespace App\Console\Commands;

use App\Models\StorageBox;
use App\Models\Archive;
use Illuminate\Console\Command;

class SyncStorageBoxCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:sync-box-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync StorageBox archive counts with actual archive data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting StorageBox archive count sync...');

        $updatedCount = 0;
        $totalBoxes = StorageBox::count();

        StorageBox::chunk(100, function ($boxes) use (&$updatedCount) {
            foreach ($boxes as $box) {
                $actualCount = Archive::where('box_number', $box->box_number)->count();

                if ($actualCount != $box->archive_count) {
                    $this->line("Box {$box->box_number}: {$box->archive_count} -> {$actualCount}");
                    $box->archive_count = $actualCount;
                    $box->save();
                    $updatedCount++;
                }
            }
        });

        $this->info("Sync completed! Updated {$updatedCount} out of {$totalBoxes} boxes.");

        return Command::SUCCESS;
    }
}
