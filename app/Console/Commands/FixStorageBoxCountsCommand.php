<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StorageBox;
use App\Models\StorageRow;
use App\Models\StorageRack;
use Illuminate\Support\Facades\DB;

class FixStorageBoxCountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:storage-box-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix storage box counts by recalculating archive counts from actual data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting storage box counts fix...');

        try {
            DB::beginTransaction();

            // Get all storage boxes
            $boxes = StorageBox::all();
            $fixedCount = 0;

            foreach ($boxes as $box) {
                // Count actual archives in this box
                $actualCount = DB::table('archives')
                    ->where('rack_number', $box->rack_id)
                    ->where('row_number', $box->row_id)
                    ->where('box_number', $box->box_number)
                    ->whereNotNull('file_number')
                    ->count();

                // Update box if count is different
                if ($box->archive_count !== $actualCount) {
                    $oldCount = $box->archive_count;
                    $box->archive_count = $actualCount;
                    $box->save();

                    $this->line("Box {$box->id}: {$oldCount} → {$actualCount}");
                    $fixedCount++;
                }
            }

            // Update row counts
            $rows = StorageRow::all();
            foreach ($rows as $row) {
                $actualCount = StorageBox::where('row_id', $row->id)->sum('archive_count');
                if ($row->total_boxes !== $actualCount) {
                    $row->total_boxes = $actualCount;
                    $row->save();
                }
            }

            // Update rack counts
            $racks = StorageRack::all();
            foreach ($racks as $rack) {
                $actualCount = StorageBox::where('rack_id', $rack->id)->sum('archive_count');
                if ($rack->total_boxes !== $actualCount) {
                    $rack->total_boxes = $actualCount;
                    $rack->save();
                }
            }

            DB::commit();

            $this->info("✅ Storage box counts fixed successfully! {$fixedCount} boxes updated.");
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error fixing storage box counts: " . $e->getMessage());
            return 1;
        }
    }
}
