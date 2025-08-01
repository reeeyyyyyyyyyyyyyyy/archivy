<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL, we need to drop and recreate the column with new enum values
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('nasib_akhir');
        });
        
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('nasib_akhir', [
                'Permanen',
                'Musnah',
                'Musnah Kecuali SK Masuk Berkas Perseorangan',
                'Musnah Kecuali Dokumen Penting',
                'Musnah Kecuali Laporan Tahunan',
                'Musnah Kecuali Data Statistik',
                'Dinilai Kembali',
                '-'
            ])->default('Dinilai Kembali')->after('retention_inactive');
        });
        
        // Update existing data to default value
        DB::table('categories')->update(['nasib_akhir' => 'Musnah']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('nasib_akhir');
        });
        
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('nasib_akhir', ['Musnah', 'Permanen', 'Dinilai Kembali', '-'])->default('Dinilai Kembali')->after('retention_inactive');
        });
    }
}; 