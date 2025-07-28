<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Archive;
use App\Models\User;
use App\Jobs\UpdateArchiveStatusJob;
use Illuminate\Support\Facades\Log;

class GenerateFakeDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:generate
                            {--categories=10 : Number of categories to create}
                            {--classifications=30 : Number of classifications to create}
                            {--archives=100 : Number of archives to create}
                            {--admin-percent=40 : Percentage of archives created by admin}
                            {--staff-percent=40 : Percentage of archives created by staff}
                            {--intern-percent=20 : Percentage of archives created by intern}
                            {--force : Force recreation of data}
                            {--status-update : Update archive statuses after creation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake data for testing the archive system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting fake data generation...');

        // Check if data exists and force option
        if (!$this->option('force') && $this->dataExists()) {
            $this->warn('⚠️  Data already exists! Use --force to recreate.');
            if (!$this->confirm('Do you want to continue with existing data?')) {
                $this->info('❌ Operation cancelled.');
                return;
            }
        }

        // Clear existing data if force option
        if ($this->option('force')) {
            $this->clearExistingData();
        }

        try {
            // Step 1: Create Categories
            $this->createCategories();

            // Step 2: Create Classifications
            $this->createClassifications();

            // Step 3: Ensure Users Exist
            $this->ensureUsersExist();

            // Step 4: Create Archives
            $this->createArchives();

            // Step 5: Update Statuses
            if ($this->option('status-update')) {
                $this->updateArchiveStatuses();
            }

            $this->info('✅ Fake data generation completed successfully!');
            $this->displaySummary();

        } catch (\Exception $e) {
            $this->error('❌ Error generating fake data: ' . $e->getMessage());
            Log::error('GenerateFakeDataCommand error: ' . $e->getMessage());
        }
    }

    /**
     * Check if data already exists
     */
    private function dataExists(): bool
    {
        return Category::count() > 0 || Classification::count() > 0 || Archive::count() > 0;
    }

    /**
     * Clear existing data
     */
    private function clearExistingData(): void
    {
        $this->info('🗑️  Clearing existing data...');

        Archive::truncate();
        Classification::truncate();
        Category::truncate();

        $this->info('✅ Existing data cleared.');
    }

    /**
     * Create categories
     */
    private function createCategories(): void
    {
        $count = (int) $this->option('categories');
        $this->info("📁 Creating {$count} categories...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            Category::factory()->create();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Created {$count} categories");
    }

    /**
     * Create classifications
     */
    private function createClassifications(): void
    {
        $count = (int) $this->option('classifications');
        $this->info("📋 Creating {$count} classifications...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            Classification::factory()->create();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Created {$count} classifications");
    }

    /**
     * Ensure users exist
     */
    private function ensureUsersExist(): void
    {
        $this->info('👥 Ensuring users exist...');

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@archivy.test'],
            [
                'name' => 'Administrator',
                'email' => 'admin@archivy.test',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff@archivy.test'],
            [
                'name' => 'Staff TU',
                'email' => 'staff@archivy.test',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );
        if (!$staff->hasRole('staff')) {
            $staff->assignRole('staff');
        }

        // Create intern user
        $intern = User::firstOrCreate(
            ['email' => 'intern@archivy.test'],
            [
                'name' => 'Mahasiswa Magang',
                'email' => 'intern@archivy.test',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );
        if (!$intern->hasRole('intern')) {
            $intern->assignRole('intern');
        }

        $this->info("✅ Users ready - Admin: {$admin->id}, Staff: {$staff->id}, Intern: {$intern->id}");
    }

    /**
     * Create archives with user distribution
     */
    private function createArchives(): void
    {
        $totalArchives = (int) $this->option('archives');
        $adminPercent = (int) $this->option('admin-percent');
        $staffPercent = (int) $this->option('staff-percent');
        $internPercent = (int) $this->option('intern-percent');

        $adminCount = (int) ($totalArchives * $adminPercent / 100);
        $staffCount = (int) ($totalArchives * $staffPercent / 100);
        $internCount = $totalArchives - $adminCount - $staffCount; // Remaining for intern

        $this->info("📦 Creating {$totalArchives} archives...");
        $this->info("   - Admin: {$adminCount} ({$adminPercent}%)");
        $this->info("   - Staff: {$staffCount} ({$staffPercent}%)");
        $this->info("   - Intern: {$internCount} (" . ($internPercent + ($totalArchives - $adminCount - $staffCount - $internCount)) . "%)");

        $admin = User::role('admin')->first();
        $staff = User::role('staff')->first();
        $intern = User::role('intern')->first();

        $bar = $this->output->createProgressBar($totalArchives);
        $bar->start();

        // Create admin archives
        for ($i = 0; $i < $adminCount; $i++) {
            Archive::factory()->create([
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
            $bar->advance();
        }

        // Create staff archives
        for ($i = 0; $i < $staffCount; $i++) {
            Archive::factory()->create([
                'created_by' => $staff->id,
                'updated_by' => $staff->id,
            ]);
            $bar->advance();
        }

        // Create intern archives
        for ($i = 0; $i < $internCount; $i++) {
            Archive::factory()->create([
                'created_by' => $intern->id,
                'updated_by' => $intern->id,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Created {$totalArchives} archives");
    }

    /**
     * Update archive statuses
     */
    private function updateArchiveStatuses(): void
    {
        $this->info('🔄 Updating archive statuses...');

        UpdateArchiveStatusJob::dispatchSync();

        $this->info('✅ Archive statuses updated');
    }

    /**
     * Display summary
     */
    private function displaySummary(): void
    {
        $this->newLine();
        $this->info('📊 Data Summary:');
        $this->table(
            ['Entity', 'Count'],
            [
                ['Categories', Category::count()],
                ['Classifications', Classification::count()],
                ['Archives', Archive::count()],
                ['Users', User::count()],
            ]
        );

        $this->newLine();
        $this->info('📈 Archive Status Distribution:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Aktif', Archive::where('status', 'Aktif')->count()],
                ['Inaktif', Archive::where('status', 'Inaktif')->count()],
                ['Permanen', Archive::where('status', 'Permanen')->count()],
                ['Musnah', Archive::where('status', 'Musnah')->count()],
            ]
        );

        $this->newLine();
        $this->info('👥 Archive Creation by User:');
        $this->table(
            ['User', 'Role', 'Count'],
            [
                ['Administrator', 'Admin', Archive::where('created_by', User::role('admin')->first()->id)->count()],
                ['Staff TU', 'Staff', Archive::where('created_by', User::role('staff')->first()->id)->count()],
                ['Mahasiswa Magang', 'Intern', Archive::where('created_by', User::role('intern')->first()->id)->count()],
            ]
        );

        $this->newLine();
        $this->info('🔑 Login Credentials:');
        $this->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@archivy.test', 'password'],
                ['Staff', 'staff@archivy.test', 'password'],
                ['Intern', 'intern@archivy.test', 'password'],
            ]
        );
    }
}
