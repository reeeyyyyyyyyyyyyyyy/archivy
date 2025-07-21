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
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('classification_id')->constrained()->onDelete('cascade');
            $table->string('index_number')->unique()->comment('Nomor Berkas/Lampiran Arsip');
            $table->text('uraian');
            $table->date('kurun_waktu_start')->comment('Tanggal Arsip');
            $table->enum('tingkat_perkembangan', ['Asli', 'Salinan', 'Tembusan'])->default('Asli');
            $table->integer('jumlah')->comment('Jumlah Berkas');
            $table->string('ket')->nullable()->comment('Keterangan');
            $table->integer('retention_active');
            $table->integer('retention_inactive');
            $table->date('transition_active_due');
            $table->date('transition_inactive_due');
            $table->enum('status', ['Aktif', 'Inaktif', 'Permanen', 'Musnah'])->default('Aktif');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
