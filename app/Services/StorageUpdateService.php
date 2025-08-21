<?php

namespace App\Services;

use App\Models\Archive;
use App\Models\StorageBox;
use App\Models\StorageRack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StorageUpdateService
{
    /**
     * Update storage location for an archive
     */
    public function updateStorageLocation($archiveId, $rackId, $rowNumber, $boxNumber, $fileNumber = null)
    {
        return DB::transaction(function () use ($archiveId, $rackId, $rowNumber, $boxNumber, $fileNumber) {
            // Lock the archive to prevent concurrent modifications
            $archive = Archive::where('id', $archiveId)
                ->lockForUpdate()
                ->firstOrFail();

            // Lock the storage box to prevent concurrent access (scoped by rack)
            $storageBox = StorageBox::where('rack_id', $rackId)
                ->where('box_number', $boxNumber)
                ->lockForUpdate()
                ->first();

            if (!$storageBox) {
                throw new \Exception("Box {$boxNumber} tidak ditemukan!");
            }

            // Use real-time counts rather than cached archive_count
            $realArchiveCount = Archive::where('rack_number', $rackId)
                ->where('box_number', $boxNumber)
                ->count();

            // Check if box capacity is exceeded
            if ($realArchiveCount >= $storageBox->capacity) {
                throw new \Exception("Box {$boxNumber} sudah mencapai kapasitas maksimal!");
            }

                        // Get next file number if not provided
            if (!$fileNumber) {
                // Next file number follows correct definitive number rules
                $fileNumber = Archive::getNextFileNumberCorrect(
                    $rackId,
                    $boxNumber,
                    $archive->classification_id,
                    $archive->kurun_waktu_start->year
                );
            }

            // Update storage box count based on REAL number of archives after insert
            // We will recalc after archive update to avoid race conditions

            // Update archive with location
            $archive->update([
                'box_number' => $boxNumber,
                'file_number' => $fileNumber,
                'rack_number' => $rackId,
                'row_number' => $rowNumber,
                'updated_by' => Auth::id(),
            ]);

            // Recalculate storage box count from archives and update status
            $realCountAfter = Archive::where('rack_number', $rackId)
                ->where('box_number', $boxNumber)
                ->count();
            $storageBox->archive_count = $realCountAfter;
            $storageBox->save();
            $storageBox->updateStatus();

            // Log the storage update
            Log::info("Storage location updated for archive ID {$archiveId}: Box {$boxNumber}, File {$fileNumber}, Rak {$rackId}, Baris {$rowNumber} by user " . Auth::id());

            return [
                'success' => true,
                'archive' => $archive,
                'storage_box' => $storageBox,
                'message' => "Lokasi penyimpanan berhasil di-set untuk arsip: {$archive->index_number}. File Number: {$fileNumber}"
            ];
        });
    }

    /**
     * Bulk update storage locations
     */
    public function bulkUpdateStorageLocation($archiveIds, $rackId)
    {
        return DB::transaction(function () use ($archiveIds, $rackId) {
            $successCount = 0;
            $errors = [];

            foreach ($archiveIds as $archiveId) {
                try {
                    // Find available box in the rack
                    $availableBox = StorageBox::where('rack_id', $rackId)
                        ->where('status', 'available')
                        ->first();

                    if ($availableBox) {
                        $result = $this->updateStorageLocation(
                            $archiveId,
                            $rackId,
                            $availableBox->row->row_number,
                            $availableBox->box_number
                        );
                        $successCount++;
                    } else {
                        $errors[] = "Tidak ada box tersedia di rak yang dipilih untuk arsip ID {$archiveId}";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error untuk arsip ID {$archiveId}: " . $e->getMessage();
                }
            }

            return [
                'success' => $successCount > 0,
                'success_count' => $successCount,
                'errors' => $errors,
                'message' => "Berhasil memindahkan {$successCount} arsip ke rak yang dipilih"
            ];
        });
    }

    /**
     * Remove storage location from archive
     */
    public function removeStorageLocation($archiveId)
    {
        return DB::transaction(function () use ($archiveId) {
            $archive = Archive::where('id', $archiveId)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$archive->hasStorageLocation()) {
                throw new \Exception("Arsip tidak memiliki lokasi penyimpanan!");
            }

            // Update storage box count
            $storageBox = StorageBox::where('box_number', $archive->box_number)->first();
            if ($storageBox) {
                $storageBox->decrement('archive_count');
                $storageBox->updateStatus();
            }

            // Clear archive location
            $archive->update([
                'box_number' => null,
                'file_number' => null,
                'rack_number' => null,
                'row_number' => null,
                'updated_by' => Auth::id(),
            ]);

            Log::info("Storage location removed for archive ID {$archiveId} by user " . Auth::id());

            return [
                'success' => true,
                'message' => "Lokasi penyimpanan berhasil dihapus untuk arsip: {$archive->index_number}"
            ];
        });
    }

    /**
     * Get storage statistics
     */
    public function getStorageStatistics()
    {
        $totalArchives = Archive::count();
        $archivesWithLocation = Archive::withLocation()->count();
        $archivesWithoutLocation = Archive::withoutLocation()->count();

        $totalBoxes = StorageBox::count();
        $availableBoxes = StorageBox::where('status', 'available')->count();
        $fullBoxes = StorageBox::where('status', 'full')->count();

        return [
            'total_archives' => $totalArchives,
            'archives_with_location' => $archivesWithLocation,
            'archives_without_location' => $archivesWithoutLocation,
            'total_boxes' => $totalBoxes,
            'available_boxes' => $availableBoxes,
            'full_boxes' => $fullBoxes,
            'utilization_percentage' => $totalBoxes > 0 ? round(($totalBoxes - $availableBoxes) / $totalBoxes * 100, 2) : 0
        ];
    }

    /**
     * Get available boxes for a specific rack
     */
    public function getAvailableBoxes($rackId)
    {
        return StorageBox::where('rack_id', $rackId)
            ->where('status', 'available')
            ->with('row')
            ->get();
    }

    /**
     * Get next available box for a specific rack
     */
    public function getNextAvailableBox($rackId)
    {
        return StorageBox::where('rack_id', $rackId)
            ->where('status', 'available')
            ->with('row')
            ->first();
    }
}
