<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Storage Racks Table
        Schema::create('storage_racks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // e.g., "Rak 1", "Rak 2"
            $table->text('description')->nullable();
            $table->integer('total_rows')->default(0);
            $table->integer('total_boxes')->default(0);
            $table->integer('capacity_per_box')->default(50); // Default limit per box
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();
        });

        // Storage Rows Table
        Schema::create('storage_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rack_id')->constrained('storage_racks')->onDelete('cascade');
            $table->integer('row_number'); // 1, 2, 3, etc.
            $table->integer('total_boxes')->default(0);
            $table->integer('available_boxes')->default(0);
            $table->enum('status', ['available', 'full', 'maintenance'])->default('available');
            $table->timestamps();

            $table->unique(['rack_id', 'row_number']);
        });

        // Storage Boxes Table
        Schema::create('storage_boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rack_id')->constrained('storage_racks')->onDelete('cascade');
            $table->foreignId('row_id')->constrained('storage_rows')->onDelete('cascade');
            $table->integer('box_number'); // Global box number
            $table->integer('archive_count')->default(0);
            $table->integer('capacity')->default(50);
            $table->enum('status', ['available', 'partially_full', 'full', 'reserved'])->default('available');
            $table->timestamps();

            $table->unique(['rack_id', 'row_id', 'box_number']);
            $table->unique('box_number'); // Global unique
        });

        // Storage Capacity Settings Table
        Schema::create('storage_capacity_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rack_id')->constrained('storage_racks')->onDelete('cascade');
            $table->integer('default_capacity_per_box')->default(50);
            $table->integer('warning_threshold')->default(40); // 80% of capacity
            $table->boolean('auto_assign')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_capacity_settings');
        Schema::dropIfExists('storage_boxes');
        Schema::dropIfExists('storage_rows');
        Schema::dropIfExists('storage_racks');
    }
};
