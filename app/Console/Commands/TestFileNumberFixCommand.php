<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archive;
use App\Models\StorageRack;
use App\Models\StorageBox;
use App\Models\StorageRow;
use App\Models\Category;
use App\Models\Classification;
use Carbon\Carbon;

class TestFileNumberFixCommand extends Command
{
    protected $signature = 'test:file-number-fix {--create-test-data : Create test data for testing}';
    protected $description = 'Test the file number fix for year-based numbering';

    public function handle()
    {
        $this->info('🧪 Testing File Number Fix for Year-Based Numbering...');

        if ($this->option('create-test-data')) {
            $this->createTestData();
        }

        $this->testFileNumberGeneration();
        $this->testYearBasedNumbering();
        $this->testGenerateLabels();

        $this->info('✅ File number fix testing completed!');
    }

    private function createTestData()
    {
        $this->info('📝 Creating test data...');

        // Create test category and classification
        $category = Category::firstOrCreate(['nama_kategori' => 'TEST_CATEGORY']);
        $classification = Classification::firstOrCreate([
            'category_id' => $category->id,
            'code' => 'TEST_CODE',
            'nama_klasifikasi' => 'TEST_CLASSIFICATION'
        ]);

        // Create test rack, row, and box
        $rack = StorageRack::firstOrCreate([
            'name' => 'Test Rack',
            'status' => 'active'
        ]);

        $row = StorageRow::firstOrCreate([
            'rack_id' => $rack->id,
            'row_number' => 1
        ]);

        $box = StorageBox::firstOrCreate([
            'rack_id' => $rack->id,
            'row_id' => $row->id,
            'box_number' => 1,
            'capacity' => 100
        ]);

        // Create test archives for different years
        $years = [2020, 2021, 2022, 2023];
        $archivesPerYear = [5, 15, 15, 15]; // As per user's test case

        foreach ($years as $index => $year) {
            $count = $archivesPerYear[$index];
            $this->info("   Creating {$count} archives for year {$year}...");

            for ($i = 1; $i <= $count; $i++) {
                $archive = Archive::create([
                    'index_number' => "TEST-{$year}-{$i}",
                    'description' => "Test Archive {$year} - {$i}",
                    'category_id' => $category->id,
                    'classification_id' => $classification->id,
                    'kurun_waktu_start' => Carbon::create($year, 1, 1),
                    'kurun_waktu_end' => Carbon::create($year, 12, 31),
                    'status' => 'Aktif',
                    'rack_number' => $rack->id,
                    'row_number' => 1,
                    'box_number' => 1,
                    'file_number' => $i, // This will be corrected by our fix
                ]);

                $this->line("     - Created archive {$archive->index_number} with file_number {$archive->file_number}");
            }
        }

        $this->info('✅ Test data created successfully!');
    }

    private function testFileNumberGeneration()
    {
        $this->info('🔢 Testing file number generation...');

        // Test getNextFileNumberForClassification
        $rackId = 1;
        $boxNumber = 1;
        $categoryId = 1;
        $classificationId = 1;

        $this->info("   Testing getNextFileNumberForClassification for:");
        $this->info("   - Rack: {$rackId}");
        $this->info("   - Box: {$boxNumber}");
        $this->info("   - Classification: {$classificationId}");

        foreach ([2020, 2021, 2022, 2023] as $year) {
            $nextFileNumber = Archive::getNextFileNumberForClassification(
                $rackId,
                $boxNumber,
                $classificationId,
                $year
            );

            $this->info("   - Year {$year}: Next file number = {$nextFileNumber}");
        }
    }

    private function testYearBasedNumbering()
    {
        $this->info('📅 Testing year-based numbering logic...');

        // Get archives from test box
        $archives = Archive::where('rack_number', 1)
            ->where('box_number', 1)
            ->orderBy('kurun_waktu_start')
            ->orderBy('file_number')
            ->get();

        $this->info("   Found {$archives->count()} archives in test box");

        // Group by year
        $archivesByYear = $archives->groupBy(function($archive) {
            return $archive->kurun_waktu_start->year;
        });

        foreach ($archivesByYear as $year => $yearArchives) {
            $this->info("   Year {$year}:");
            $this->info("     - Count: {$yearArchives->count()}");
            $this->info("     - File numbers: " . $yearArchives->pluck('file_number')->implode(', '));

            // Check if file numbers start from 1 for each year
            $minFileNumber = $yearArchives->min('file_number');
            $maxFileNumber = $yearArchives->max('file_number');

            if ($minFileNumber === 1) {
                $this->info("     ✅ File numbers start from 1 (correct)");
            } else {
                $this->warn("     ❌ File numbers should start from 1, but start from {$minFileNumber}");
            }
        }
    }

    private function testGenerateLabels()
    {
        $this->info('🏷️ Testing generate labels with year-based numbering...');

        // Get test box
        $box = StorageBox::where('box_number', 1)->first();
        if (!$box) {
            $this->error('Test box not found!');
            return;
        }

        $rack = StorageRack::find(1);
        if (!$rack) {
            $this->error('Test rack not found!');
            return;
        }

        // Test generateLabelsData method
        $controller = new \App\Http\Controllers\GenerateLabelController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('generateLabelsData');
        $method->setAccessible(true);

        $labels = $method->invoke($controller, collect([$box]), $rack);

        $this->info("   Generated labels:");
        foreach ($labels as $label) {
            $this->info("   - Box {$label['box_number']}:");
            foreach ($label['ranges'] as $range) {
                $this->info("     * {$range}");
            }
        }
    }
}
