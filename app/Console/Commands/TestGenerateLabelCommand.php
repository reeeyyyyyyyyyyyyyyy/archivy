<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StorageRack;
use App\Models\StorageBox;
use App\Models\Archive;
use App\Http\Controllers\GenerateLabelController;
use Carbon\Carbon;

class TestGenerateLabelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:generate-label {rack_id?} {--format=pdf}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the generate label feature';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Generate Label Feature...');

        // Get or create test rack
        $rackId = $this->argument('rack_id');
        if (!$rackId) {
            $rack = StorageRack::where('status', 'active')->first();
            if (!$rack) {
                $this->error('❌ No active racks found. Please create a rack first.');
                return 1;
            }
            $rackId = $rack->id;
        }

        $rack = StorageRack::find($rackId);
        if (!$rack) {
            $this->error("❌ Rack with ID {$rackId} not found.");
            return 1;
        }

        $this->info("📦 Using rack: {$rack->name} (ID: {$rack->id})");

        // Get boxes for this rack
        $boxes = StorageBox::where('rack_id', $rack->id)
            ->orderBy('box_number')
            ->get();

        if ($boxes->isEmpty()) {
            $this->error('❌ No boxes found for this rack.');
            return 1;
        }

        $this->info("📋 Found {$boxes->count()} boxes");

        // Test data processing
        $this->info('🔄 Testing data processing...');
        $controller = new GenerateLabelController();

        try {
            $labels = $controller->generateLabelsData($boxes, $rack);
            $this->info("✅ Generated " . count($labels) . " labels");

            // Display sample labels
            foreach (array_slice($labels, 0, 3) as $index => $label) {
                $this->line("📄 Label " . ($index + 1) . ": Box " . $label['box_number']);
                foreach ($label['ranges'] as $range) {
                    $this->line("   - " . $range);
                }
            }
        } catch (\Exception $e) {
            $this->error("❌ Error in data processing: " . $e->getMessage());
            return 1;
        }

        // Test file generation
        $format = $this->option('format');
        $this->info("📁 Testing {$format} generation...");

        try {
            switch ($format) {
                case 'pdf':
                    $result = $controller->generatePDF($labels, $rack);
                    break;
                case 'word':
                    $result = $controller->generateWord($labels, $rack);
                    break;
                case 'excel':
                    $result = $controller->generateExcel($labels, $rack);
                    break;
                default:
                    $this->error("❌ Invalid format: {$format}");
                    return 1;
            }

            if ($result->getData()->success) {
                $this->info("✅ {$format} file generated successfully!");
                $this->info("📥 Download URL: " . $result->getData()->download_url);
            } else {
                $this->error("❌ Failed to generate {$format} file");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error in file generation: " . $e->getMessage());
            return 1;
        }

        $this->info('🎉 Generate Label feature test completed successfully!');
        return 0;
    }
}
