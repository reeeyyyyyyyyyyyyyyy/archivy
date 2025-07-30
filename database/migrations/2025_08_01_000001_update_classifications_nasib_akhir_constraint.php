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
        // Drop the existing check constraint
        DB::statement('ALTER TABLE classifications DROP CONSTRAINT IF EXISTS classifications_nasib_akhir_check');

        // Add the new check constraint with 'Dinilai Kembali' included
        DB::statement("ALTER TABLE classifications ADD CONSTRAINT classifications_nasib_akhir_check CHECK (nasib_akhir IN ('Musnah', 'Permanen', 'Dinilai Kembali'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the updated check constraint
        DB::statement('ALTER TABLE classifications DROP CONSTRAINT IF EXISTS classifications_nasib_akhir_check');

        // Restore the original check constraint
        DB::statement("ALTER TABLE classifications ADD CONSTRAINT classifications_nasib_akhir_check CHECK (nasib_akhir IN ('Musnah', 'Permanen'))");
    }
};
