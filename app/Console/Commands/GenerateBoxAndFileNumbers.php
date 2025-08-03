<?php

namespace App\Console\Commands;

use App\Models\Archive;
use App\Models\StorageRack;
use App\Models\StorageBox;
use Illuminate\Console\Command;

class GenerateBoxAndFileNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:generate-box-file-numbers {--rack-id= : Specific rack ID to process} {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate automatic box and file numbers for archives without storage locations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rackId = $this->option('rack-id');
        $dryRun = $this->option('dry-run');

        $this->info('Starting automatic box and file number generation...');

        // Get archives without storage locations
        $query = Archive::whereNull('rack_number')
            ->orWhereNull('box_number')
            ->orWhereNull('file_number');

        if ($rackId) {
            $query->where('rack_number', $rackId);
        }

        $archives = $query->get();

        if ($archives->isEmpty()) {
            $this->info('No archives found without storage locations.');
            return Command::SUCCESS;
        }

        $this->info("Found {$archives->count()} archives without complete storage locations.");

        // Get available racks
        $racks = StorageRack::where('status', 'active')->get();

        if ($racks->isEmpty()) {
            $this->error('No active storage racks found.');
            return Command::FAILURE;
        }

        $processedCount = 0;
        $errors = [];

        foreach ($archives as $archive) {
            try {
                $result = $this->assignStorageLocation($archive, $racks, $dryRun);

                if ($result['success']) {
                    $processedCount++;
                    $this->line("✓ Archive {$archive->index_number}: {$result['message']}");
                } else {
                    $errors[] = "Archive {$archive->index_number}: {$result['message']}";
                    $this->line("✗ Archive {$archive->index_number}: {$result['message']}");
                }
            } catch (\Exception $e) {
                $errors[] = "Archive {$archive->index_number}: {$e->getMessage()}";
                $this->line("✗ Archive {$archive->index_number}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Processing completed!");
        $this->info("Successfully processed: {$processedCount} archives");

        if (!empty($errors)) {
            $this->warn("Errors encountered: " . count($errors));
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }

        if ($dryRun) {
            $this->warn("DRY RUN: No changes were made to the database.");
        }

        return Command::SUCCESS;
    }

    /**
     * Assign storage location to an archive
     */
    private function assignStorageLocation(Archive $archive, $racks, bool $dryRun): array
    {
        // Try to find the best available location
        foreach ($racks as $rack) {
            $nextBox = $rack->getNextAvailableBox();

            if ($nextBox) {
                $nextFileNumber = $nextBox->getNextFileNumber();

                if (!$dryRun) {
                    $archive->update([
                        'rack_number' => $rack->id,
                        'row_number' => $nextBox->row_number,
                        'box_number' => $nextBox->box_number,
                        'file_number' => $nextFileNumber
                    ]);
                }

                return [
                    'success' => true,
                    'message' => "Assigned to Rack {$rack->name}, Box {$nextBox->box_number}, File {$nextFileNumber}"
                ];
            }
        }

        return [
            'success' => false,
            'message' => "No available storage locations found"
        ];
    }
}
