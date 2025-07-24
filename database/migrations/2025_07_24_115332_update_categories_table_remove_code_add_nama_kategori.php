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
        Schema::table('categories', function (Blueprint $table) {
            // Remove existing columns that are not needed
            $table->dropColumn(['retention_active', 'retention_inactive', 'nasib_akhir']);

            // Rename name to nama_kategori for clarity
            $table->renameColumn('name', 'nama_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Add back the removed columns
            $table->integer('retention_active')->default(0);
            $table->integer('retention_inactive')->default(0);
            $table->enum('nasib_akhir', ['Musnah', 'Permanen', 'Dinilai Kembali'])->default('Dinilai Kembali');

            // Rename back nama_kategori to name
            $table->renameColumn('nama_kategori', 'name');
        });
    }
};
