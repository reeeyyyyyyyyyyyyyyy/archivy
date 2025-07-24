<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            // Rename fields to match new schema
            $table->renameColumn('uraian', 'description');
            $table->renameColumn('jumlah', 'jumlah_berkas');
            $table->renameColumn('retention_active', 'retention_aktif');
            $table->renameColumn('retention_inactive', 'retention_inaktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            // Rename fields back to original names
            $table->renameColumn('description', 'uraian');
            $table->renameColumn('jumlah_berkas', 'jumlah');
            $table->renameColumn('retention_aktif', 'retention_active');
            $table->renameColumn('retention_inaktif', 'retention_inactive');
        });
    }
};
