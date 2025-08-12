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
            // Storage location fields
            $table->unsignedInteger('box_number')->nullable()->after('skkad')->comment('Global box sequence number');
            $table->unsignedInteger('file_number')->nullable()->after('box_number')->comment('File number within box (restarts at 1 per box)');
            $table->unsignedSmallInteger('rack_number')->nullable()->after('file_number')->comment('Physical rack number');
            $table->unsignedSmallInteger('row_number')->nullable()->after('rack_number')->comment('Shelf row number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->dropColumn([
                'box_number',
                'file_number',
                'rack_number',
                'row_number'
            ]);
        });
    }
};
