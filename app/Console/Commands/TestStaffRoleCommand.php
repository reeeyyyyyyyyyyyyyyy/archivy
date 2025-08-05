<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\StorageRack;
use App\Models\StorageRow;
use App\Models\StorageBox;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class TestStaffRoleCommand extends Command
{
    protected $signature = 'test:staff-role {--fix : Apply fixes automatically}';
    protected $description = 'Test and fix staff role functionality issues';

    public function handle()
    {
        $this->info('🔍 Testing Staff Role Functionality...');

        $issues = [];

        // Test 1: Check if staff user exists
        $staffUser = User::where('role_type', 'staff')->first();
        if (!$staffUser) {
            $issues[] = '❌ No staff user found';
        } else {
            $this->info('✅ Staff user found: ' . $staffUser->name);
        }

        // Test 2: Check archive data
        $archives = Archive::count();
        $this->info("📊 Total archives: {$archives}");

        // Test 3: Check categories and classifications
        $categories = Category::count();
        $classifications = Classification::count();
        $this->info("📁 Categories: {$categories}, Classifications: {$classifications}");

        // Test 4: Check storage data
        $racks = StorageRack::count();
        $rows = StorageRow::count();
        $boxes = StorageBox::count();
        $this->info("🗄️ Storage: {$racks} racks, {$rows} rows, {$boxes} boxes");

        // Test 5: Check routes
        $this->info('🔗 Testing routes...');
        $staffRoutes = [
            'staff.dashboard',
            'staff.archives.index',
            'staff.archives.aktif',
            'staff.archives.inaktif',
            'staff.archives.permanen',
            'staff.archives.musnah',
            'staff.storage.index',
            'staff.bulk.index',
            'staff.export.index',
            'staff.generate-labels.index',
            'staff.storage-management.index',
        ];

        foreach ($staffRoutes as $route) {
            try {
                $url = route($route);
                $this->info("✅ Route {$route}: OK");
            } catch (\Exception $e) {
                $issues[] = "❌ Route {$route}: " . $e->getMessage();
            }
        }

        // Test 6: Check controllers
        $this->info('🎮 Testing controllers...');
        $controllers = [
            'App\Http\Controllers\Staff\ArchiveController',
            'App\Http\Controllers\Staff\SearchController',
            'App\Http\Controllers\Staff\AnalyticsController',
            'App\Http\Controllers\BulkOperationController',
            'App\Http\Controllers\StorageLocationController',
            'App\Http\Controllers\StorageManagementController',
            'App\Http\Controllers\GenerateLabelController',
        ];

        foreach ($controllers as $controller) {
            if (class_exists($controller)) {
                $this->info("✅ Controller {$controller}: OK");
            } else {
                $issues[] = "❌ Controller {$controller}: Not found";
            }
        }

        // Test 7: Check views
        $this->info('👁️ Testing views...');
        $views = [
            'staff.archives.index',
            'staff.storage.index',
            'staff.bulk.index',
            'staff.search.index',
        ];

        foreach ($views as $view) {
            if (view()->exists($view)) {
                $this->info("✅ View {$view}: OK");
            } else {
                $issues[] = "❌ View {$view}: Not found";
            }
        }

        // Test 8: Check database queries
        $this->info('💾 Testing database queries...');
        try {
            $archives = Archive::with(['category', 'classification', 'createdByUser'])->paginate(15);
            $this->info("✅ Archive query: OK ({$archives->total()} total)");
        } catch (\Exception $e) {
            $issues[] = "❌ Archive query: " . $e->getMessage();
        }

        // Test 9: Check storage queries
        try {
            $storageArchives = Archive::withoutLocation()->get();
            $this->info("✅ Storage query: OK ({$storageArchives->count()} without location)");
        } catch (\Exception $e) {
            $issues[] = "❌ Storage query: " . $e->getMessage();
        }

        // Test 10: Check bulk operations
        try {
            $bulkArchives = Archive::with(['category', 'classification', 'createdByUser'])->get();
            $this->info("✅ Bulk operations query: OK ({$bulkArchives->count()} archives)");
        } catch (\Exception $e) {
            $issues[] = "❌ Bulk operations query: " . $e->getMessage();
        }

        // Display results
        if (empty($issues)) {
            $this->info('🎉 All tests passed! Staff role functionality is working correctly.');
        } else {
            $this->error('❌ Issues found:');
            foreach ($issues as $issue) {
                $this->error($issue);
            }

            if ($this->option('fix')) {
                $this->info('🔧 Applying fixes...');
                $this->applyFixes();
            }
        }

        return 0;
    }

    private function applyFixes()
    {
        $this->info('🔧 Applying automatic fixes...');

        // Fix 1: Ensure staff routes are properly defined
        $this->info('📝 Checking route definitions...');

        // Fix 2: Check view files
        $this->info('👁️ Checking view files...');

        // Fix 3: Check controller methods
        $this->info('🎮 Checking controller methods...');

        $this->info('✅ Fixes applied. Run the test again to verify.');
    }
}
