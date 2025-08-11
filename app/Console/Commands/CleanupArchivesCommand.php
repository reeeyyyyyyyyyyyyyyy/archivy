<?php

namespace App\Console\Commands;

use App\Models\Archive;
use Illuminate\Console\Command;

class CleanupArchivesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archives:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup all archives for testing related archives feature';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting archive cleanup...');

        $count = Archive::count();

        if ($count > 0) {
            if ($this->confirm("Found {$count} archives. Do you want to delete all archives?")) {
                Archive::truncate();
                $this->info('✅ All archives have been deleted successfully!');
            } else {
                $this->info('❌ Cleanup cancelled.');
                return;
            }
        } else {
            $this->info('ℹ️ No archives found to clean up.');
        }

        $this->info('🎯 Ready for testing related archives feature!');
    }
}
