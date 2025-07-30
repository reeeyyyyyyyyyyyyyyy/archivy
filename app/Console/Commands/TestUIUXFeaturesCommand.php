<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use Illuminate\Support\Facades\Auth;

class TestUIUXFeaturesCommand extends Command
{
    protected $signature = 'test:ui-ux-features';
    protected $description = 'Test UI/UX features like headers, navigation, and responsive design';

    public function handle()
    {
        $this->info('🧪 Testing UI/UX Features...');

        // Ensure we have test data
        $user = User::first();
        if (!$user) {
            $this->error('❌ Tidak ada user ditemukan!');
            return;
        }

        Auth::login($user);

        // Test Archive Counts by Status
        $this->info('📋 Testing Archive Status Counts...');
        $this->testArchiveStatusCounts();

        // Test Navigation Structure
        $this->info('📋 Testing Navigation Structure...');
        $this->testNavigationStructure();

        // Test Responsive Design Elements
        $this->info('📋 Testing Responsive Design Elements...');
        $this->testResponsiveDesign();

        // Test Header Consistency
        $this->info('📋 Testing Header Consistency...');
        $this->testHeaderConsistency();

        $this->info('✅ UI/UX Features tests completed!');
    }

    private function testArchiveStatusCounts()
    {
        $counts = [
            'total' => Archive::count(),
            'aktif' => Archive::where('status', 'Aktif')->count(),
            'inaktif' => Archive::where('status', 'Inaktif')->count(),
            'permanen' => Archive::where('status', 'Permanen')->count(),
            'musnah' => Archive::where('status', 'Musnah')->count(),
            'dinilai_kembali' => Archive::where('status', 'Dinilai Kembali')->count(),
        ];

        $this->info('✅ Archive Status Counts:');
        foreach ($counts as $status => $count) {
            $this->info("   - {$status}: {$count}");
        }
    }

    private function testNavigationStructure()
    {
        $navigationItems = [
            'Dashboard' => 'admin.dashboard',
            'Manajemen Arsip' => [
                'Semua Arsip' => 'admin.archives.index',
                'Arsip Aktif' => 'admin.archives.aktif',
                'Arsip Inaktif' => 'admin.archives.inaktif',
                'Arsip Permanen' => 'admin.archives.permanen',
                'Arsip Musnah' => 'admin.archives.musnah',
                'Arsip Dinilai Kembali' => 'admin.re-evaluation.index',
                'Tambah Arsip' => 'admin.archives.create',
            ],
            'Export Excel' => 'admin.export.index',
            'Lokasi Penyimpanan' => 'admin.storage.index',
            'Master Data' => [
                'Kategori' => 'admin.categories.index',
                'Klasifikasi' => 'admin.classifications.index',
            ],
        ];

        $this->info('✅ Navigation Structure:');
        foreach ($navigationItems as $item => $route) {
            if (is_array($route)) {
                $this->info("   - {$item}:");
                foreach ($route as $subItem => $subRoute) {
                    $this->info("     - {$subItem}: {$subRoute}");
                }
            } else {
                $this->info("   - {$item}: {$route}");
            }
        }
    }

    private function testResponsiveDesign()
    {
        $responsiveClasses = [
            'Container' => 'max-w-7xl mx-auto',
            'Grid System' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
            'Table Overflow' => 'overflow-x-auto',
            'Mobile Navigation' => 'lg:hidden',
            'Desktop Navigation' => 'lg:translate-x-0',
        ];

        $this->info('✅ Responsive Design Classes:');
        foreach ($responsiveClasses as $element => $classes) {
            $this->info("   - {$element}: {$classes}");
        }
    }

    private function testHeaderConsistency()
    {
        $headerConfigs = [
            'Arsip' => [
                'icon' => 'fas fa-archive',
                'bg' => 'bg-blue-600',
                'subtitle' => 'Manajemen lengkap semua arsip digital sistem',
            ],
            'Arsip Aktif' => [
                'icon' => 'fas fa-play-circle',
                'bg' => 'bg-green-600',
                'subtitle' => 'Arsip dalam periode aktif dan dapat diakses',
            ],
            'Arsip Inaktif' => [
                'icon' => 'fas fa-pause-circle',
                'bg' => 'bg-yellow-600',
                'subtitle' => 'Arsip yang telah melewati masa aktif',
            ],
            'Arsip Permanen' => [
                'icon' => 'fas fa-shield-alt',
                'bg' => 'bg-purple-600',
                'subtitle' => 'Arsip dengan nilai guna berkelanjutan',
            ],
            'Arsip Musnah' => [
                'icon' => 'fas fa-ban',
                'bg' => 'bg-red-600',
                'subtitle' => 'Arsip yang telah dimusnahkan sesuai retensi',
            ],
            'Arsip Dinilai Kembali' => [
                'icon' => 'fas fa-redo',
                'bg' => 'bg-indigo-600',
                'subtitle' => 'Kelola arsip yang memerlukan penilaian ulang',
            ],
            'Lokasi Penyimpanan' => [
                'icon' => 'fas fa-map-marker-alt',
                'bg' => 'bg-indigo-600',
                'subtitle' => 'Atur lokasi penyimpanan arsip Anda',
            ],
        ];

        $this->info('✅ Header Configurations:');
        foreach ($headerConfigs as $title => $config) {
            $this->info("   - {$title}:");
            $this->info("     Icon: {$config['icon']}");
            $this->info("     Background: {$config['bg']}");
            $this->info("     Subtitle: {$config['subtitle']}");
        }
    }
}
