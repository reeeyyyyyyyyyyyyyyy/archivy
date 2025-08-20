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
        // Drop existing constraint if exists
        DB::statement("ALTER TABLE classifications DROP CONSTRAINT IF EXISTS classifications_nasib_akhir_check");

        // Add new constraint with "Masuk ke Berkas Perseorangan"
        DB::statement("ALTER TABLE classifications ADD CONSTRAINT classifications_nasib_akhir_check CHECK (nasib_akhir IN ('Musnah', 'Permanen', 'Dinilai Kembali', 'Masuk ke Berkas Perseorangan'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new constraint
        DB::statement("ALTER TABLE classifications DROP CONSTRAINT IF EXISTS classifications_nasib_akhir_check");

        // Restore original constraint
        DB::statement("ALTER TABLE classifications ADD CONSTRAINT classifications_nasib_akhir_check CHECK (nasib_akhir IN ('Musnah', 'Permanen', 'Dinilai Kembali'))");
    }
};
