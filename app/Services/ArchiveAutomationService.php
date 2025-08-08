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

        // 4. Auto-generate definitive number (optional for now)
        // $this->generateDefinitiveNumber($archive);
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
     * Generate definitive number per masalah
     */
    public function generateDefinitiveNumber(Archive $archive)
    {
        $classificationId = $archive->classification_id;
        $boxNumber = $archive->box_number;

        // Count archives in this box with same classification
        $count = Archive::where('box_number', $boxNumber)
                       ->where('classification_id', $classificationId)
                       ->count();

        // Definitive number restarts at 1 for each classification
        $definitiveNumber = $count;

        $archive->update(['definitive_number' => $definitiveNumber]);
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
