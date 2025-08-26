<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\StorageBox;
use App\Models\StorageRack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RelatedArchivesController extends Controller
{
    // Show related archives for specific archive
    public function index(Archive $archive)
    {
        Log::info('RelatedArchivesController@index called', [
            'archive_id' => $archive->id,
            'archive_description' => $archive->description
        ]);

        try {
            $relatedArchives = $archive->getAllRelatedArchives();

            Log::info('Related archives found', [
                'count' => $relatedArchives->count(),
                'archive_ids' => $relatedArchives->pluck('id')->toArray()
            ]);

            // Get all active racks with available boxes (same as StorageLocationController)
            $racks = StorageRack::with(['rows', 'boxes'])
                ->where('status', 'active')
                ->get()
                ->filter(function ($rack) {
                    // Calculate available boxes using new formula
                    $capacity = $rack->capacity_per_box;
                    $n = $capacity;
                    $halfN = $n / 2;

                    $availableBoxes = $rack->boxes->filter(function ($box) use ($halfN) {
                        return $box->archive_count < $halfN; // Available if less than half capacity
                    });

                    return $availableBoxes->count() > 0;
                });

            // Add next available box data for each rack (same as StorageLocationController)
            foreach ($racks as $rack) {
                // Load boxes with their relationships
                $rack->load(['boxes' => function ($query) {
                    $query->orderBy('box_number');
                }]);

                // Calculate available boxes using real-time data
                $availableBoxes = $rack->boxes->filter(function ($box) use ($rack) {
                    $realTimeArchiveCount = \App\Models\Archive::where('rack_number', $rack->id)
                        ->where('box_number', $box->box_number)
                        ->count();
                    return $realTimeArchiveCount < $box->capacity;
                });

                $partiallyFullBoxes = $rack->boxes->filter(function ($box) use ($rack) {
                    $realTimeArchiveCount = \App\Models\Archive::where('rack_number', $rack->id)
                        ->where('box_number', $box->box_number)
                        ->count();
                    return $realTimeArchiveCount >= $box->capacity / 2 && $realTimeArchiveCount < $box->capacity;
                });

                $fullBoxes = $rack->boxes->filter(function ($box) use ($rack) {
                    $realTimeArchiveCount = \App\Models\Archive::where('rack_number', $rack->id)
                        ->where('box_number', $box->box_number)
                        ->count();
                    return $realTimeArchiveCount >= $box->capacity;
                });

                // Set calculated counts
                $rack->available_boxes_count = $availableBoxes->count();
                $rack->partially_full_boxes_count = $partiallyFullBoxes->count();
                $rack->full_boxes_count = $fullBoxes->count();

                // Ensure boxes have all required data with real-time archive count
                foreach ($rack->boxes as $box) {
                    $box->row_number = $box->row ? $box->row->row_number : 0;
                    $box->box_number = $box->box_number;

                    // Get real-time archive count from actual archives for this specific rack
                    $realTimeArchiveCount = \App\Models\Archive::where('rack_number', $rack->id)
                        ->where('box_number', $box->box_number)
                        ->count();
                    $box->archive_count = $realTimeArchiveCount;

                    $box->capacity = $box->capacity;

                    // Calculate status using real-time count
                    if ($realTimeArchiveCount >= $box->capacity) {
                        $box->status = 'full';
                    } elseif ($realTimeArchiveCount >= $box->capacity / 2) {
                        $box->status = 'partially_full';
                    } else {
                        $box->status = 'available';
                    }
                }

                $nextBox = $rack->getNextAvailableBox();
                if ($nextBox) {
                    $rack->next_available_box = [
                        'box_number' => $nextBox->box_number,
                        'row_number' => $nextBox->row_number,
                        'next_file_number' => $nextBox->getNextFileNumber()
                    ];
                } else {
                    $rack->next_available_box = null;
                }

                // Add rack data for JavaScript
                $rack->total_rows = $rack->total_rows;
                $rack->total_boxes = $rack->total_boxes;
                $rack->capacity_per_box = $rack->capacity_per_box;
            }

            // Ensure racks is properly formatted for JavaScript
            $racks = $racks->values();

            // Determine the correct view based on user role
            $user = Auth::user();
            $viewPath = match (true) {
                $user->roles->contains('name', 'admin') => 'admin.archives.related',
                $user->roles->contains('name', 'staff') => 'staff.archives.related',
                $user->roles->contains('name', 'intern') => 'intern.archives.related',
                default => 'staff.archives.related'
            };

            return view($viewPath, compact('archive', 'relatedArchives', 'racks'));
        } catch (\Exception $e) {
            Log::error('Error in RelatedArchivesController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Show global related archives by category
    public function byCategory(Request $request)
    {
        $categoryId = $request->category_id;
        $archives = Archive::where('category_id', $categoryId)
            ->where('is_parent', true)
            ->with(['relatedArchives'])
            ->orderBy('kurun_waktu_start')
            ->get();

        // Determine the correct view based on user role
        $user = Auth::user();
        $viewPath = match (true) {
            $user->roles->contains('name', 'admin') => 'admin.archives.related-category',
            $user->roles->contains('name', 'staff') => 'staff.archives.related-category',
            $user->roles->contains('name', 'intern') => 'intern.archives.related-category',
            default => 'intern.archives.related-category'
        };

        return view($viewPath, compact('archives'));
    }

    // Create related archive with auto-filled data
    public function createRelated(Archive $parentArchive)
    {
        // Load the parent archive with its relationships
        $parentArchive->load(['category', 'classification']);

        Log::info('RelatedArchivesController@createRelated called', [
            'parent_archive_id' => $parentArchive->id,
            'parent_archive_description' => $parentArchive->description,
            'category' => $parentArchive->category ? $parentArchive->category->nama_kategori : 'NULL',
            'classification' => $parentArchive->classification ? $parentArchive->classification->nama_klasifikasi : 'NULL'
        ]);

        // Determine the correct view based on user role
        $user = Auth::user();
        $viewPath = match (true) {
            $user->roles->contains('name', 'admin') => 'admin.archives.create-related',
            $user->roles->contains('name', 'staff') => 'staff.archives.create-related',
            $user->roles->contains('name', 'intern') => 'intern.archives.create-related',
            default => 'intern.archives.create-related'
        };

        return view($viewPath, compact('parentArchive'));
    }

    // Store related archive
    public function storeRelated(Request $request, Archive $parentArchive)
    {
        Log::info('RelatedArchivesController@storeRelated called', [
            'parent_archive_id' => $parentArchive->id,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'index_number' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'kurun_waktu_start' => 'required|date',
            'tingkat_perkembangan' => 'required|string',
            'jumlah_berkas' => 'required|integer|min:1',
            'skkad' => 'required|string',
            'ket' => 'nullable|string',
        ]);

        // Calculate transition dates based on parent archive retention
        $kurunWaktuStart = \Carbon\Carbon::parse($validated['kurun_waktu_start']);
        $transitionActiveDue = $kurunWaktuStart->copy()->addYears($parentArchive->retention_aktif);
        $transitionInactiveDue = $transitionActiveDue->copy()->addYears($parentArchive->retention_inaktif);

        // Calculate status based on retention dates and nasib akhir
        $now = \Carbon\Carbon::now();
        $status = 'Aktif';

        if ($now->gt($transitionInactiveDue)) {
            // Both active and inactive periods have passed
            // Check if this is LAINNYA category (manual nasib_akhir)
            if ($parentArchive->category && $parentArchive->category->nama_kategori === 'LAINNYA') {
                // Use manual_nasib_akhir from parent archive for LAINNYA category
                $status = match (true) {
                    str_starts_with($parentArchive->manual_nasib_akhir, 'Musnah') => 'Musnah',
                    $parentArchive->manual_nasib_akhir === 'Permanen' => 'Permanen',
                    $parentArchive->manual_nasib_akhir === 'Dinilai Kembali' => 'Dinilai Kembali',
                    default => 'Permanen'
                };
            } else {
                // Use classification nasib_akhir for JRA categories
                $status = match (true) {
                    str_starts_with($parentArchive->classification->nasib_akhir, 'Musnah') => 'Musnah',
                    $parentArchive->classification->nasib_akhir === 'Permanen' => 'Permanen',
                    $parentArchive->classification->nasib_akhir === 'Dinilai Kembali' => 'Dinilai Kembali',
                    default => 'Permanen'
                };
            }
        } elseif ($now->gt($transitionActiveDue)) {
            // Only active period has passed
            $status = 'Inaktif';
        }

        // Get nasib akhir from parent archive (for LAINNYA category, it's stored in manual_nasib_akhir field)
        $nasibAkhir = null;

        // Check if parent archive has manual nasib_akhir (for LAINNYA category)
        if ($parentArchive->manual_nasib_akhir) {
            $nasibAkhir = $parentArchive->manual_nasib_akhir;
        } elseif ($parentArchive->classification && $parentArchive->classification->nasib_akhir) {
            // Use classification nasib_akhir for JRA categories
            $nasibAkhir = $parentArchive->classification->nasib_akhir;
        }

        // Check for duplicate archives with same attributes (including year)
        // For related archives, we only check for exact duplicates (same year)
        $duplicateArchive = Archive::where('category_id', $parentArchive->category_id)
            ->where('classification_id', $parentArchive->classification_id)
            ->where('lampiran_surat', $parentArchive->lampiran_surat)
            ->where('kurun_waktu_start', $validated['kurun_waktu_start'])
            ->where('index_number', $validated['index_number']) // Also check index number
            ->first();

        if ($duplicateArchive) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Arsip dengan kategori/klasifikasi/lampiran/tahun/nomor yang sama sudah ada. Arsip: ' . $duplicateArchive->description);
        }

        // Find the actual parent (oldest archive with same attributes)
        $actualParent = Archive::where('category_id', $parentArchive->category_id)
            ->where('classification_id', $parentArchive->classification_id)
            ->where('lampiran_surat', $parentArchive->lampiran_surat)
            ->orderBy('kurun_waktu_start', 'asc')
            ->first();

        // If the new archive is older than the current parent, update parent relationships
        if ($kurunWaktuStart->lt($actualParent->kurun_waktu_start)) {
            // Update existing parent to not be parent
            $actualParent->update(['is_parent' => false, 'parent_archive_id' => null]);

            // Create new archive as parent
            $newArchive = Archive::create([
                'category_id' => $parentArchive->category_id,
                'classification_id' => $parentArchive->classification_id,
                'lampiran_surat' => $parentArchive->lampiran_surat,
                'parent_archive_id' => null,
                'is_parent' => true,
                'index_number' => $validated['index_number'],
                'description' => $validated['description'],
                'kurun_waktu_start' => $validated['kurun_waktu_start'],
                'tingkat_perkembangan' => $validated['tingkat_perkembangan'],
                'jumlah_berkas' => $validated['jumlah_berkas'],
                'skkad' => $validated['skkad'],
                'ket' => $validated['ket'],
                'retention_aktif' => $parentArchive->retention_aktif,
                'retention_inaktif' => $parentArchive->retention_inaktif,
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'status' => $status,
                'manual_nasib_akhir' => $nasibAkhir,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Update all related archives to point to new parent
            Archive::where('category_id', $parentArchive->category_id)
                ->where('classification_id', $parentArchive->classification_id)
                ->where('lampiran_surat', $parentArchive->lampiran_surat)
                ->where('id', '!=', $newArchive->id)
                ->update(['parent_archive_id' => $newArchive->id]);
        } else {
            // Create new archive as child of existing parent
            $newArchive = Archive::create([
                'category_id' => $parentArchive->category_id,
                'classification_id' => $parentArchive->classification_id,
                'lampiran_surat' => $parentArchive->lampiran_surat,
                'parent_archive_id' => $actualParent->id,
                'is_parent' => false,
                'index_number' => $validated['index_number'],
                'description' => $validated['description'],
                'kurun_waktu_start' => $validated['kurun_waktu_start'],
                'tingkat_perkembangan' => $validated['tingkat_perkembangan'],
                'jumlah_berkas' => $validated['jumlah_berkas'],
                'skkad' => $validated['skkad'],
                'ket' => $validated['ket'],
                'retention_aktif' => $parentArchive->retention_aktif,
                'retention_inaktif' => $parentArchive->retention_inaktif,
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'status' => $status,
                'manual_nasib_akhir' => $nasibAkhir,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        Log::info('Related archive created successfully', [
            'new_archive_id' => $newArchive->id,
            'parent_archive_id' => $parentArchive->id
        ]);

        // Simple success message without HTML
        $successMessage = "Arsip terkait berhasil dibuat! Silakan klik tombol Tambah Lagi jika ingin menambahkan arsip terkait lainnya.";

        // Determine the correct route based on user role
        $user = Auth::user();
        $redirectRoute = match (true) {
            $user->roles->contains('name', 'admin') => 'admin.archives.related',
            $user->roles->contains('name', 'staff') => 'staff.archives.related',
            $user->roles->contains('name', 'intern') => 'intern.archives.related',
            default => 'staff.archives.related'
        };

        return redirect()->route($redirectRoute, $parentArchive)
            ->with([
                'success' => $successMessage,
                'show_add_related_button' => true,
                'parent_archive_id' => $parentArchive->id,
                'new_archive_details' => [
                    'index_number' => $newArchive->index_number,
                    'description' => $newArchive->description,
                    'year' => $newArchive->kurun_waktu_start->format('Y'),
                    'status' => $newArchive->status
                ]
            ]);
    }

    /**
     * Bulk update location for related archives
     */
    public function bulkUpdateLocation(Request $request)
    {
        try {
            $request->validate([
                'archive_ids' => 'required|array',
                'archive_ids.*' => 'exists:archives,id',
                'rack_number' => 'required|integer|min:1',
                'row_number' => 'required|integer|min:1',
                'box_number' => 'required|integer|min:1',
                'auto_generate_boxes' => 'boolean'
            ]);

            $archiveIds = $request->archive_ids;
            $rackNumber = $request->rack_number;
            $rowNumber = $request->row_number;
            $boxNumber = $request->box_number;
            $autoGenerateBoxes = $request->auto_generate_boxes ?? false;

            Log::info('Bulk update location started', [
                'archive_count' => count($archiveIds),
                'rack_number' => $rackNumber,
                'box_number' => $boxNumber,
                'auto_generate_boxes' => $autoGenerateBoxes
            ]);

            // Get all selected archives (including those that already have location)
            $archives = Archive::whereIn('id', $archiveIds)
                ->orderBy('kurun_waktu_start')
                ->get();

            if ($archives->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada arsip yang dipilih'
                ], 400);
            }

            // Check for archives that already have the same location
            $rackName = StorageRack::find($rackNumber)->name ?? "Rak {$rackNumber}";
            $sameLocationArchives = [];
            $archivesToUpdate = [];

            foreach ($archives as $archive) {
                // Check if archive already has the same location
                if (
                    $archive->rack_number == $rackNumber &&
                    $archive->row_number == $rowNumber &&
                    $archive->box_number == $boxNumber
                ) {
                    $sameLocationArchives[] = [
                        'id' => $archive->id,
                        'description' => $archive->description,
                        'current_location' => "{$rackName}, Baris {$archive->row_number}, Box {$archive->box_number}"
                    ];
                } else {
                    $archivesToUpdate[] = $archive;
                }
            }

            // If all archives are already in the same location
            if (count($sameLocationArchives) === count($archives)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semua arsip yang dipilih sudah berada di lokasi yang sama',
                    'same_location_count' => count($sameLocationArchives),
                    'same_location_error' => true
                ], 400);
            }

            // If some archives are in the same location, log warning but continue with others
            if (count($sameLocationArchives) > 0) {
                Log::warning('Some archives already in same location', [
                    'same_location_count' => count($sameLocationArchives),
                    'archives_to_update' => count($archivesToUpdate)
                ]);
            }

            $updatedCount = 0;
            $currentBoxNumber = $boxNumber;
            $currentFileNumber = 1;

            // Store original archive data before updates to check for previous locations
            $originalArchiveData = [];
            foreach ($archivesToUpdate as $archive) {
                $originalArchiveData[$archive->id] = [
                    'rack_number' => $archive->rack_number,
                    'box_number' => $archive->box_number,
                    'row_number' => $archive->row_number
                ];
            }

            foreach ($archivesToUpdate as $archive) {
                // Decrement old storage box count if archive had previous location
                if ($archive->rack_number && $archive->box_number) {
                    $oldStorageBox = StorageBox::where('rack_id', $archive->rack_number)
                        ->where('box_number', $archive->box_number)
                        ->first();

                    if ($oldStorageBox) {
                        $oldStorageBox->decrement('archive_count');
                        $oldStorageBox->updateStatus();
                    }
                }

                // Get real-time count and capacity for the current box from StorageBox
                $storageBoxForCheck = StorageBox::where('rack_id', $rackNumber)
                    ->where('box_number', $currentBoxNumber)
                    ->first();

                $boxCapacity = $storageBoxForCheck?->capacity ?? 50;
                $currentBoxArchives = Archive::where('rack_number', $rackNumber)
                    ->where('box_number', $currentBoxNumber)
                    ->count();

                // If current box is full and auto-generate is enabled, move to next available box automatically
                if ($currentBoxArchives >= $boxCapacity && $autoGenerateBoxes) {
                    $currentBoxNumber++;
                    $currentFileNumber = 1;

                    // Re-check capacity for the new box
                    $storageBoxForCheck = StorageBox::where('rack_id', $rackNumber)
                        ->where('box_number', $currentBoxNumber)
                        ->first();
                    $boxCapacity = $storageBoxForCheck?->capacity ?? 50;
                    $currentBoxArchives = Archive::where('rack_number', $rackNumber)
                        ->where('box_number', $currentBoxNumber)
                        ->count();
                }

                // If still full (auto off or next box also full), return error
                if ($currentBoxArchives >= $boxCapacity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Box {$currentBoxNumber} sudah penuh ({$currentBoxArchives}/{$boxCapacity}). Pilih box lain yang tersedia.",
                        'box_full' => true,
                        'box_number' => $currentBoxNumber,
                        'current_count' => $currentBoxArchives,
                        'capacity' => $boxCapacity
                    ], 400);
                }

                // Get next file number based on CLASSIFICATION and YEAR (resets to 1 for different classification/year)
                $fileNumber = Archive::getNextFileNumberForClassification(
                    $rackNumber,
                    $currentBoxNumber,
                    $archive->classification_id,
                    $archive->kurun_waktu_start->year
                );

                // Update archive location
                $archive->update([
                    'rack_number' => $rackNumber,
                    'row_number' => $rowNumber,
                    'box_number' => $currentBoxNumber,
                    'file_number' => $fileNumber,
                    'updated_by' => Auth::id()
                ]);

                $updatedCount++;

                // Update new storage box count (recalculate real-time after each placement)
                $storageBox = StorageBox::where('rack_id', $rackNumber)
                    ->where('box_number', $currentBoxNumber)
                    ->first();

                if ($storageBox) {
                    // Recompute count from archives to avoid drift, then update status
                    $realCount = Archive::where('rack_number', $rackNumber)
                        ->where('box_number', $currentBoxNumber)
                        ->count();
                    $storageBox->archive_count = $realCount;
                    $storageBox->save();
                    $storageBox->updateStatus();
                }
            }

            // Auto sync storage box counts after bulk update
            // Storage box count is automatically updated by the increment() method above
            // No need for manual command

            Log::info('Bulk update location completed', [
                'updated_count' => $updatedCount,
                'total_archives' => count($archiveIds),
                'same_location_count' => count($sameLocationArchives)
            ]);

            // Prepare response message based on whether archives had previous location
            $hasPreviousLocation = false;
            foreach ($archivesToUpdate as $archive) {
                // Check if archive had a previous location using stored original data
                if (isset($originalArchiveData[$archive->id])) {
                    $originalData = $originalArchiveData[$archive->id];
                    if ($originalData['rack_number'] && $originalData['box_number']) {
                        $hasPreviousLocation = true;
                        break;
                    }
                }
            }

            if ($hasPreviousLocation) {
                $message = "Lokasi berhasil diupdate untuk {$updatedCount} arsip";
            } else {
                $message = "Lokasi berhasil ditempatkan untuk {$updatedCount} arsip";
            }

            if (count($sameLocationArchives) > 0) {
                $message .= ". {" . count($sameLocationArchives) . "} arsip tidak dipindahkan karena sudah berada di lokasi yang sama";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'updated_count' => $updatedCount,
                'same_location_count' => count($sameLocationArchives),
                'same_location_archives' => $sameLocationArchives,
                'details' => [
                    'rack_name' => $rackName,
                    'row_number' => $rowNumber,
                    'box_number' => $boxNumber,
                    'total_selected' => count($archiveIds),
                    'successfully_updated' => $updatedCount,
                    'already_in_location' => count($sameLocationArchives)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk update location failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal update lokasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
