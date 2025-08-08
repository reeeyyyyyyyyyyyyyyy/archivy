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
            // Add automation tracking fields
            $table->integer('definitive_number')->nullable()->after('is_manual_input');
            $table->integer('year_detected')->nullable()->after('definitive_number');
            $table->integer('sort_order')->nullable()->after('year_detected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->dropColumn([
                'definitive_number',
                'year_detected',
                'sort_order'
            ]);
        });
    }
};
