<?php

namespace App\Services;

use App\Models\Archive;
use App\Models\Classification;
use Carbon\Carbon;

class ArchiveAutomationService
{
    /**
     * Auto-process archive after creation/update
     */
    public function autoProcessArchive(Archive $archive)
    {
        // 1. Auto-detect year
        $year = $archive->kurun_waktu_start->year;
        $archive->update(['year_detected' => $year]);

        // 2. Auto-sort by year (oldest first)
        $this->autoSortByYear($archive);

        // 3. Auto-assign storage (optional for now)
        // $this->autoAssignStorage($archive);

        // 4. Auto-generate definitive number per tahun
        $this->generateDefinitiveNumber($archive);
    }

    /**
     * Auto-sort archives by year (oldest first)
     */
    public function autoSortByYear(Archive $archive)
    {
        $year = $archive->kurun_waktu_start->year;

        // Get all archives with same classification, sorted by year
        $relatedArchives = Archive::where('classification_id', $archive->classification_id)
            ->orderBy('kurun_waktu_start')
            ->get();

        // Calculate sort order based on year
        $sortOrder = $relatedArchives->where('kurun_waktu_start', '<=', $archive->kurun_waktu_start)->count();

        $archive->update(['sort_order' => $sortOrder]);
    }

    /**
     * Auto-assign storage based on year order
     */
    public function autoAssignStorage(Archive $archive)
    {
        $year = $archive->kurun_waktu_start->year;
        $classificationId = $archive->classification_id;

        // Find optimal storage location
        $storageLocation = $this->findOptimalStorageLocation($year, $classificationId);

        // Assign location
        $archive->update([
            'rack_number' => $storageLocation['rack_number'],
            'box_number' => $storageLocation['box_number'],
            'row_number' => $storageLocation['row_number']
        ]);
    }

    /**
     * Generate definitive number based on storage location
     */
    public function generateDefinitiveNumber(Archive $archive)
    {
        // Check if archive has storage location
        if (!$archive->rack_number || !$archive->row_number || !$archive->box_number || !$archive->file_number) {
            // If no storage location, use simple sequential number per year
            $classificationId = $archive->classification_id;
            $year = $archive->kurun_waktu_start->year;

            $count = Archive::where('classification_id', $classificationId)
                           ->whereYear('kurun_waktu_start', $year)
                           ->where('id', '<=', $archive->id)
                           ->count();

            $definitiveNumber = $count;
        } else {
            // Generate definitive number based on storage location
            $definitiveNumber = $this->generateLocationBasedDefinitiveNumber($archive);
        }

        $archive->update(['definitive_number' => $definitiveNumber]);
    }

    /**
     * Generate definitive number based on storage location (Rack-Row-Box-File format)
     */
    private function generateLocationBasedDefinitiveNumber(Archive $archive): int
    {
        // Format: RRBBFF (Rack-Row-Box-File)
        // Example: Rak 1, Row 1, Box 1, File 1 = 010101
        // Example: Rak 1, Row 1, Box 1, File 10 = 010110
        $rackNumber = str_pad($archive->rack_number, 2, '0', STR_PAD_LEFT);
        $rowNumber = str_pad($archive->row_number, 2, '0', STR_PAD_LEFT);
        $boxNumber = str_pad($archive->box_number, 2, '0', STR_PAD_LEFT);
        $fileNumber = str_pad($archive->file_number, 2, '0', STR_PAD_LEFT);

        $definitiveNumber = (int) ($rackNumber . $rowNumber . $boxNumber . $fileNumber);

        return $definitiveNumber;
    }

    /**
     * Find optimal storage location based on year and classification
     */
    private function findOptimalStorageLocation($year, $classificationId)
    {
        // For now, return default values
        // This will be implemented later when storage management is enhanced
        return [
            'rack_number' => 1,
            'box_number' => 1,
            'row_number' => 1
        ];
    }
}
