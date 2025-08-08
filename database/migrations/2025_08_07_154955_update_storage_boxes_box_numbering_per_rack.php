<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, drop the global unique constraint on box_number
        Schema::table('storage_boxes', function (Blueprint $table) {
            $table->dropUnique(['box_number']);
        });

        // Add a new unique constraint for per-rack box numbering
        Schema::table('storage_boxes', function (Blueprint $table) {
            $table->unique(['rack_id', 'box_number'], 'storage_boxes_rack_box_unique');
        });

        // Update existing box numbers to be per-rack
        $racks = DB::table('storage_racks')->get();

        foreach ($racks as $rack) {
            $boxes = DB::table('storage_boxes')
                ->where('rack_id', $rack->id)
                ->orderBy('id')
                ->get();

            $boxNumber = 1;
            foreach ($boxes as $box) {
                DB::table('storage_boxes')
                    ->where('id', $box->id)
                    ->update(['box_number' => $boxNumber]);
                $boxNumber++;
            }
        }
    }

    public function down(): void
    {
        // Drop the per-rack unique constraint
        Schema::table('storage_boxes', function (Blueprint $table) {
            $table->dropUnique('storage_boxes_rack_box_unique');
        });

        // Restore global unique constraint
        Schema::table('storage_boxes', function (Blueprint $table) {
            $table->unique('box_number');
        });

        // Restore global box numbering (this is simplified - in real scenario you'd need to preserve original numbers)
        $boxes = DB::table('storage_boxes')->orderBy('id')->get();
        $globalBoxNumber = 1;

        foreach ($boxes as $box) {
            DB::table('storage_boxes')
                ->where('id', $box->id)
                ->update(['box_number' => $globalBoxNumber]);
            $globalBoxNumber++;
        }
    }
};
