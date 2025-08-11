<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
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

            return view('admin.archives.related', compact('archive', 'relatedArchives'));
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
            'index_number' => 'required|integer|min:1',
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

        // Find the actual parent (oldest archive with same attributes)
        $actualParent = Archive::where('category_id', $parentArchive->category_id)
            ->where('classification_id', $parentArchive->classification_id)
            ->where('lampiran_surat', $parentArchive->lampiran_surat)
            ->orderBy('kurun_waktu_start')
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
                'status' => 'Aktif',
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
                'status' => 'Aktif',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        Log::info('Related archive created successfully', [
            'new_archive_id' => $archive->id,
            'parent_archive_id' => $parentArchive->id
        ]);

        return redirect()->route('admin.archives.show', $archive)
            ->with('success', 'Arsip terkait berhasil dibuat');
    }
}
