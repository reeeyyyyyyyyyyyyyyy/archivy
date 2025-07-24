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
        Schema::table('classifications', function (Blueprint $table) {
            // Rename name to nama_klasifikasi for clarity
            $table->renameColumn('name', 'nama_klasifikasi');

            // Add retention and nasib_akhir fields
            $table->integer('retention_aktif')->default(0)->after('nama_klasifikasi');
            $table->integer('retention_inaktif')->default(0)->after('retention_aktif');
            $table->enum('nasib_akhir', ['Musnah', 'Permanen'])->default('Permanen')->after('retention_inaktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classifications', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn(['retention_aktif', 'retention_inaktif', 'nasib_akhir']);

            // Rename back nama_klasifikasi to name
            $table->renameColumn('nama_klasifikasi', 'name');
        });
    }
};
