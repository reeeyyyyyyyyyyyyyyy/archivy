<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Category;
use App\Models\Classification;
use Illuminate\Support\Facades\Auth;

class TestSelect2FormCommand extends Command
{
    protected $signature = 'test:select2-form';
    protected $description = 'Test Select2 functionality and form input validation';

    public function handle()
    {
        $this->info('🧪 Testing Select2 Form Functionality...');

        // Ensure we have test data
        $user = User::first();
        if (!$user) {
            $this->error('❌ Tidak ada user ditemukan!');
            return;
        }

        Auth::login($user);

        // Test Categories
        $this->info('📋 Testing Categories...');
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->error('❌ Tidak ada kategori ditemukan!');
            return;
        }

        $this->info("✅ Ditemukan {$categories->count()} kategori:");
        foreach ($categories as $category) {
            $this->info("   - ID: {$category->id}, Nama: {$category->nama_kategori}");
        }

        // Test Classifications
        $this->info('📋 Testing Classifications...');
        $classifications = Classification::with('category')->get();
        if ($classifications->isEmpty()) {
            $this->error('❌ Tidak ada klasifikasi ditemukan!');
            return;
        }

        $this->info("✅ Ditemukan {$classifications->count()} klasifikasi:");
        foreach ($classifications as $classification) {
            $this->info("   - ID: {$classification->id}, Code: {$classification->code}, Nama: {$classification->nama_klasifikasi}");
            $this->info("     Category: {$classification->category->nama_kategori}");
            $this->info("     Retention Aktif: {$classification->retention_aktif}, Inaktif: {$classification->retention_inaktif}");
            $this->info("     Nasib Akhir: {$classification->nasib_akhir}");
        }

        // Test JRA vs LAINNYA
        $this->info('📋 Testing JRA vs LAINNYA Logic...');
        $jraClassifications = Classification::where('code', '!=', 'LAINNYA')->get();
        $lainnyaClassification = Classification::where('code', 'LAINNYA')->first();

        $this->info("✅ JRA Classifications: {$jraClassifications->count()}");
        $this->info("✅ LAINNYA Classification: " . ($lainnyaClassification ? 'Ada' : 'Tidak ada'));

        if ($lainnyaClassification) {
            $this->info("   - LAINNYA Category ID: {$lainnyaClassification->category_id}");
            $this->info("   - LAINNYA Retention: {$lainnyaClassification->retention_aktif}/{$lainnyaClassification->retention_inaktif}");
            $this->info("   - LAINNYA Nasib Akhir: {$lainnyaClassification->nasib_akhir}");
        }

        // Test Form Validation Rules
        $this->info('📋 Testing Form Validation Rules...');
        $this->testValidationRules();

        $this->info('✅ Select2 Form tests completed!');
    }

    private function testValidationRules()
    {
        $rules = [
            'classification_id' => 'required|exists:classifications,id',
            'index_number' => 'required|string|max:255|unique:archives,index_number',
            'description' => 'required|string|max:255',
            'lampiran_surat' => 'nullable|string',
            'kurun_waktu_start' => 'required|date',
            'tingkat_perkembangan' => 'required|string',
            'skkad' => 'required|in:SANGAT RAHASIA,TERBATAS,RAHASIA,BIASA/TERBUKA',
            'jumlah_berkas' => 'required|integer|min:1',
            'ket' => 'nullable|string',
            'is_manual_input' => 'boolean',
            'manual_retention_aktif' => 'required_if:is_manual_input,1|integer|min:0',
            'manual_retention_inaktif' => 'required_if:is_manual_input,1|integer|min:0',
            'manual_nasib_akhir' => 'required_if:is_manual_input,1|in:Musnah,Permanen,Dinilai Kembali',
        ];

        $this->info('✅ Validation rules configured:');
        foreach ($rules as $field => $rule) {
            $this->info("   - {$field}: {$rule}");
        }
    }
}
