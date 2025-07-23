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
            $table->boolean('manual_status_override')->default(false)->after('status');
            $table->timestamp('manual_override_at')->nullable()->after('manual_status_override');
            $table->unsignedBigInteger('manual_override_by')->nullable()->after('manual_override_at');
            
            $table->foreign('manual_override_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->dropForeign(['manual_override_by']);
            $table->dropColumn(['manual_status_override', 'manual_override_at', 'manual_override_by']);
        });
    }
}; 