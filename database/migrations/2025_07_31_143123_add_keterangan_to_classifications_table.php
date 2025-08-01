<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classifications', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('nama_klasifikasi');
        });

        Schema::table('archives', function (Blueprint $table) {
            // Ganti renameColumn dengan addColumn jika kolom 'ket' belum ada
            if (Schema::hasColumn('archives', 'ket')) {
                $table->renameColumn('ket', 'keterangan');
            } else {
                $table->text('keterangan')->nullable()->after('jumlah_berkas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('classifications', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });

        Schema::table('archives', function (Blueprint $table) {
            // Periksa apakah kolom keterangan ada sebelum di-rename
            if (Schema::hasColumn('archives', 'keterangan')) {
                $table->renameColumn('keterangan', 'ket');
            }
        });
    }
};