<?php

namespace App\Console\Commands;

use App\Jobs\UpdateArchiveStatusJob;
use Illuminate\Console\Command;

class UpdateArchiveStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch job to update archive statuses based on their retention dates.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UpdateArchiveStatusJob::dispatch();
        $this->info('Job to update archive statuses has been dispatched.');
    }
}
