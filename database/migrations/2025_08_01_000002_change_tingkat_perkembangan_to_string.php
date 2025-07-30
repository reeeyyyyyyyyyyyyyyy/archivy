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
        // Change tingkat_perkembangan from enum to string
        DB::statement('ALTER TABLE archives ALTER COLUMN tingkat_perkembangan TYPE VARCHAR(255)');

        // Remove the enum constraint
        DB::statement('ALTER TABLE archives DROP CONSTRAINT IF EXISTS archives_tingkat_perkembangan_check');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum
        DB::statement('ALTER TABLE archives ALTER COLUMN tingkat_perkembangan TYPE VARCHAR(255)');
        DB::statement("ALTER TABLE archives ADD CONSTRAINT archives_tingkat_perkembangan_check CHECK (tingkat_perkembangan IN ('Asli', 'Salinan', 'Tembusan'))");
    }
};
