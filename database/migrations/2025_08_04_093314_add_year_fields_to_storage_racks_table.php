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
        Schema::table('storage_racks', function (Blueprint $table) {
            $table->integer('year_start')->nullable()->after('status');
            $table->integer('year_end')->nullable()->after('year_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storage_racks', function (Blueprint $table) {
            $table->dropColumn(['year_start', 'year_end']);
        });
    }
};
