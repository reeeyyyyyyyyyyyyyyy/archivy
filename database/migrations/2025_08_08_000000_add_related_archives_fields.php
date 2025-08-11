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
            // Add related archives tracking
            $table->unsignedBigInteger('parent_archive_id')->nullable()->after('updated_by');
            $table->boolean('is_parent')->default(false)->after('parent_archive_id');

            // Add foreign key constraint
            $table->foreign('parent_archive_id')
                  ->references('id')
                  ->on('archives')
                  ->onDelete('set null');

            // Add indexes for performance
            $table->index(['category_id', 'classification_id', 'lampiran_surat'], 'idx_archives_related');
            $table->index('parent_archive_id', 'idx_archives_parent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_archives_related');
            $table->dropIndex('idx_archives_parent');

            // Drop foreign key
            $table->dropForeign(['parent_archive_id']);

            // Drop columns
            $table->dropColumn([
                'parent_archive_id',
                'is_parent'
            ]);
        });
    }
};
