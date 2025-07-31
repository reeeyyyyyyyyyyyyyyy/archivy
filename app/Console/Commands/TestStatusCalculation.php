<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Carbon\Carbon;

class TestStatusCalculation extends Command
{
    protected $signature = 'test:status-calculation';
    protected $description = 'Test status calculation for LAINNYA category archives';

    public function handle()
    {
        $this->info('🔧 TESTING STATUS CALCULATION FOR LAINNYA CATEGORY...');

        // 1. Test LAINNYA category creation
        $this->info('📋 1. Testing LAINNYA category creation...');

        $user = User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->first();

        $lainnyaCategory = Category::where('nama_kategori', 'LAINNYA')->first();
        $lainnyaClassification = Classification::where('code', 'LAINNYA')->first();

        if (!$user || !$lainnyaCategory || !$lainnyaClassification) {
            $this->error('Missing required data (user, LAINNYA category, or LAINNYA classification)!');
            return;
        }

        $this->info("   User: {$user->name}");
        $this->info("   Category: {$lainnyaCategory->nama_kategori}");
        $this->info("   Classification: {$lainnyaClassification->nama_klasifikasi}");

        // 2. Create test archives with different scenarios
        $this->info('📋 2. Creating test archives with different scenarios...');

        $testCases = [
            [
                'name' => 'Test Archive - Musnah',
                'kurun_waktu_start' => '2020',
                'retention_aktif' => 1,
                'retention_inaktif' => 1,
                'nasib_akhir' => 'Musnah',
                'expected_status' => 'Musnah'
            ],
            [
                'name' => 'Test Archive - Permanen',
                'kurun_waktu_start' => '2020',
                'retention_aktif' => 1,
                'retention_inaktif' => 1,
                'nasib_akhir' => 'Permanen',
                'expected_status' => 'Permanen'
            ],
            [
                'name' => 'Test Archive - Dinilai Kembali',
                'kurun_waktu_start' => '2020',
                'retention_aktif' => 1,
                'retention_inaktif' => 1,
                'nasib_akhir' => 'Dinilai Kembali',
                'expected_status' => 'Dinilai Kembali'
            ],
            [
                'name' => 'Test Archive - Recent (Should be Aktif)',
                'kurun_waktu_start' => date('Y'),
                'retention_aktif' => 5,
                'retention_inaktif' => 10,
                'nasib_akhir' => 'Musnah',
                'expected_status' => 'Aktif'
            ]
        ];

        foreach ($testCases as $index => $testCase) {
            $this->info("   Creating: {$testCase['name']}");

            // Calculate transition dates
            $kurunWaktuStart = Carbon::parse($testCase['kurun_waktu_start']);
            $transitionActiveDue = $kurunWaktuStart->copy()->addYears($testCase['retention_aktif']);
            $transitionInactiveDue = $transitionActiveDue->copy()->addYears($testCase['retention_inaktif']);

            $archive = Archive::create([
                'user_id' => $user->id,
                'category_id' => $lainnyaCategory->id,
                'classification_id' => $lainnyaClassification->id,
                'nomor_arsip' => "TEST-{$index}-" . date('Y'),
                'description' => $testCase['name'],
                'kurun_waktu_start' => $testCase['kurun_waktu_start'],
                'kurun_waktu_end' => $testCase['kurun_waktu_start'],
                'tingkat_perkembangan' => 'Asli',
                'jumlah' => 1,
                'satuan' => 'Berkas',
                'jumlah_berkas' => 1,
                'lampiran_surat' => 'Test lampiran',
                'skkad' => 'BIASA/TERBUKA',
                'retention_aktif' => $testCase['retention_aktif'],
                'retention_inaktif' => $testCase['retention_inaktif'],
                'manual_nasib_akhir' => $testCase['nasib_akhir'],
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'status' => 'Aktif',
                'index_number' => "TEST-{$index}-" . date('Y'),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);

            $this->info("     - Created archive ID: {$archive->id}");
            $this->info("     - Transition Active Due: {$transitionActiveDue->format('Y-m-d')}");
            $this->info("     - Transition Inactive Due: {$transitionInactiveDue->format('Y-m-d')}");
            $this->info("     - Manual Nasib Akhir: {$testCase['nasib_akhir']}");
            $this->info("     - Expected Status: {$testCase['expected_status']}");
        }

        // 3. Test status calculation
        $this->info('📋 3. Testing status calculation...');

        $archives = Archive::where('description', 'like', 'Test Archive%')->get();

        foreach ($archives as $archive) {
            $this->info("   Testing archive ID: {$archive->id}");
            $this->info("     - Current status: {$archive->status}");
            $this->info("     - Manual nasib_akhir: {$archive->manual_nasib_akhir}");
            $this->info("     - Classification code: {$archive->classification->code}");

            // Simulate status calculation
            $today = today();
            $status = 'Aktif'; // Default

            if ($archive->transition_inactive_due <= $today) {
                // Both active and inactive periods have passed
                // Check if this is LAINNYA category (manual nasib_akhir)
                if ($archive->classification->code === 'LAINNYA') {
                    // Use manual nasib_akhir from archive
                    $status = match (true) {
                        str_starts_with($archive->manual_nasib_akhir, 'Musnah') => 'Musnah',
                        $archive->manual_nasib_akhir === 'Permanen' => 'Permanen',
                        $archive->manual_nasib_akhir === 'Dinilai Kembali' => 'Dinilai Kembali',
                        default => 'Permanen'
                    };
                } else {
                    // Use classification nasib_akhir for JRA categories
                    $status = match (true) {
                        str_starts_with($archive->classification->nasib_akhir, 'Musnah') => 'Musnah',
                        $archive->classification->nasib_akhir === 'Permanen' => 'Permanen',
                        $archive->classification->nasib_akhir === 'Dinilai Kembali' => 'Dinilai Kembali',
                        default => 'Permanen'
                    };
                }
            } elseif ($archive->transition_active_due <= $today) {
                // Only active period has passed
                $status = 'Inaktif';
            }

            $this->info("     - Calculated status: {$status}");

            // Update archive status
            $archive->update(['status' => $status]);
            $this->info("     - Updated status: {$archive->fresh()->status}");
        }

        // 4. Show results
        $this->info('📋 4. Results summary...');

        $results = Archive::where('description', 'like', 'Test Archive%')
            ->orderBy('id')
            ->get(['id', 'description', 'status', 'manual_nasib_akhir', 'transition_active_due', 'transition_inactive_due']);

        foreach ($results as $archive) {
            $this->info("   Archive {$archive->id}: {$archive->description}");
            $this->info("     - Status: {$archive->status}");
            $this->info("     - Manual Nasib Akhir: {$archive->manual_nasib_akhir}");
            $this->info("     - Active Due: {$archive->transition_active_due}");
            $this->info("     - Inactive Due: {$archive->transition_inactive_due}");
        }

        $this->info('✅ STATUS CALCULATION TESTING COMPLETED!');
        $this->info('');
        $this->info('📝 TESTING NOTES:');
        $this->info('- Check if LAINNYA category uses manual nasib_akhir');
        $this->info('- Verify status calculation logic');
        $this->info('- Confirm transition dates are correct');
        $this->info('- Test different nasib_akhir values');
    }
}
