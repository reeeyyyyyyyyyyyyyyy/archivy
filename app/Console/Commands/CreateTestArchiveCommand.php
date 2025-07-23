<?php

namespace App\Console\Commands;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateTestArchiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:create-test {year : Year for the archive start date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test archive with specified year to test status transitions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = (int) $this->argument('year');
        $currentYear = date('Y');
        
        if ($year > $currentYear) {
            $this->error("Year cannot be in the future. Current year: {$currentYear}");
            return 1;
        }
        
        $category = Category::first();
        $classification = Classification::first();
        
        if (!$category || !$classification) {
            $this->error("No categories or classifications found. Please create some first.");
            return 1;
        }
        
        $startDate = Carbon::createFromDate($year, 1, 1);
        $activeDue = $startDate->copy()->addYears($category->retention_active);
        $inactiveDue = $activeDue->copy()->addYears($category->retention_inactive);
        
        $archive = Archive::create([
            'category_id' => $category->id,
            'classification_id' => $classification->id,
            'index_number' => "TEST-{$year}-" . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'uraian' => "Test archive from {$year} for status transition testing",
            'kurun_waktu_start' => $startDate->toDateString(),
            'tingkat_perkembangan' => 'Asli',
            'jumlah' => 1,
            'ket' => 'Created for testing automatic status transitions',
            'retention_active' => $category->retention_active,
            'retention_inactive' => $category->retention_inactive,
            'transition_active_due' => $activeDue->toDateString(),
            'transition_inactive_due' => $inactiveDue->toDateString(),
            'status' => 'Aktif',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        
        $this->info("Created test archive ID: {$archive->id}");
        $this->line("  Start Date: {$startDate->toDateString()}");
        $this->line("  Active Due: {$activeDue->toDateString()}");
        $this->line("  Inactive Due: {$inactiveDue->toDateString()}");
        $this->line("  Category: {$category->name} (nasib_akhir: {$category->nasib_akhir})");
        
        $today = today();
        if ($activeDue <= $today) {
            $this->warn("This archive should transition to Inaktif (active due date has passed)");
        }
        if ($inactiveDue <= $today) {
            $this->warn("This archive should transition to final status (inactive due date has passed)");
        }
        
        return 0;
    }
} 