<?php

namespace App\Observers;

use App\Models\Archive;
use App\Models\StorageBox;
use Illuminate\Support\Facades\Log;

class ArchiveObserver
{
    /**
     * Handle the Archive "created" event.
     */
    public function created(Archive $archive): void
    {
        // Update storage box count when archive is created with location
        if ($archive->rack_number && $archive->box_number && $archive->file_number) {
            $this->updateStorageBoxCount($archive);
        }
    }

    /**
     * Handle the Archive "updated" event.
     */
    public function updated(Archive $archive): void
    {
        // Update storage box count when archive location changes
        if ($archive->wasChanged(['rack_number', 'box_number', 'file_number'])) {
            $this->updateStorageBoxCount($archive);

            // If archive was moved from another box, update the old box count
            if ($archive->getOriginal('rack_number') && $archive->getOriginal('box_number')) {
                $this->updateStorageBoxCountFromOriginal($archive);
            }
        }
    }

    /**
     * Handle the Archive "deleted" event.
     */
    public function deleted(Archive $archive): void
    {
        // Update storage box count when archive is deleted
        if ($archive->rack_number && $archive->box_number) {
            $this->decrementStorageBoxCount($archive);
        }
    }

    /**
     * Update storage box count for new/updated archive
     */
    private function updateStorageBoxCount(Archive $archive): void
    {
        try {
            $storageBox = StorageBox::where('rack_id', $archive->rack_number)
                ->where('box_number', $archive->box_number)
                ->first();

            if ($storageBox) {
                // Recalculate archive count for this box
                $actualCount = Archive::where('rack_number', $archive->rack_number)
                    ->where('box_number', $archive->box_number)
                    ->whereNotNull('file_number')
                    ->count();

                $storageBox->archive_count = $actualCount;
                $storageBox->updateStatus();
                $storageBox->save();

                Log::info('Storage box count updated', [
                    'box_id' => $storageBox->id,
                    'new_count' => $actualCount
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating storage box count', [
                'error' => $e->getMessage(),
                'archive_id' => $archive->id
            ]);
        }
    }

    /**
     * Update storage box count for archive that was moved from another box
     */
    private function updateStorageBoxCountFromOriginal(Archive $archive): void
    {
        try {
            $originalRack = $archive->getOriginal('rack_number');
            $originalBox = $archive->getOriginal('box_number');

            $storageBox = StorageBox::where('rack_id', $originalRack)
                ->where('box_number', $originalBox)
                ->first();

            if ($storageBox) {
                // Recalculate archive count for the old box
                $actualCount = Archive::where('rack_number', $originalRack)
                    ->where('box_number', $originalBox)
                    ->whereNotNull('file_number')
                    ->count();

                $storageBox->archive_count = $actualCount;
                $storageBox->updateStatus();
                $storageBox->save();

                Log::info('Old storage box count updated', [
                    'box_id' => $storageBox->id,
                    'new_count' => $actualCount
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating old storage box count', [
                'error' => $e->getMessage(),
                'archive_id' => $archive->id
            ]);
        }
    }

    /**
     * Decrement storage box count when archive is deleted
     */
    private function decrementStorageBoxCount(Archive $archive): void
    {
        try {
            $storageBox = StorageBox::where('rack_id', $archive->rack_number)
                ->where('box_number', $archive->box_number)
                ->first();

            if ($storageBox) {
                // Recalculate archive count for this box
                $actualCount = Archive::where('rack_number', $archive->rack_number)
                    ->where('box_number', $archive->box_number)
                    ->whereNotNull('file_number')
                    ->count();

                $storageBox->archive_count = $actualCount;
                $storageBox->updateStatus();
                $storageBox->save();

                Log::info('Storage box count decremented', [
                    'box_id' => $storageBox->id,
                    'new_count' => $actualCount
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error decrementing storage box count', [
                'error' => $e->getMessage(),
                'archive_id' => $archive->id
            ]);
        }
    }
}
