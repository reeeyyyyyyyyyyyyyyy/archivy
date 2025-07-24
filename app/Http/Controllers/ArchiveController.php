<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Http\Requests\StoreArchiveRequest;
use App\Http\Requests\UpdateArchiveRequest;
use App\Jobs\UpdateArchiveStatusJob;
use App\Exports\ArchiveExportWithHeader;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ArchiveController extends Controller
{
    /**
     * Display all archives (main archive page with add button)
     */
    public function index(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $archives = $query->latest()->paginate($request->get('per_page', 15));

        $title = 'Semua Arsip';
        $showAddButton = $this->canCreateArchive();

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = $this->getFilterUsers();

        $viewPath = $this->getViewPath('archives.index');
        return view($viewPath, compact('archives', 'title', 'showAddButton', 'categories', 'classifications', 'users'));
    }

    /**
     * Display active archives only
     */
    public function aktif(Request $request)
    {
        $query = Archive::aktif()->with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $archives = $query->latest()->paginate($request->get('per_page', 15));

        $title = 'Arsip Aktif';
        $showAddButton = false;

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = $this->getFilterUsers();

        $viewPath = $this->getViewPath('archives.index');
        return view($viewPath, compact('archives', 'title', 'showAddButton', 'categories', 'classifications', 'users'));
    }

    /**
     * Display inactive archives only
     */
    public function inaktif(Request $request)
    {
        $query = Archive::inaktif()->with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $archives = $query->latest()->paginate($request->get('per_page', 15));

        $title = 'Arsip Inaktif';
        $showAddButton = false;

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = $this->getFilterUsers();

        $viewPath = $this->getViewPath('archives.index');
        return view($viewPath, compact('archives', 'title', 'showAddButton', 'categories', 'classifications', 'users'));
    }

    /**
     * Display permanent archives only
     */
    public function permanen(Request $request)
    {
        $query = Archive::permanen()->with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $archives = $query->latest()->paginate($request->get('per_page', 15));

        $title = 'Arsip Permanen';
        $showAddButton = false;

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = $this->getFilterUsers();

        $viewPath = $this->getViewPath('archives.index');
        return view($viewPath, compact('archives', 'title', 'showAddButton', 'categories', 'classifications', 'users'));
    }

    /**
     * Display destroyed archives only
     */
    public function musnah(Request $request)
    {
        $query = Archive::musnah()->with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $archives = $query->latest()->paginate($request->get('per_page', 15));

        $title = 'Arsip Musnah';
        $showAddButton = false;
        $showStatusActions = true; // Allow status changes from musnah page

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = $this->getFilterUsers();

        $viewPath = $this->getViewPath('archives.index');
        return view($viewPath, compact('archives', 'title', 'showAddButton', 'showStatusActions', 'categories', 'classifications', 'users'));
    }

    /**
     * Change archive status via AJAX
     */
    public function changeStatus(Request $request)
    {
        $request->validate([
            'archive_id' => 'required|exists:archives,id',
            'status' => 'required|in:Aktif,Inaktif,Permanen,Musnah'
        ]);

        try {
            $archive = Archive::findOrFail($request->archive_id);
            $oldStatus = $archive->status;

            $archive->update([
                'status' => $request->status,
                'manual_status_override' => true,
                'manual_override_at' => now(),
                'manual_override_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            Log::info("Status change: Archive ID {$archive->id} changed from {$oldStatus} to {$request->status} by user " . Auth::id());

            return response()->json([
                'success' => true,
                'message' => "Status arsip berhasil diubah menjadi {$request->status}",
                'archive_id' => $archive->id,
                'new_status' => $request->status
            ]);

        } catch (\Exception $e) {
            Log::error('Status change error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get classification details for AJAX
     */
    public function getClassificationDetails(Classification $classification)
    {
        $classification->load('category');
        return response()->json($classification);
    }

    /**
     * Get classifications by category for AJAX
     */
    public function getClassificationsByCategory(Request $request)
    {
        $classifications = Classification::query()
            ->where('category_id', $request->query('category_id'))
            ->with('category')
            ->get();
        return response()->json($classifications);
    }

    /**
     * Generate automatic index number with better readability
     */
    private function generateIndexNumber(Classification $classification, $kurunWaktuStart)
    {
        $year = Carbon::parse($kurunWaktuStart)->year;

        // Get current year's archive count for sequential numbering
        $currentYearCount = Archive::whereYear('kurun_waktu_start', $year)->count();
        $nextSequence = $currentYearCount + 1;

        // Format: ARK/YYYY/KODE-KLASIFIKASI/NNNN
        // Example: ARK/2024/01.02/0001
        return sprintf('ARK/%d/%s/%04d', $year, $classification->code, $nextSequence);
    }

    /**
     * Calculate and update archive status immediately
     */
    private function calculateAndSetStatus(Archive $archive)
    {
        $today = today();
        $status = 'Aktif'; // Default

        if ($archive->transition_inactive_due <= $today) {
            // Both active and inactive periods have passed
            $status = match (true) {
                str_starts_with($archive->category->nasib_akhir, 'Musnah') => 'Musnah',
                $archive->category->nasib_akhir === 'Permanen' => 'Permanen',
                $archive->category->nasib_akhir === 'Dinilai Kembali' => 'Permanen',
                default => 'Permanen'
            };
        } elseif ($archive->transition_active_due <= $today) {
            // Only active period has passed
            $status = 'Inaktif';
        }

        $archive->update(['status' => $status]);
        return $status;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->canCreateArchive()) {
            abort(403, 'Access denied. You do not have permission to create archives.');
        }

        $categories = Category::all();
        $classifications = Classification::with('category')->get();
        $viewPath = $this->getViewPath('archives.create');
        return view($viewPath, compact('categories', 'classifications'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArchiveRequest $request)
    {
        $validated = $request->validated();

        try {
            $classification = Classification::with('category')->findOrFail($validated['classification_id']);
            $category = $classification->category;

            $indexNumber = $this->generateIndexNumber($classification, $validated['kurun_waktu_start']);

            $kurunWaktuStart = Carbon::parse($validated['kurun_waktu_start']);
            $transitionActiveDue = $kurunWaktuStart->copy()->addYears($classification->retention_aktif);
            $transitionInactiveDue = $transitionActiveDue->copy()->addYears($classification->retention_inaktif);

            $archive = Archive::create(array_merge($validated, [
                'category_id' => $category->id,
                'index_number' => $indexNumber,
                'jumlah_berkas' => $validated['jumlah_berkas'] ?? 1,
                'retention_aktif' => $classification->retention_aktif,
                'retention_inaktif' => $classification->retention_inaktif,
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'status' => 'Aktif', // Initial status
                'created_by' => Auth::id() ?? 1,
                'updated_by' => Auth::id() ?? 1,
            ]));

            $archive->load(['category', 'classification']);
            $finalStatus = $this->calculateAndSetStatus($archive);

            $user = Auth::user();
            $redirectRoute = $user->hasRole('admin') ? 'admin.archives.index' :
                           ($user->hasRole('staff') ? 'staff.archives.index' : 'intern.archives.index');
            return redirect()->route($redirectRoute)->with('success', "Archive created successfully with status: {$finalStatus}");
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create archive: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Archive $archive)
    {
        $archive->load(['category', 'classification.category', 'createdByUser', 'updatedByUser']);
        $viewPath = $this->getViewPath('archives.show');
        return view($viewPath, compact('archive'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Archive $archive)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('staff')) {
            abort(403, 'Access denied. You do not have permission to edit archives.');
        }

        $categories = Category::all();
        $classifications = Classification::with('category')->get();
        $viewPath = $this->getViewPath('archives.edit');
        return view($viewPath, compact('archive', 'categories', 'classifications'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArchiveRequest $request, Archive $archive)
    {
        $validated = $request->validated();

        try {
            $classification = Classification::with('category')->findOrFail($validated['classification_id']);
            $category = $classification->category;

            $kurunWaktuStart = Carbon::parse($validated['kurun_waktu_start']);
            $transitionActiveDue = $kurunWaktuStart->copy()->addYears($classification->retention_aktif);
            $transitionInactiveDue = $transitionActiveDue->copy()->addYears($classification->retention_inaktif);

            $archive->update(array_merge($validated, [
                'category_id' => $category->id,
                'jumlah_berkas' => $validated['jumlah_berkas'] ?? 1,
                'retention_aktif' => $classification->retention_aktif,
                'retention_inaktif' => $classification->retention_inaktif,
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'updated_by' => Auth::id() ?? 1,
            ]));

            $archive->load(['category', 'classification']);
            $finalStatus = $this->calculateAndSetStatus($archive);

            $user = Auth::user();
            $redirectRoute = $user->hasRole('admin') ? 'admin.archives.index' :
                           ($user->hasRole('staff') ? 'staff.archives.index' : 'intern.archives.index');
            return redirect()->route($redirectRoute)->with('success', "Archive updated successfully with status: {$finalStatus}");
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update archive: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Archive $archive)
    {
        try {
            $archive->delete();
            return redirect()->route('admin.archives.index')->with('success', 'Arsip berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus arsip: ' . $e->getMessage());
        }
    }

    /**
     * Export archives to Excel based on status
     */
    public function exportArchives($status = 'all', Request $request)
    {
        try {
            // Map status to proper format
            $mappedStatus = match($status) {
                'aktif' => 'Aktif',
                'inaktif' => 'Inaktif',
                'permanen' => 'Permanen',
                'musnah' => 'Musnah',
                'all' => 'all',
                default => 'all'
            };

            $statusTitle = $this->getStatusTitle($mappedStatus);
            $fileName = 'daftar-arsip-' . strtolower(str_replace(' ', '-', $statusTitle)) . '-' . date('Y-m-d') . '.xlsx';

            return Excel::download(new ArchiveExportWithHeader($mappedStatus), $fileName);

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal mengeksport data: ' . $e->getMessage()]);
        }
    }

    /**
     * Show export menu with status selection
     */
    public function exportMenu()
    {
        $statuses = [
            'all' => 'Semua Status',
            'aktif' => 'Arsip Aktif',
            'inaktif' => 'Arsip Inaktif',
            'permanen' => 'Arsip Permanen',
            'musnah' => 'Arsip Musnah'
        ];

        $archiveCounts = [
            'all' => Archive::count(),
            'aktif' => Archive::aktif()->count(),
            'inaktif' => Archive::inaktif()->count(),
            'permanen' => Archive::permanen()->count(),
            'musnah' => Archive::musnah()->count(),
        ];

        $viewPath = $this->getViewPath('archives.export-menu');
        return view($viewPath, compact('statuses', 'archiveCounts'));
    }

    /**
     * Show export all form with comprehensive filters
     */
    public function exportAllForm()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = \App\Models\User::orderBy('name')->get();
        $statuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah'];

        return view('admin.archives.export-all', compact('categories', 'classifications', 'users', 'statuses'));
    }

    /**
     * Show export form for archives
     */
    public function exportForm($status = 'all')
    {
        $statusTitle = $this->getStatusTitle($status);

        $viewPath = $this->getViewPath('archives.export');
        return view($viewPath, compact('status', 'statusTitle'));
    }

    /**
     * Export archives to Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'status' => 'required|in:all,aktif,inaktif,permanen,musnah,Aktif,Inaktif,Permanen,Musnah',
            'year_from' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'year_to' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'created_by' => 'nullable|string|max:50'
        ]);

        $status = $request->status;
        $yearFrom = $request->year_from;
        $yearTo = $request->year_to;
        $createdBy = $request->created_by;

        // Handle "current_user" option
        if ($createdBy === 'current_user') {
            $createdBy = Auth::id();
        }

        // Validate range if both provided
        if ($yearFrom && $yearTo && $yearFrom > $yearTo) {
            return redirect()->back()->withErrors(['year_range' => 'Tahun "Dari" tidak boleh lebih besar dari tahun "Sampai"']);
        }

        $statusTitle = $this->getStatusTitle($status);
        $fileName = 'daftar-arsip-' . strtolower(str_replace(' ', '-', $statusTitle));

        // Add created by to filename
        if ($createdBy) {
            if ($createdBy == Auth::id()) {
                $fileName .= '-saya';
            } else {
                $user = \App\Models\User::find($createdBy);
                if ($user) {
                    $fileName .= '-' . strtolower(str_replace(' ', '-', $user->name));
                }
            }
        }

        // Add year range to filename
        if ($yearFrom && $yearTo) {
            if ($yearFrom == $yearTo) {
                $fileName .= '-' . $yearFrom;
            } else {
                $fileName .= '-' . $yearFrom . '-' . $yearTo;
            }
        } elseif ($yearFrom) {
            $fileName .= '-dari-' . $yearFrom;
        } elseif ($yearTo) {
            $fileName .= '-sampai-' . $yearTo;
        }

        $fileName .= '-' . date('Y-m-d') . '.xlsx';

        return Excel::download(new ArchiveExportWithHeader($status, $yearFrom, $yearTo, $createdBy), $fileName);
    }

    /**
     * Get status title for display
     */
    private function getStatusTitle($status): string
    {
        return match($status) {
            'aktif', 'Aktif' => 'Aktif',
            'inaktif', 'Inaktif' => 'Inaktif',
            'permanen', 'Permanen' => 'Permanen',
            'musnah', 'Musnah' => 'Usul Musnah',
            'all' => 'Semua Status',
            default => 'Semua Status'
        };
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters($query, Request $request)
    {
        // Search filter
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('index_number', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function($catQuery) use ($searchTerm) {
                      $catQuery->where('nama_kategori', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('classification', function($classQuery) use ($searchTerm) {
                      $classQuery->where('nama_klasifikasi', 'like', "%{$searchTerm}%")
                                 ->orWhere('code', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Category filter
        if ($request->filled('category_filter')) {
            $query->where('category_id', $request->get('category_filter'));
        }

        // Classification filter
        if ($request->filled('classification_filter')) {
            $query->where('classification_id', $request->get('classification_filter'));
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('kurun_waktu_start', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('kurun_waktu_start', '<=', $request->get('date_to'));
        }

        // Created by filter
        if ($request->filled('created_by_filter')) {
            $query->where('created_by', $request->get('created_by_filter'));
        }

        return $query;
    }

    /**
     * Determine if the current user can create a new archive.
     */
    private function canCreateArchive(): bool
    {
        $user = Auth::user();
        return $user->hasRole('admin') || $user->hasRole('staff');
    }

    /**
     * Get users for filter based on current user role
     */
    private function getFilterUsers()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            // Admin can see all users
            return \App\Models\User::orderBy('name')->get();
        } elseif ($user->hasRole('staff')) {
            // Staff can only see staff and intern users
            return \App\Models\User::whereHas('roles', function($query) {
                $query->whereIn('name', ['staff', 'intern']);
            })->orderBy('name')->get();
        } elseif ($user->hasRole('intern')) {
            // Intern can only see staff and intern users
            return \App\Models\User::whereHas('roles', function($query) {
                $query->whereIn('name', ['staff', 'intern']);
            })->orderBy('name')->get();
        }

        return \App\Models\User::orderBy('name')->get();
    }

    /**
     * Get the appropriate view path based on user role.
     */
    private function getViewPath(string $viewName): string
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return 'admin.' . $viewName;
        } elseif ($user->hasRole('staff')) {
            return 'staff.' . $viewName;
        } elseif ($user->hasRole('intern')) {
            return 'intern.' . $viewName;
        }

        return 'admin.' . $viewName; // Fallback to admin view
    }
}
