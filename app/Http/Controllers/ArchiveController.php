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
     * Generate automatic index number for JRA categories
     * User inputs: NOMOR_URUT/KODE_KOMPONEN (e.g., 001/SKPD)
     * System generates: KODE_KLASIFIKASI/NOMOR_URUT/KODE_KOMPONEN/TAHUN
     */
    private function generateAutoIndexNumber(Classification $classification, $userInput, $kurunWaktuStart)
    {
        $year = Carbon::parse($kurunWaktuStart)->year;

        // Validate and parse user input
        if (empty(trim($userInput))) {
            throw new \Exception('Nomor urut dan kode komponen harus diisi (format: 001/SKPD)');
        }

        $parts = explode('/', trim($userInput));
        if (count($parts) !== 2) {
            throw new \Exception('Format tidak valid. Gunakan format: NOMOR_URUT/KODE_KOMPONEN (contoh: 001/SKPD)');
        }

        $nomorUrut = trim($parts[0]);
        $kodeKomponen = trim($parts[1]);

        // Validate nomor urut is numeric
        if (!is_numeric($nomorUrut)) {
            throw new \Exception('Nomor urut harus berupa angka (contoh: 001)');
        }

        if (empty($kodeKomponen)) {
            throw new \Exception('Kode komponen tidak boleh kosong (contoh: SKPD)');
        }

        // Pad nomor urut to 3 digits
        $nomorUrut = str_pad(intval($nomorUrut), 3, '0', STR_PAD_LEFT);

        // Format: KODE_KLASIFIKASI/NOMOR_URUT/KODE_KOMPONEN/TAHUN
        // Example: 01.02/001/SKPD/2024
        return sprintf('%s/%s/%s/%d', $classification->code, $nomorUrut, $kodeKomponen, $year);
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
                str_starts_with($archive->classification->nasib_akhir, 'Musnah') => 'Musnah',
                $archive->classification->nasib_akhir === 'Permanen' => 'Permanen',
                $archive->classification->nasib_akhir === 'Dinilai Kembali' => 'Dinilai Kembali',
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

            // Check if this is manual input (LAINNYA category)
            $isManualInput = $validated['is_manual_input'] ?? false;

            // Handle index number based on input type
            if ($isManualInput) {
                // Use manual index number for LAINNYA category (full format)
                $indexNumber = $validated['index_number'];
            } else {
                // For JRA categories: User inputs NOMOR_URUT/KODE_KOMPONEN, system adds classification code & year
                $userInput = $validated['index_number'];
                $indexNumber = $this->generateAutoIndexNumber($classification, $userInput, $validated['kurun_waktu_start']);
            }

            // Handle retention values
            $retentionAktif = $isManualInput ?
                (int)($validated['manual_retention_aktif'] ?? 0) :
                (int)$classification->retention_aktif;

            $retentionInaktif = $isManualInput ?
                (int)($validated['manual_retention_inaktif'] ?? 0) :
                (int)$classification->retention_inaktif;

            // Calculate transition dates
            $kurunWaktuStart = Carbon::parse($validated['kurun_waktu_start']);
            $transitionActiveDue = $kurunWaktuStart->copy()->addYears($retentionAktif);
            $transitionInactiveDue = $transitionActiveDue->copy()->addYears($retentionInaktif);

            // Prepare archive data
            $archiveData = array_merge($validated, [
                'category_id' => $category->id,
                'index_number' => $indexNumber,
                'retention_aktif' => $retentionAktif,
                'retention_inaktif' => $retentionInaktif,
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'status' => 'Aktif', // Initial status
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Create the archive
            $archive = Archive::create($archiveData);

            // Load classification relationship for status calculation
            $archive->load('classification');
            $finalStatus = $this->calculateAndSetStatus($archive);

            $user = Auth::user();
            $redirectRoute = $user->hasRole('admin') ? 'admin.archives.index' : ($user->hasRole('staff') ? 'staff.archives.index' : 'intern.archives.index');

            $inputType = $isManualInput ? 'manual' : 'otomatis';
            return redirect()->route($redirectRoute)->with([
                'success' => "✅ Berhasil menyimpan arsip dengan status {$finalStatus}!",
                'new_archive_id' => $archive->id,
                'show_location_options' => true
            ]);
        } catch (Throwable $e) {
            Log::error('Archive creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);
            return redirect()->back()->withInput()->with('error', '❌ Gagal membuat arsip: ' . $e->getMessage() . '. Silakan periksa data dan coba lagi.');
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

        // Permission check: Admin and Staff can edit any archive, Intern can only edit their own
        if (!$user->hasRole('admin') && !$user->hasRole('staff') && !$user->hasRole('intern')) {
            abort(403, 'Access denied. You do not have permission to edit archives.');
        }

        // If user is intern, they can only edit archives they created
        if ($user->hasRole('intern') && $archive->created_by !== $user->id) {
            abort(403, 'Access denied. You can only edit archives that you created.');
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
        $user = Auth::user();

        // Permission check: Admin and Staff can update any archive, Intern can only update their own
        if (!$user->hasRole('admin') && !$user->hasRole('staff') && !$user->hasRole('intern')) {
            abort(403, 'Access denied. You do not have permission to update archives.');
        }

        // If user is intern, they can only update archives they created
        if ($user->hasRole('intern') && $archive->created_by !== $user->id) {
            abort(403, 'Access denied. You can only update archives that you created.');
        }

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
            $redirectRoute = $user->hasRole('admin') ? 'admin.archives.index' : ($user->hasRole('staff') ? 'staff.archives.index' : 'intern.archives.index');

            return redirect()->route($redirectRoute)->with('success', "✅ Berhasil mengubah arsip '{$archive->description}' dengan status {$finalStatus}!");
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', '❌ Gagal mengubah arsip. Silakan periksa data dan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Archive $archive)
    {
        $user = Auth::user();

        // Permission check: Only admin can delete archives
        if (!$user->hasRole('admin')) {
            abort(403, 'Access denied. Only administrators can delete archives.');
        }

        try {
            $archiveDescription = $archive->description;
            $archiveNumber = $archive->index_number;

            // Log the deletion for audit trail
            Log::info("Archive deleted: ID {$archive->id}, Description: {$archiveDescription}, Number: {$archiveNumber}, Deleted by user: " . Auth::id());

            $archive->delete();

            // Redirect to appropriate index page based on user role
            $redirectRoute = $user->hasRole('admin') ? 'admin.archives.index' : ($user->hasRole('staff') ? 'staff.archives.index' : 'intern.archives.index');

            return redirect()->route($redirectRoute)->with('success', "✅ Berhasil menghapus arsip '{$archiveDescription}' ({$archiveNumber})!");
        } catch (\Exception $e) {
            Log::error('Archive deletion error: ' . $e->getMessage());

            // Redirect to appropriate index page even on error
            $redirectRoute = $user->hasRole('admin') ? 'admin.archives.index' : ($user->hasRole('staff') ? 'staff.archives.index' : 'intern.archives.index');

            return redirect()->route($redirectRoute)->with('error', '❌ Gagal menghapus arsip. Silakan coba lagi.');
        }
    }

    /**
     * Export archives to Excel based on status
     */
    public function exportArchives($status = 'all', Request $request)
    {
        try {
            // Map status to proper format
            $mappedStatus = match ($status) {
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
        $user = Auth::user();
        $statuses = [
            'all' => 'Semua Status',
            'aktif' => 'Arsip Aktif',
            'inaktif' => 'Arsip Inaktif',
            'permanen' => 'Arsip Permanen',
            'musnah' => 'Arsip Musnah'
        ];

        // Count archives based on user role
        if ($user->hasRole('intern')) {
            // Intern can only see their own archives
            $archiveCounts = [
                'all' => Archive::where('created_by', $user->id)->count(),
                'aktif' => Archive::where('created_by', $user->id)->where('status', 'Aktif')->count(),
                'inaktif' => Archive::where('created_by', $user->id)->where('status', 'Inaktif')->count(),
                'permanen' => Archive::where('created_by', $user->id)->where('status', 'Permanen')->count(),
                'musnah' => Archive::where('created_by', $user->id)->where('status', 'Musnah')->count(),
            ];
        } else {
            // Admin and Staff can see all archives
            $archiveCounts = [
                'all' => Archive::count(),
                'aktif' => Archive::aktif()->count(),
                'inaktif' => Archive::inaktif()->count(),
                'permanen' => Archive::permanen()->count(),
                'musnah' => Archive::musnah()->count(),
            ];
        }

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
     * Show export form with filters
     */
    public function exportForm($status = 'all')
    {
        $statusTitle = $this->getStatusTitle($status);
        $user = Auth::user();

        // For intern, calculate total records they created with the specified status
        $totalRecords = 0;
        if ($user->hasRole('intern')) {
            $query = Archive::where('created_by', $user->id);

            if ($status !== 'all') {
                $query->where('status', ucfirst($status));
            }

            $totalRecords = $query->count();
        } else {
            // For admin/staff, get all records
            $query = Archive::query();

            if ($status !== 'all') {
                $query->where('status', ucfirst($status));
            }

            $totalRecords = $query->count();
        }

        $viewPath = $this->getViewPath('archives.export');
        return view($viewPath, compact('status', 'statusTitle', 'totalRecords'));
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
        $user = Auth::user();

        // Normalize status to proper case
        $status = match ($status) {
            'aktif' => 'Aktif',
            'inaktif' => 'Inaktif',
            'permanen' => 'Permanen',
            'musnah' => 'Musnah',
            'all' => 'all',
            default => $status
        };

        // Filter by user role
        if ($user->hasRole('intern')) {
            // Intern can only export their own archives
            $createdBy = $user->id;
        } elseif ($user->hasRole('staff')) {
            // Staff can export their own archives or all archives from staff/intern
            if ($createdBy === 'current_user' || $createdBy === $user->id) {
                $createdBy = $user->id;
            } else {
                // If no specific user selected, staff can see all staff/intern archives
                $createdBy = null;
            }
        } else {
            // Admin can export any archives
            if ($createdBy === 'current_user') {
                $createdBy = $user->id;
            }
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
                $userModel = \App\Models\User::find($createdBy);
                if ($userModel) {
                    $fileName .= '-' . strtolower(str_replace(' ', '-', $userModel->name));
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
        return match ($status) {
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
            $query->where(function ($q) use ($searchTerm) {
                $q->where('index_number', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhereHas('category', function ($catQuery) use ($searchTerm) {
                        $catQuery->where('nama_kategori', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('classification', function ($classQuery) use ($searchTerm) {
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

        // Grid location filters
        if ($request->filled('rack_filter')) {
            $query->where('rack_number', $request->get('rack_filter'));
        }

        if ($request->filled('row_filter')) {
            $query->where('row_number', $request->get('row_filter'));
        }

        if ($request->filled('box_filter')) {
            $query->where('box_number', $request->get('box_filter'));
        }

        if ($request->filled('file_filter')) {
            $query->where('file_number', $request->get('file_filter'));
        }

        // ✅ Tambahkan return di sini
        return $query;
    }


    /**
     * Get rows for a specific rack
     */
    public function getRackRows($rackId)
    {
        $rack = \App\Models\StorageRack::find($rackId);
        if (!$rack) {
            return response()->json([]);
        }

        $rows = [];
        for ($i = 1; $i <= $rack->total_rows; $i++) {
            $rows[] = ['row_number' => $i];
        }

        return response()->json($rows);
    }

    /**
     * Get boxes for a specific rack and row
     */
    public function getRackRowBoxes($rackId, $rowNumber)
    {
        $rack = \App\Models\StorageRack::find($rackId);
        if (!$rack) {
            return response()->json([]);
        }

        $boxes = \App\Models\StorageBox::where('rack_id', $rackId)
            ->where('row_number', $rowNumber)
            ->orderBy('box_number')
            ->get(['box_number', 'status', 'archive_count', 'capacity']);

        return response()->json($boxes);
    }



    /**
     * Check if user can create archives
     */
    private function canCreateArchive(): bool
    {
        $user = Auth::user();
        return $user->hasRole('admin') || $user->hasRole('staff') || $user->hasRole('intern');
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
            return \App\Models\User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['staff', 'intern']);
            })->orderBy('name')->get();
        } elseif ($user->hasRole('intern')) {
            // Intern can only see staff and intern users
            return \App\Models\User::whereHas('roles', function ($query) {
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
