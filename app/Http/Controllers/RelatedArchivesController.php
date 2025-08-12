<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\StorageBox;
use App\Models\StorageRack;
use Illuminate\Http\Request;
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

                // Calculate available boxes using new formula
                $capacity = $rack->capacity_per_box;
                $n = $capacity;
                $halfN = $n / 2;

                $availableBoxes = $rack->boxes->filter(function ($box) use ($halfN) {
                    return $box->archive_count < $halfN;
                });

                $partiallyFullBoxes = $rack->boxes->filter(function ($box) use ($n, $halfN) {
                    return $box->archive_count >= $halfN && $box->archive_count < $n;
                });

                $fullBoxes = $rack->boxes->filter(function ($box) use ($n) {
                    return $box->archive_count >= $n;
                });

                // Set calculated counts
                $rack->available_boxes_count = $availableBoxes->count();
                $rack->partially_full_boxes_count = $partiallyFullBoxes->count();
                $rack->full_boxes_count = $fullBoxes->count();

                // Ensure boxes have all required data with real-time archive count
                foreach ($rack->boxes as $box) {
                    $box->row_number = $box->row ? $box->row->row_number : 0;
                    $box->box_number = $box->box_number;

                    // Get real-time archive count from actual archives
                    $realTimeArchiveCount = \App\Models\Archive::where('box_number', $box->box_number)->count();
                    $box->archive_count = $realTimeArchiveCount;

                    $box->capacity = $box->capacity;

                    // Calculate status using new formula with real-time count
                    if ($realTimeArchiveCount >= $n) {
                        $box->status = 'full';
                    } elseif ($realTimeArchiveCount >= $halfN) {
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

            return view('admin.archives.related', compact('archive', 'relatedArchives', 'racks'));
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

        return view('admin.archives.related-category', compact('archives'));
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

        return view('admin.archives.create-related', compact('parentArchive'));
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

        // Check for duplicate archives with same attributes
        $duplicateArchive = Archive::where('category_id', $parentArchive->category_id)
            ->where('classification_id', $parentArchive->classification_id)
            ->where('lampiran_surat', $parentArchive->lampiran_surat)
            ->where('kurun_waktu_start', $validated['kurun_waktu_start'])
            ->first();

        if ($duplicateArchive) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Arsip dengan kategori/klasifikasi/lampiran/tahun yang sama sudah ada. Arsip: ' . $duplicateArchive->description);
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
            $archive = Archive::create([
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
                ->where('id', '!=', $archive->id)
                ->update(['parent_archive_id' => $archive->id]);
        } else {
            // Create new archive as child of existing parent
            $archive = Archive::create([
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
            'new_archive_id' => $archive->id,
            'parent_archive_id' => $parentArchive->id
        ]);

        return redirect()->route('admin.archives.related', $parentArchive)
            ->with([
                'success' => 'Arsip terkait berhasil dibuat!',
                'show_add_related_button' => true,
                'parent_archive_id' => $parentArchive->id
            ]);
    }
}
