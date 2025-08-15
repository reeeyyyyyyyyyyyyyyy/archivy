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
        // First update existing archives with "Masuk ke Berkas Perseorangan" nasib akhir to have "Berkas Perseorangan" status
        DB::statement("UPDATE archives SET status = 'Berkas Perseorangan' WHERE manual_nasib_akhir = 'Masuk ke Berkas Perseorangan'");

        // Drop existing constraint if exists
        DB::statement("ALTER TABLE archives DROP CONSTRAINT IF EXISTS archives_status_check");

        // Add new constraint with "Berkas Perseorangan" and "Dinilai Kembali"
        DB::statement("ALTER TABLE archives ADD CONSTRAINT archives_status_check CHECK (status IN ('Aktif', 'Inaktif', 'Permanen', 'Musnah', 'Dinilai Kembali', 'Berkas Perseorangan'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new constraint
        DB::statement("ALTER TABLE archives DROP CONSTRAINT IF EXISTS archives_status_check");

        // Restore original constraint
        DB::statement("ALTER TABLE archives ADD CONSTRAINT archives_status_check CHECK (status IN ('Aktif', 'Inaktif', 'Permanen', 'Musnah', 'Dinilai Kembali'))");

        // Revert status changes
        DB::statement("UPDATE archives SET status = 'Aktif' WHERE status = 'Berkas Perseorangan'");
    }
};
