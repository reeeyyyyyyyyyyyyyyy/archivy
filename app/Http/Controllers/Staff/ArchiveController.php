<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\UpdateArchiveStatusJob;
use Carbon\Carbon;

class ArchiveController extends Controller
{
    /**
     * Display a listing of archives (all)
     */
    public function index(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('index_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_filter')) {
            $query->where('category_id', $request->category_filter);
        }

        // Classification filter
        if ($request->filled('classification_filter')) {
            $query->where('classification_id', $request->classification_filter);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('kurun_waktu_start', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('kurun_waktu_start', '<=', $request->date_to);
        }

        // Created by filter
        if ($request->filled('created_by_filter')) {
            $query->where('created_by', $request->created_by_filter);
        }

        $archives = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 50));

        $categories = Category::all();
        $classifications = Classification::all();
        $users = User::all();

        return view('staff.archives.index', compact('archives', 'categories', 'classifications', 'users'))
            ->with('title', 'Semua Arsip')
            ->with('showAddButton', true);
    }

    /**
     * Display active archives
     */
    public function aktif(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser'])
            ->where('status', 'Aktif');

        // Apply filters
        $this->applyFilters($query, $request);

        $archives = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        $categories = Category::all();
        $classifications = Classification::all();
        $users = User::all();

        return view('staff.archives.index', compact('archives', 'categories', 'classifications', 'users'))
            ->with('title', 'Arsip Aktif')
            ->with('showAddButton', false);
    }

    /**
     * Display inactive archives
     */
    public function inaktif(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser'])
            ->where('status', 'Inaktif');

        // Apply filters
        $this->applyFilters($query, $request);

        $archives = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        $categories = Category::all();
        $classifications = Classification::all();
        $users = User::all();

        return view('staff.archives.index', compact('archives', 'categories', 'classifications', 'users'))
            ->with('title', 'Arsip Inaktif')
            ->with('showAddButton', false);
    }

    /**
     * Display permanent archives
     */
    public function permanen(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser'])
            ->where('status', 'Permanen');

        // Apply filters
        $this->applyFilters($query, $request);

        $archives = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        $categories = Category::all();
        $classifications = Classification::all();
        $users = User::all();

        return view('staff.archives.index', compact('archives', 'categories', 'classifications', 'users'))
            ->with('title', 'Arsip Permanen')
            ->with('showAddButton', false);
    }

    /**
     * Display destroyed archives
     */
    public function musnah(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser'])
            ->where('status', 'Musnah');

        // Apply filters
        $this->applyFilters($query, $request);

        $archives = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        $categories = Category::all();
        $classifications = Classification::all();
        $users = User::all();

        return view('staff.archives.index', compact('archives', 'categories', 'classifications', 'users'))
            ->with('title', 'Arsip Musnah')
            ->with('showAddButton', false);
    }

    /**
     * Show the form for creating a new archive
     */
    public function create()
    {
        $categories = Category::all();
        $classifications = Classification::all();

        return view('staff.archives.create', compact('categories', 'classifications'));
    }

    /**
     * Store a newly created archive
     */
    public function store(Request $request)
    {
        $request->validate([
            'index_number' => 'required|string|max:255|unique:archives',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'classification_id' => 'required|exists:classifications,id',
            'kurun_waktu_start' => 'required|date',
            'kurun_waktu_end' => 'nullable|date|after_or_equal:kurun_waktu_start',
            'tingkat_perkembangan' => 'required|string',
            'jumlah_berkas' => 'required|integer|min:1',
            'skkad' => 'required|string',
            're_evaluation' => 'boolean',
        ]);

        try {
            // Get classification and category
            $classification = Classification::with('category')->findOrFail($request->classification_id);
            $category = $classification->category;

            // Calculate retention values from classification
            $retentionAktif = (int)$classification->retention_aktif;
            $retentionInaktif = (int)$classification->retention_inaktif;

            // Calculate transition dates
            $kurunWaktuStart = \Carbon\Carbon::parse($request->kurun_waktu_start);
            $transitionActiveDue = $kurunWaktuStart->copy()->addYears($retentionAktif);
            $transitionInactiveDue = $transitionActiveDue->copy()->addYears($retentionInaktif);

            $archive = Archive::create([
                'index_number' => $request->index_number,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'classification_id' => $request->classification_id,
                'kurun_waktu_start' => $request->kurun_waktu_start,
                'kurun_waktu_end' => $request->kurun_waktu_end,
                'tingkat_perkembangan' => $request->tingkat_perkembangan,
                'jumlah_berkas' => $request->jumlah_berkas,
                'skkad' => $request->skkad,
                're_evaluation' => $request->has('re_evaluation'),
                'retention_aktif' => $retentionAktif,
                'retention_inaktif' => $retentionInaktif,
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'status' => 'Aktif', // Initial status
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Calculate and set status
            $this->calculateAndSetStatus($archive);

            // Dispatch job to update status
            UpdateArchiveStatusJob::dispatchSync();

            return redirect()->route('staff.archives.index')
                ->with('success', 'Arsip berhasil ditambahkan dengan status ' . $archive->status);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan arsip: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified archive
     */
    public function show(Archive $archive)
    {
        $archive->load(['category', 'classification', 'createdByUser', 'updatedByUser']);

        return view('staff.archives.show', compact('archive'));
    }

    /**
     * Show the form for editing the specified archive
     */
    public function edit(Archive $archive)
    {
        $categories = Category::all();
        $classifications = Classification::all();

        return view('staff.archives.edit', compact('archive', 'categories', 'classifications'));
    }

    /**
     * Update the specified archive
     */
    public function update(Request $request, Archive $archive)
    {
        // Check if user can edit this archive
        $user = Auth::user();
        if ($user->role_type !== 'admin' && $archive->created_by !== $user->id) {
            return redirect()->route('staff.archives.index')
                ->with('error', 'Anda tidak memiliki izin untuk mengedit arsip ini.');
        }

        $validated = $request->validate([
            'index_number' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'classification_id' => 'required|exists:classifications,id',
            'kurun_waktu_start' => 'required|date',
            'kurun_waktu_end' => 'nullable|date|after_or_equal:kurun_waktu_start',
            'tingkat_perkembangan' => 'required|string',
            // 'media_type' => 'required|string',
            'evaluation_notes' => 'nullable|string',
        ]);

        // Set the index_number based on status and classification
        if ($archive->status == 'Dinilai Kembali') {
            $validated['index_number'] = $archive->index_number; // Keep original for Dinilai Kembali
        } elseif ($archive->classification->code == 'LAINNYA') {
            $validated['index_number'] = $request->index_number; // Manual input for LAINNYA
        } else {
            $validated['index_number'] = $request->index_number; // Will be formatted by accessor
        }

        $archive->update($validated);
        $archive->update(['updated_by' => $user->id]);

        // Recalculate status
        $this->calculateAndSetStatus($archive);

        return redirect()->route('staff.archives.index')
            ->with('success', 'Arsip berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Archive $archive)
    {
        $user = Auth::user();

        // Check if user can delete this archive
        if ($user->role_type !== 'admin' && $archive->created_by !== $user->id) {
            return redirect()->route('staff.archives.index')
                ->with('error', 'Anda tidak memiliki izin untuk menghapus arsip ini.');
        }

        try {
            $archive->delete();
            return redirect()->route('staff.archives.index')
                ->with('success', 'Arsip berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('staff.archives.index')
                ->with('error', 'Gagal menghapus arsip: ' . $e->getMessage());
        }
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('index_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_filter')) {
            $query->where('category_id', $request->category_filter);
        }

        // Classification filter
        if ($request->filled('classification_filter')) {
            $query->where('classification_id', $request->classification_filter);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('kurun_waktu_start', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('kurun_waktu_start', '<=', $request->date_to);
        }

        // Created by filter
        if ($request->filled('created_by_filter')) {
            $query->where('created_by', $request->created_by_filter);
        }
    }

    /**
     * Calculate and set archive status
     */
    private function calculateAndSetStatus(Archive $archive)
    {
        $today = today();
        $status = 'Aktif'; // Default

        if ($archive->transition_inactive_due <= $today) {
            // Both active and inactive periods have passed
            // Check if this is LAINNYA category (manual nasib_akhir)
            if ($archive->classification->code === 'LAINNYA') {
                // Use manual nasib_akhir from archive
                $status = match (true) {
                    str_starts_with($archive->manual_nasib_akhir, 'Musnah') => 'Musnah',
                    $archive->manual_nasib_akhir === 'Permanen' => 'Permanen',
                    $archive->manual_nasib_akhir === 'Dinilai Kembali' => 'Dinilai Kembali',
                    default => 'Permanen'
                };
            } else {
                // Use classification nasib_akhir for JRA categories
                $status = match (true) {
                    str_starts_with($archive->classification->nasib_akhir, 'Musnah') => 'Musnah',
                    $archive->classification->nasib_akhir === 'Permanen' => 'Permanen',
                    $archive->classification->nasib_akhir === 'Dinilai Kembali' => 'Dinilai Kembali',
                    default => 'Permanen'
                };
            }
        } elseif ($archive->transition_active_due <= $today) {
            // Only active period has passed
            $status = 'Inaktif';
        }

        $archive->update(['status' => $status]);
        return $status;
    }
}
