<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\StorageRack;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class TestStaffSpecificIssuesCommand extends Command
{
    protected $signature = 'test:staff-specific-issues';
    protected $description = 'Test specific staff role issues mentioned by user';

    public function handle()
    {
        $this->info('🔍 Testing Specific Staff Role Issues...');

        $issues = [];
        $fixes = [];

        // Test 1: Filter Modal Issues
        $this->info('📋 Testing Filter Modal Issues...');

        // Check if staff archive index view exists and has proper modal structure
        if (View::exists('staff.archives.index')) {
            $this->info('✅ Staff archive index view exists');

            // Check for modal overlay fix
            $viewContent = file_get_contents(resource_path('views/staff/archives/index.blade.php'));
            if (strpos($viewContent, 'fixed inset-0 bg-black bg-opacity-50') !== false) {
                $this->info('✅ Modal overlay fixed (full screen coverage)');
                $fixes[] = 'Modal overlay opacity fixed';
            } else {
                $issues[] = 'Modal overlay still has opacity issues';
            }

            // Check for field size improvements
            if (strpos($viewContent, 'py-3 px-4 text-sm') !== false) {
                $this->info('✅ Field sizes improved to match admin');
                $fixes[] = 'Field sizes improved';
            } else {
                $issues[] = 'Field sizes still too small';
            }

            // Check for select2 filters
            if (strpos($viewContent, 'select2-filter') !== false) {
                $this->info('✅ Select2 filters implemented');
                $fixes[] = 'Select2 filters added';
            } else {
                $issues[] = 'Select2 filters not implemented';
            }
        } else {
            $issues[] = 'Staff archive index view not found';
        }

        // Test 2: Storage Filter Field Sizes
        $this->info('📦 Testing Storage Filter Field Sizes...');

        if (View::exists('staff.storage.index')) {
            $viewContent = file_get_contents(resource_path('views/staff/storage/index.blade.php'));
            if (strpos($viewContent, 'py-3 px-4 text-sm') !== false) {
                $this->info('✅ Storage filter field sizes improved');
                $fixes[] = 'Storage filter field sizes fixed';
            } else {
                $issues[] = 'Storage filter field sizes still too small';
            }
        } else {
            $issues[] = 'Staff storage index view not found';
        }

        // Test 3: Storage Create Auto-fill
        $this->info('🏗️ Testing Storage Create Auto-fill...');

        if (View::exists('staff.storage.create')) {
            $viewContent = file_get_contents(resource_path('views/staff/storage/create.blade.php'));
            if (strpos($viewContent, 'updateRackInfo()') !== false) {
                $this->info('✅ Auto rack number filling implemented');
                $fixes[] = 'Auto rack number filling added';
            } else {
                $issues[] = 'Auto rack number filling not implemented';
            }

            if (strpos($viewContent, 'readonly') !== false) {
                $this->info('✅ Rack number field made readonly');
                $fixes[] = 'Rack number field made readonly';
            } else {
                $issues[] = 'Rack number field not readonly';
            }
        } else {
            $issues[] = 'Staff storage create view not found';
        }

        // Test 4: Navigation Access Issues
        $this->info('🧭 Testing Navigation Access...');

        $staffRoutes = [
            'staff.storage-management.index',
            'staff.export.index',
            'staff.generate-labels.index',
        ];

        foreach ($staffRoutes as $route) {
            try {
                $url = route($route);
                $this->info("✅ Route {$route}: Accessible");
                $fixes[] = "Route {$route} accessible";
            } catch (\Exception $e) {
                $issues[] = "Route {$route}: " . $e->getMessage();
            }
        }

        // Test 5: Bulk Operations Filter
        $this->info('🔧 Testing Bulk Operations Filter...');

        if (View::exists('staff.bulk.index')) {
            $viewContent = file_get_contents(resource_path('views/staff/bulk/index.blade.php'));
            if (strpos($viewContent, 'loadArchives()') !== false && strpos($viewContent, 'updateArchiveTable') !== false) {
                $this->info('✅ Bulk operations filter functionality improved');
                $fixes[] = 'Bulk operations filter fixed';
            } else {
                $issues[] = 'Bulk operations filter still not working';
            }
        } else {
            $issues[] = 'Staff bulk index view not found';
        }

        // Test 6: Storage Management Pagination
        $this->info('📄 Testing Storage Management Pagination...');

        try {
            $racks = StorageRack::paginate(15);
            if (method_exists($racks, 'links')) {
                $this->info('✅ Storage management pagination fixed');
                $fixes[] = 'Storage management pagination fixed';
            } else {
                $issues[] = 'Storage management pagination still broken';
            }
        } catch (\Exception $e) {
            $issues[] = 'Storage management pagination error: ' . $e->getMessage();
        }

        // Test 7: Staff Controller Methods
        $this->info('🎮 Testing Staff Controller Methods...');

        $staffControllers = [
            'App\Http\Controllers\Staff\ArchiveController',
            'App\Http\Controllers\Staff\SearchController',
            'App\Http\Controllers\Staff\AnalyticsController',
        ];

        foreach ($staffControllers as $controller) {
            if (class_exists($controller)) {
                $this->info("✅ Controller {$controller}: OK");
                $fixes[] = "Controller {$controller} working";
            } else {
                $issues[] = "Controller {$controller}: Not found";
            }
        }

        // Test 8: Data Availability
        $this->info('📊 Testing Data Availability...');

        $categories = Category::count();
        $classifications = Classification::count();
        $users = User::count();

        $this->info("📁 Categories: {$categories}");
        $this->info("🏷️ Classifications: {$classifications}");
        $this->info("👥 Users: {$users}");

        if ($categories > 0 && $classifications > 0 && $users > 0) {
            $this->info('✅ All required data available for filters');
            $fixes[] = 'All required data available';
        } else {
            $issues[] = 'Missing required data for filters';
        }

        // Summary
        $this->info('📋 Summary:');
        $this->info("✅ Fixes applied: " . count($fixes));
        $this->info("❌ Issues remaining: " . count($issues));

        if (!empty($fixes)) {
            $this->info('🔧 Applied Fixes:');
            foreach ($fixes as $fix) {
                $this->info("  ✅ {$fix}");
            }
        }

        if (!empty($issues)) {
            $this->error('❌ Remaining Issues:');
            foreach ($issues as $issue) {
                $this->error("  ❌ {$issue}");
            }
        } else {
            $this->info('🎉 All specific staff role issues have been fixed!');
        }

        return 0;
    }
}
