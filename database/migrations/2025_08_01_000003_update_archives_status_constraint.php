<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE archives DROP CONSTRAINT IF EXISTS archives_status_check');
        DB::statement("ALTER TABLE archives ADD CONSTRAINT archives_status_check CHECK (status IN ('Aktif', 'Inaktif', 'Permanen', 'Musnah', 'Dinilai Kembali'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE archives DROP CONSTRAINT IF EXISTS archives_status_check');
        DB::statement("ALTER TABLE archives ADD CONSTRAINT archives_status_check CHECK (status IN ('Aktif', 'Inaktif', 'Permanen', 'Musnah'))");
    }
};
