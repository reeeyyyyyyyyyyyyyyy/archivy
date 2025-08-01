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
        Schema::table('archives', function (Blueprint $table) {
            // Change index_number comment to reflect new meaning (nomor arsip)
            $table->string('index_number')->comment('Nomor Arsip')->change();

            // Add new fields for Phase 1 revisions
            $table->text('lampiran_surat')->nullable()->after('description')->comment('Lampiran Arsip (text/paragraph)');
            $table->enum('skkd', ['SANGAT RAHASIA', 'TERBATAS', 'RAHASIA', 'BIASA/TERBUKA'])->default('BIASA/TERBUKA')->after('tingkat_perkembangan');

            // Storage location fields for new workflow
            $table->unsignedInteger('box_number')->nullable()->after('skkad')->comment('Global box sequence number');
            $table->unsignedInteger('file_number')->nullable()->after('box_number')->comment('File number within box (restarts at 1 per box)');
            $table->unsignedSmallInteger('rack_number')->nullable()->after('file_number')->comment('Physical rack number');
            $table->unsignedSmallInteger('row_number')->nullable()->after('rack_number')->comment('Shelf row number');

            // Add re-evaluation flag for "Dinilai Kembali" archives
            $table->boolean('re_evaluation')->default(false)->after('row_number')->comment('Archive marked for re-evaluation');

            // Manual input flags for non-JRA categories
            $table->boolean('is_manual_input')->default(false)->after('re_evaluation')->comment('Manual input for non-JRA categories');
            $table->string('manual_classification_code')->nullable()->after('is_manual_input')->comment('Manual classification code');
            $table->string('manual_category_name')->nullable()->after('manual_classification_code')->comment('Manual category name');
            $table->integer('manual_retention_aktif')->nullable()->after('manual_category_name')->comment('Manual active retention period');
            $table->integer('manual_retention_inaktif')->nullable()->after('manual_retention_aktif')->comment('Manual inactive retention period');
            $table->enum('manual_nasib_akhir', ['Musnah', 'Permanen', 'Dinilai Kembali'])->nullable()->after('manual_retention_inaktif')->comment('Manual final disposition');
        });

                // Status field is already varchar, so we don't need to modify the column type
        // The new 'Dinilai Kembali' status can be used directly
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn([
                'lampiran_surat',
                'skkd',
                'box_number',
                'file_number',
                'rack_number',
                'row_number',
                're_evaluation',
                'is_manual_input',
                'manual_classification_code',
                'manual_category_name',
                'manual_retention_aktif',
                'manual_retention_inaktif',
                'manual_nasib_akhir'
            ]);

            // Note: Status field remains varchar, manual cleanup of 'Dinilai Kembali' status may be needed

            // Revert index_number comment
            $table->string('index_number')->comment('Nomor Berkas/Lampiran Arsip')->change();
        });
    }
};
