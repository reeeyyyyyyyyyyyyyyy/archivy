<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\StorageBox;
use App\Http\Requests\StoreArchiveRequest;
use App\Http\Requests\UpdateArchiveRequest;
use App\Jobs\UpdateArchiveStatusJob;
use App\Exports\ArchiveExportWithHeader;
use App\Exports\ArchiveAktifExport;
use App\Exports\ArchiveMusnahExport;
use App\Exports\ArchiveInaktifPermanenExport;
use App\Exports\ArchiveStatusExport;
use App\Services\TelegramService;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Models\User;
use App\Services\ArchiveAutomationService;


class ArchiveController extends Controller
{
    /**
     * Display all archives (main archive page with add button)
     */
    public function index(Request $request)
    {
        // Get all archives (not just latest)
        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser'])
            ->orderBy('kurun_waktu_start', 'desc');

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $archives = $query->latest()->paginate($request->get('per_page', 1000));

        $title = 'Semua Arsip';
        $showAddButton = $this->canCreateArchive();
        $showActionButtons = true; // Show action buttons for all archives

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = $this->getFilterUsers();

        $viewPath = $this->getViewPath('archives.index');
        return view($viewPath, compact('archives', 'title', 'showAddButton', 'showActionButtons', 'categories', 'classifications', 'users'));
    }

    /**
     * Display active archives only
     */
    public function aktif(Request $request)
    {
        $query = Archive::aktif()->with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $archives = $query->latest()->paginate($request->get('per_page', 1000));

        $title = 'Arsip Aktif';
        $showAddButton = false;
        $showActionButtons = true; // Show Edit, Show, and Delete buttons

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = $this->getFilterUsers();

        $viewPath = $this->getViewPath('archives.index');
        return view($viewPath, compact('archives', 'title', 'showAddButton', 'showActionButtons', 'categories', 'classifications', 'users'));
    }

    /**
     * Display inactive archives only
     */
    public function inaktif(Request $request)
    {
        $query = Archive::inaktif()->with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $archives = $query->latest()->paginate($request->get('per_page', 1000));

        $title = 'Arsip Inaktif';
        $showAddButton = false;
        $showActionButtons = true; // Show Edit, Show, and Delete buttons

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = $this->getFilterUsers();

        $viewPath = $this->getViewPath('archives.index');
        return view($viewPath, compact('archives', 'title', 'showAddButton', 'showActionButtons', 'categories', 'classifications', 'users'));
    }

    /**
     * Display permanent archives only
     */
    public function permanen(Request $request)
    {
        $query = Archive::permanen()->with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $archives = $query->latest()->paginate($request->get('per_page', 1000));

        $title = 'Arsip Permanen';
        $showAddButton = false;
        $showActionButtons = true; // Show Edit, Show, and Delete buttons

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = $this->getFilterUsers();

        $viewPath = $this->getViewPath('archives.index');
        return view($viewPath, compact('archives', 'title', 'showAddButton', 'showActionButtons', 'categories', 'classifications', 'users'));
    }

    /**
     * Display destroyed archives only
     */
    public function musnah(Request $request)
    {
        $query = Archive::musnah()->with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $archives = $query->latest()->paginate($request->get('per_page', 1000));

        $title = 'Arsip Musnah';
        $showAddButton = false;
        $showStatusActions = true; // Allow status changes from musnah page
        $showActionButtons = true; // Show Edit, Show, and Delete buttons

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();
        $users = $this->getFilterUsers();

        $viewPath = $this->getViewPath('archives.index');
        return view($viewPath, compact('archives', 'title', 'showAddButton', 'showStatusActions', 'showActionButtons', 'categories', 'classifications', 'users'));
    }

    /**
     * Display parent archives only (for related archives management)
     */
    public function parentArchives(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser'])
            ->where('is_parent', true)
            ->orderBy('kurun_waktu_start', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                    ->orWhere('index_number', 'like', "%{$searchTerm}%")
                    ->orWhere('lampiran_surat', 'like', "%{$searchTerm}%")
                    ->orWhereHas('category', function ($q) use ($searchTerm) {
                        $q->where('nama_kategori', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('classification', function ($q) use ($searchTerm) {
                        $q->where('nama_klasifikasi', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Category filter
        if ($request->filled('category_filter')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('nama_kategori', $request->category_filter);
            });
        }

        $archives = $query->paginate(25);

        $title = 'Arsip Induk (Per Masalah)';
        $showAddButton = $this->canCreateArchive();
        $showActionButtons = true; // Show action buttons for parent archives

        return view('admin.archives.parent-archives', compact('archives', 'title', 'showAddButton', 'showActionButtons'));
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
    // private function generateIndexNumber(Classification $classification, $kurunWaktuStart)
    // {
    //     $year = Carbon::parse($kurunWaktuStart)->year;

    //     // Get current year's archive count for sequential numbering
    //     $currentYearCount = Archive::whereYear('kurun_waktu_start', $year)->count();
    //     $nextSequence = $currentYearCount + 1;

    //     // Format: ARK/YYYY/KODE-KLASIFIKASI/NNNN
    //     // Example: ARK/2024/01.02/0001
    //     return sprintf('ARK/%d/%s/%04d', $year, $classification->code, $nextSequence);
    // }

    /**
     * Calculate and update archive status immediately
     */
    private function calculateAndSetStatus(Archive $archive)
    {
        $today = today();
        $status = 'Aktif'; // Default

        // Special handling for "Berkas Perseorangan"
        if ($archive->manual_nasib_akhir === 'Masuk ke Berkas Perseorangan') {
            // For "Berkas Perseorangan", follow retention logic but set final status to "Berkas Perseorangan"
            if ($archive->transition_inactive_due <= $today) {
                // Both active and inactive periods have passed
                $status = 'Berkas Perseorangan';
            } elseif ($archive->transition_active_due <= $today) {
                // Only active period has passed
                $status = 'Inaktif';
            }
            // If still in active period, keep as 'Aktif'
        } else {
            // Normal retention logic for other cases
            if ($archive->transition_inactive_due <= $today) {
                // Both active and inactive periods have passed

                // Check if this requires manual input (LAINNYA category OR hybrid cases)
                $requiresManualInput = $this->requiresManualInput($archive);

                if ($requiresManualInput) {
                    // Use manual_nasib_akhir from archive for manual classifications
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
        }

        $archive->update(['status' => $status]);
        return $status;
    }

    /**
     * Check if archive requires manual input based on category and retention values
     */
    private function requiresManualInput(Archive $archive): bool
    {
        // LAINNYA category always requires manual input
        if ($archive->category && $archive->category->nama_kategori === 'LAINNYA') {
            return true;
        }

        // Check if classification has any retention field = 0 (indicating manual input needed)
        if ($archive->classification) {
            $classification = $archive->classification;
            if ($classification->retention_aktif === 0 || $classification->retention_inaktif === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get retention values for archive (handles hybrid cases)
     */
    private function getRetentionValues(Archive $archive, $validated = null): array
    {
        $classification = $archive->classification;

        // Case 1: LAINNYA category - always manual
        if ($archive->category && $archive->category->nama_kategori === 'LAINNYA') {
            return [
                'retention_aktif' => (int)($validated['manual_retention_aktif'] ?? 0),
                'retention_inaktif' => (int)($validated['manual_retention_inaktif'] ?? 0),
                'nasib_akhir' => $validated['manual_nasib_akhir'] ?? 'Dinilai Kembali'
            ];
        }

        // Case 2: Hybrid cases - some fields manual, some from database
        $retentionAktif = $classification->retention_aktif;
        $retentionInaktif = $classification->retention_inaktif;
        $nasibAkhir = $classification->nasib_akhir;

        // If retention_aktif = 0, use manual input
        if ($retentionAktif === 0 && isset($validated['manual_retention_aktif'])) {
            $retentionAktif = (int)$validated['manual_retention_aktif'];
        }

        // If retention_inaktif = 0, use manual input
        if ($retentionInaktif === 0 && isset($validated['manual_retention_inaktif'])) {
            $retentionInaktif = (int)$validated['manual_retention_inaktif'];
        }

        // If nasib_akhir = 'Dinilai Kembali', use manual input
        if ($nasibAkhir === 'Dinilai Kembali' && isset($validated['manual_nasib_akhir'])) {
            $nasibAkhir = $validated['manual_nasib_akhir'];
        }

        return [
            'retention_aktif' => $retentionAktif,
            'retention_inaktif' => $retentionInaktif,
            'nasib_akhir' => $nasibAkhir
        ];
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
            // Check for duplicate archives
            $duplicateArchive = Archive::where('category_id', $validated['category_id'])
                ->where('classification_id', $validated['classification_id'])
                ->where('lampiran_surat', $validated['lampiran_surat'])
                ->first();

            if ($duplicateArchive) {
                return redirect()->back()
                    ->withInput()
                    ->with([
                        'duplicate_warning' => true,
                        'duplicate_archive_id' => $duplicateArchive->id,
                        'duplicate_archive_description' => $duplicateArchive->description,
                        'duplicate_archive_year' => $duplicateArchive->kurun_waktu_start->format('Y'),
                        'duplicate_archive_data' => $validated
                    ]);
            }

            $classification = Classification::with('category')->findOrFail($validated['classification_id']);
            $category = $classification->category;

            // Use manual index_number directly (no auto-generation)
            $indexNumber = $validated['index_number'];

            // Create temporary archive object for retention calculation
            $tempArchive = new Archive();
            $tempArchive->classification = $classification;
            $tempArchive->category = $category;

            // Get retention values (handles hybrid cases)
            $retentionValues = $this->getRetentionValues($tempArchive, $validated);
            $retentionAktif = $retentionValues['retention_aktif'];
            $retentionInaktif = $retentionValues['retention_inaktif'];

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
                'is_parent' => true, // First archive is parent
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Add manual fields if needed
            if ($this->requiresManualInput($tempArchive)) {
                $archiveData['is_manual_input'] = true;
                $archiveData['manual_retention_aktif'] = $validated['manual_retention_aktif'] ?? null;
                $archiveData['manual_retention_inaktif'] = $validated['manual_retention_inaktif'] ?? null;
                $archiveData['manual_nasib_akhir'] = $validated['manual_nasib_akhir'] ?? null;

                // Set status to "Berkas Perseorangan" if nasib akhir is "Masuk ke Berkas Perseorangan"
                if ($validated['manual_nasib_akhir'] === 'Masuk ke Berkas Perseorangan') {
                    $archiveData['status'] = 'Berkas Perseorangan';
                }
            }

            // Create the archive
            $archive = Archive::create($archiveData);

            // Load classification relationship for status calculation
            $archive->load('classification');

            // Only calculate status if not already set to "Berkas Perseorangan"
            if ($archive->status !== 'Berkas Perseorangan') {
                $finalStatus = $this->calculateAndSetStatus($archive);
            } else {
                $finalStatus = 'Berkas Perseorangan';
            }

            // Auto-process archive (year detection and sorting)
            $automationService = new ArchiveAutomationService();
            $automationService->autoProcessArchive($archive);

            $user = Auth::user();
            $redirectRoute = $user->role_type === 'admin' ? 'admin.archives.index' : ($user->role_type === 'staff' ? 'staff.archives.index' : 'intern.archives.index');

            return redirect()->route($redirectRoute)->with([
                'create_success' => "✅ Berhasil menyimpan arsip dengan status {$finalStatus}!",
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
        if ($user->role_type !== 'admin' && $user->role_type !== 'staff' && $user->role_type !== 'intern') {
            abort(403, 'Access denied. You do not have permission to edit archives.');
        }

        // If user is intern, they can only edit archives they created
        if ($user->role_type === 'intern' && $archive->created_by !== $user->id) {
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

        // Permission check: Admin and Staff can edit any archive, Intern can only edit their own
        if ($user->role_type !== 'admin' && $user->role_type !== 'staff' && $user->role_type !== 'intern') {
            abort(403, 'Access denied. You do not have permission to edit archives.');
        }

        // If user is intern, they can only edit archives they created
        if ($user->role_type === 'intern' && $archive->created_by !== $user->id) {
            abort(403, 'Access denied. You can only edit archives that you created.');
        }

        $validated = $request->validated();

        try {
            $classification = Classification::with('category')->findOrFail($validated['classification_id']);
            $category = $classification->category;

            // Handle index number based on input type
            $indexNumber = $validated['index_number'];

            // Handle retention values
            $isManualInput = $validated['is_manual_input'] ?? false;

            if ($isManualInput) {
                // Use manual retention values
                $retentionAktif = (int)($validated['manual_retention_aktif'] ?? 0);
                $retentionInaktif = (int)($validated['manual_retention_inaktif'] ?? 0);
            } else {
                // Use classification retention values
                $retentionAktif = (int)$classification->retention_aktif;
                $retentionInaktif = (int)$classification->retention_inaktif;
            }

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
                'updated_by' => Auth::id(),
            ]);

            // Update the archive
            $archive->update($archiveData);

            // Load classification relationship for status calculation
            $archive->load('classification');
            $finalStatus = $this->calculateAndSetStatus($archive);

            // Auto-process archive (year detection and sorting)
            $automationService = new ArchiveAutomationService();
            $automationService->autoProcessArchive($archive);


            $user = Auth::user();
            $redirectRoute = $user->hasRole('admin') ? 'admin.archives.index' : ($user->hasRole('staff') ? 'staff.archives.index' : 'intern.archives.index');

            return redirect()->route($redirectRoute)->with('success', "✅ Berhasil memperbarui arsip dengan status {$finalStatus}!");
        } catch (Throwable $e) {
            Log::error('Archive update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);
            return redirect()->back()->withInput()->with('error', '❌ Gagal memperbarui arsip: ' . $e->getMessage() . '. Silakan periksa data dan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Archive $archive)
    {
        $user = Auth::user();

        // Permission check: Staff and Intern can delete archives, Intern can only delete their own
        if ($user->role_type === 'intern' && $archive->created_by !== $user->id) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Access denied. You can only delete archives that you created.'], 403);
            }
            abort(403, 'Access denied. You can only delete archives that you created.');
        }

        try {
            $archiveDescription = $archive->description;
            $archiveNumber = $archive->index_number;

            // Log the deletion for audit trail
            Log::info("Archive deleted: ID {$archive->id}, Description: {$archiveDescription}, Number: {$archiveNumber}, Deleted by user: " . Auth::id());

            // Handle parent archive deletion
            if ($archive->is_parent) {
                // Find the oldest child to become the new parent
                $newParent = Archive::where('parent_archive_id', $archive->id)
                    ->orderBy('kurun_waktu_start', 'asc')
                    ->first();

                if ($newParent) {
                    // Update the new parent
                    $newParent->update([
                        'is_parent' => true,
                        'parent_archive_id' => null
                    ]);

                    // Update all other children to point to the new parent
                    Archive::where('parent_archive_id', $archive->id)
                        ->where('id', '!=', $newParent->id)
                        ->update(['parent_archive_id' => $newParent->id]);

                    Log::info("Parent archive deleted, new parent set: ID {$newParent->id}");
                }
            }

            // Check if this is a related archive deletion
            $isRelatedDeletion = request()->has('from_related') || str_contains(request()->header('referer'), 'related');

            $archive->delete();

            // Auto sync storage box counts after archive deletion
            \Illuminate\Support\Facades\Artisan::call('fix:storage-box-counts');

            // Handle AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "✅ Berhasil menghapus arsip ({$archiveNumber})!"
                ]);
            }

            // Redirect based on context
            if ($isRelatedDeletion) {
                // If deleting from related page, redirect back to related page
                $parentArchive = $archive->parentArchive ?? $archive->relatedArchives()->first();
                if ($parentArchive) {
                    return redirect()->route('admin.archives.related', $parentArchive)
                        ->with('delete_success', "✅ Berhasil menghapus arsip ({$archiveNumber})!");
                }
            }

            // Redirect to appropriate index page based on user role
            $redirectRoute = $user->role_type === 'admin' ? 'admin.archives.parent' : ($user->role_type === 'staff' ? 'staff.archives.parent' : 'intern.archives.parent');

            return redirect()->route($redirectRoute)->with('success', "✅ Berhasil menghapus arsip ({$archiveNumber})!");
        } catch (\Exception $e) {
            Log::error('Archive deletion error: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Gagal menghapus arsip. Silakan coba lagi.'
                ], 500);
            }

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
            'created_by' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'classification_id' => 'nullable|exists:classifications,id'
        ]);

        $status = $request->status;
        $yearFrom = $request->year_from;
        $yearTo = $request->year_to;
        $categoryId = $request->category_id;
        $classificationId = $request->classification_id;
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
            $createdBy = $user->id;
        } elseif ($user->hasRole('staff')) {
            if ($createdBy === 'current_user' || $createdBy === $user->id) {
                $createdBy = $user->id;
            } else {
                $createdBy = null;
            }
        } else {
            if ($createdBy === 'current_user') {
                $createdBy = $user->id;
            }
        }

        if ($yearFrom && $yearTo && $yearFrom > $yearTo) {
            return redirect()->back()->withErrors(['year_range' => 'Tahun "Dari" tidak boleh lebih besar dari tahun "Sampai"']);
        }

        $statusTitle = $this->getStatusTitle($status);
        $fileName = 'daftar-arsip-' . strtolower(str_replace(' ', '-', $statusTitle));

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

        // Pilih kelas ekspor berdasarkan status
        if ($status === 'Aktif') {
            return Excel::download(
                new ArchiveAktifExport($yearFrom, $yearTo, $createdBy, $categoryId, $classificationId),
                $fileName
            );
        } elseif ($status === 'Musnah') {
            return Excel::download(
                new ArchiveMusnahExport($yearFrom, $yearTo, $createdBy, $categoryId, $classificationId),
                $fileName
            );
        } elseif ($status === 'Inaktif' || $status === 'Permanen') {
            return Excel::download(
                new ArchiveInaktifPermanenExport($status, $yearFrom, $yearTo, $createdBy, $categoryId, $classificationId),
                $fileName
            );
        } else {
            // Untuk semua status
            return Excel::download(
                new ArchiveStatusExport($status, $yearFrom, $yearTo, $createdBy, $categoryId, $classificationId),
                $fileName
            );
        }
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

        // Filter by created_by
        if ($request->filled('created_by_filter')) {
            $query->where('created_by', $request->get('created_by_filter'));
        }

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
        try {
            $rack = \App\Models\StorageRack::find($rackId);
            if (!$rack) {
                \Log::warning("Rack not found: {$rackId}");
                return response()->json([]);
            }

            $boxes = \App\Models\StorageBox::where('rack_id', $rackId)
                ->whereHas('row', function ($query) use ($rowNumber) {
                    $query->where('row_number', $rowNumber);
                })
                ->orderBy('box_number')
                ->get(['box_number', 'status', 'archive_count', 'capacity']);

            \Log::info("Found {$boxes->count()} boxes for rack {$rackId}, row {$rowNumber}");

            return response()->json($boxes);
        } catch (\Exception $e) {
            \Log::error("Error in getRackRowBoxes: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Show the form for editing storage location
     */
    public function editLocation(Archive $archive)
    {
        $user = Auth::user();

        // Check permissions
        if ($user->hasRole('staff') || $user->hasRole('intern')) {
            if ($archive->created_by !== $user->id) {
                abort(403, 'Access denied. You can only edit your own archives.');
            }
        }

        // Get all active racks with available boxes (same logic as set location)
        $racks = \App\Models\StorageRack::with(['rows', 'boxes'])
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get()
            ->filter(function ($rack) {
                            // Calculate available boxes using real-time data
            $availableBoxes = $rack->boxes->filter(function ($box) {
                // Get real-time archive count
                $realTimeArchiveCount = Archive::where('box_number', $box->box_number)->count();
                return $realTimeArchiveCount < $box->capacity; // Available if not full
            });

            return $availableBoxes->count() > 0;
            });

        // Add next available box data for each rack (same as set location)
        foreach ($racks as $rack) {
            // Load boxes with their relationships
            $rack->load(['boxes' => function ($query) {
                $query->orderBy('box_number');
            }]);

            // Calculate available boxes using real-time data
            $availableBoxes = $rack->boxes->filter(function ($box) {
                $realTimeArchiveCount = Archive::where('box_number', $box->box_number)->count();
                return $realTimeArchiveCount < $box->capacity;
            });

            $partiallyFullBoxes = $rack->boxes->filter(function ($box) {
                $realTimeArchiveCount = Archive::where('box_number', $box->box_number)->count();
                return $realTimeArchiveCount >= $box->capacity / 2 && $realTimeArchiveCount < $box->capacity;
            });

            $fullBoxes = $rack->boxes->filter(function ($box) {
                $realTimeArchiveCount = Archive::where('box_number', $box->box_number)->count();
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

                // Get real-time archive count from actual archives
                $realTimeArchiveCount = Archive::where('box_number', $box->box_number)->count();
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
        }

        // Get current location info
        $currentRack = $archive->rack_number ? \App\Models\StorageRack::find($archive->rack_number) : null;
        $currentBox = $archive->box_number;
        $currentRow = $archive->row_number;
        $currentFile = $archive->file_number;

        // Debug: Log racks data
        \Log::info('EditLocation - Racks count: ' . $racks->count());
        \Log::info('EditLocation - First rack: ' . ($racks->first() ? $racks->first()->name : 'None'));

        // Convert racks to array for JavaScript compatibility - force indexed array
        $racksArray = array_values($racks->toArray());

        // Ensure proper JSON encoding for JavaScript
        foreach ($racksArray as &$rack) {
            if (isset($rack['boxes']) && is_array($rack['boxes'])) {
                $rack['boxes'] = array_values($rack['boxes']);
            }
        }

        $viewPath = $this->getViewPath('archives.edit-location');
        return view($viewPath, compact('archive', 'racks', 'racksArray', 'currentRack', 'currentBox', 'currentRow', 'currentFile'));
    }

    /**
     * Update the storage location
     */
    public function updateLocation(Request $request, Archive $archive)
    {
        \Log::info('UpdateLocation called for archive: ' . $archive->id);
        \Log::info('UpdateLocation request data: ' . json_encode($request->all()));

        $user = Auth::user();

        // Check permissions
        if ($user->hasRole('staff') || $user->hasRole('intern')) {
            if ($archive->created_by !== $user->id) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Access denied. You can only edit your own archives.'], 403);
                }
                abort(403, 'Access denied. You can only edit your own archives.');
            }
        }

        $request->validate([
            'rack_number' => 'required|exists:storage_racks,id',
            'row_number' => 'required|integer|min:1',
            'box_number' => 'required|integer|min:1',
            'file_number' => 'required|integer|min:1'
        ]);

        try {
            // Store old location for cleanup
            $oldRackNumber = $archive->rack_number;
            $oldBoxNumber = $archive->box_number;
            $oldFileNumber = $archive->file_number;

            // Check if the new location is available (excluding current archive)
            $existingArchive = Archive::where('rack_number', $request->rack_number)
                ->where('row_number', $request->row_number)
                ->where('box_number', $request->box_number)
                ->where('file_number', $request->file_number)
                ->where('id', '!=', $archive->id)
                ->first();

            if ($existingArchive) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Lokasi tersebut sudah digunakan oleh arsip lain.'], 400);
                }
                return redirect()->back()->withErrors(['error' => 'Lokasi tersebut sudah digunakan oleh arsip lain.']);
            }

            // Get next available file number for the new box with rack consideration
            $newFileNumber = Archive::getNextFileNumberForRack($request->rack_number, $request->box_number);

            // Update the archive location with new file number
            $archive->update([
                'rack_number' => $request->rack_number,
                'row_number' => $request->row_number,
                'box_number' => $request->box_number,
                'file_number' => $newFileNumber,
                'updated_by' => $user->id
            ]);

            // Update StorageBox counts for old and new boxes
            if ($oldBoxNumber && $oldBoxNumber != $request->box_number) {
                // Decrease count for old box
                $oldBox = StorageBox::where('box_number', $oldBoxNumber)->first();
                if ($oldBox) {
                    $oldBox->decrement('archive_count');
                    $oldBox->updateStatus();
                }

                // Increase count for new box
                $newBox = StorageBox::where('box_number', $request->box_number)->first();
                if ($newBox) {
                    $newBox->increment('archive_count');
                    $newBox->updateStatus();
                }
            }

            // Auto sync storage box counts
            \Illuminate\Support\Facades\Artisan::call('fix:storage-box-counts');

            // Log the location change
            Log::info("Archive location updated: Archive ID {$archive->id} moved to Rack {$request->rack_number}, Row {$request->row_number}, Box {$request->box_number}, File {$request->file_number} by user " . $user->id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lokasi penyimpanan berhasil diperbarui!'
                ]);
            }

            return redirect()->route($this->getViewPath('archives.show'), $archive)
                ->with('success', 'Lokasi penyimpanan berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Archive location update error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui lokasi penyimpanan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui lokasi penyimpanan. Silakan coba lagi.']);
        }
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

    /**
     * Bulk update location for multiple archives
     */
    public function bulkUpdateLocation(Request $request)
    {
        try {
            $validated = $request->validate([
                'archive_ids' => 'required|array',
                'archive_ids.*' => 'integer|exists:archives,id',
                'rack_number' => 'required|integer|min:1',
                'row_number' => 'required|integer|min:1',
                'box_number' => 'required|integer|min:1',
                'auto_generate_boxes' => 'boolean',
            ]);

            $archiveIds = $validated['archive_ids'];
            $rackNumber = $validated['rack_number'];
            $rowNumber = $validated['row_number'];
            $startBox = $validated['box_number'];
            $autoGenerateBoxes = $validated['auto_generate_boxes'] ?? false;

            $archives = Archive::whereIn('id', $archiveIds)->orderBy('kurun_waktu_start', 'asc')->get();
            $totalArchives = $archives->count();

            // Check if any archives already have location
            $archivesWithLocation = $archives->filter(function ($archive) {
                return !empty($archive->rack_number) && !empty($archive->box_number);
            });

            $hasExistingLocation = $archivesWithLocation->count() > 0;

            $updatedCount = 0;
            $currentBox = $startBox;
            $fileNumber = 1;

            // Sort archives by year (oldest first) for proper distribution
            $archives = $archives->sortBy('kurun_waktu_start');

            // Group archives by problem (category + classification + lampiran_surat) and year
            $archivesByProblem = $archives->groupBy(function ($archive) {
                return $archive->category_id . '_' . $archive->classification_id . '_' . $archive->lampiran_surat;
            });

            // Sort problems by oldest year first
            $archivesByProblem = $archivesByProblem->sortBy(function ($problemArchives) {
                return $problemArchives->min('kurun_waktu_start');
            });

            $currentBox = $startBox;
            $fileNumber = 1;

            // Track definitive numbers per problem and year
            $definitiveNumberTracker = [];

            foreach ($archivesByProblem as $problemKey => $problemArchives) {
                // Group archives in this problem by year
                $yearArchives = $problemArchives->groupBy(function ($archive) {
                    return $archive->kurun_waktu_start->format('Y');
                });

                foreach ($yearArchives as $year => $yearArchiveList) {
                    // Initialize definitive number counter for this problem-year combination
                    $problemYearKey = $problemKey . '_' . $year;
                    if (!isset($definitiveNumberTracker[$problemYearKey])) {
                        $definitiveNumberTracker[$problemYearKey] = 1;
                    }

                    // Reset file number for each year (file number restart from 1 per year)
                    $fileNumber = 1;

                    // Log for debugging
                    Log::info("Processing year {$year} for problem {$problemKey}", [
                        'year' => $year,
                        'problem_key' => $problemKey,
                        'archives_count' => $yearArchiveList->count(),
                        'definitive_number_start' => $definitiveNumberTracker[$problemYearKey]
                    ]);

                    foreach ($yearArchiveList as $archive) {
                        // Check if current box is full (50 archives)
                        $existingInCurrentBox = Archive::where('rack_number', $rackNumber)
                            ->where('row_number', $rowNumber)
                            ->where('box_number', $currentBox)
                            ->count();

                        if ($existingInCurrentBox >= 50) {
                            // Move to next box
                            $currentBox++;
                            $fileNumber = 1; // Reset file number for new box
                            // Recalculate existing count for new box
                            $existingInCurrentBox = Archive::where('rack_number', $rackNumber)
                                ->where('row_number', $rowNumber)
                                ->where('box_number', $currentBox)
                                ->count();
                        }

                        // Use sequential file number for this year (restart from 1 per year)
                        // Don't use existing count, use sequential numbering within the year

                        // Store old location for cleanup if needed
                        $oldRackNumber = $archive->rack_number;
                        $oldBoxNumber = $archive->box_number;
                        $oldFileNumber = $archive->file_number;

                        // Update archive location
                        $archive->update([
                            'rack_number' => $rackNumber,
                            'row_number' => $rowNumber,
                            'box_number' => $currentBox,
                            'file_number' => $fileNumber,
                            'storage_location' => "Rak {$rackNumber}, Baris {$rowNumber}, Box {$currentBox}",
                            'updated_by' => Auth::id(),
                        ]);

                        // Increment file number for next archive in this year
                        $fileNumber++;

                        // Generate definitive number per year (simple sequential: 1, 2, 3, etc.)
                        if ($archive->kurun_waktu_start) {
                            $definitiveNumber = $this->generateSimpleDefinitiveNumberPerYear($archive);
                            $archive->update(['definitive_number' => $definitiveNumber]);
                        }

                        // Update StorageBox counts for old and new boxes if location changed
                        if ($hasExistingLocation && $oldBoxNumber && $oldBoxNumber != $currentBox) {
                            // Decrease count for old box
                            $oldBox = \App\Models\StorageBox::where('box_number', $oldBoxNumber)->first();
                            if ($oldBox) {
                                $oldBox->decrement('archive_count');
                                $oldBox->updateStatus();
                            }
                        }

                        // Increase count for new box
                        $newBox = \App\Models\StorageBox::where('box_number', $currentBox)->first();
                        if ($newBox) {
                            $newBox->increment('archive_count');
                            $newBox->updateStatus();
                        }

                        $updatedCount++;
                    }
                }
            }

            Log::info("Bulk location update completed", [
                'archive_ids' => $archiveIds,
                'updated_count' => $updatedCount,
                'location' => "Rak {$rackNumber}, Baris {$rowNumber}, Box {$startBox}",
                'has_existing_location' => $hasExistingLocation,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => $hasExistingLocation
                    ? "Berhasil update lokasi untuk {$updatedCount} arsip (lokasi lama telah diganti)"
                    : "Berhasil set lokasi untuk {$updatedCount} arsip",
                'updated_count' => $updatedCount,
                'has_existing_location' => $hasExistingLocation
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk location update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal update lokasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate definitive number for archive
     */
    private function generateDefinitiveNumber(Archive $archive): int
    {
        if (!$archive->rack_number || !$archive->row_number || !$archive->box_number || !$archive->kurun_waktu_start) {
            return 0;
        }

        $rackNumber = str_pad($archive->rack_number, 2, '0', STR_PAD_LEFT);
        $rowNumber = str_pad($archive->row_number, 2, '0', STR_PAD_LEFT);
        $boxNumber = str_pad($archive->box_number, 3, '0', STR_PAD_LEFT);
        $year = $archive->kurun_waktu_start->format('Y');

        // Convert to integer format: RRBBYYYY (Rack-Row-Box-Year)
        return (int) ($rackNumber . $rowNumber . $boxNumber . $year);
    }

    /**
     * Generate definitive number for archive with specific file number
     */
    private function generateDefinitiveNumberWithFileNumber(Archive $archive, int $fileNumber): int
    {
        if (!$archive->rack_number || !$archive->row_number || !$archive->box_number || !$archive->kurun_waktu_start) {
            return 0;
        }

        // Use a more compact format: RRBBFFF (Rack-Row-Box-File)
        $rackNumber = str_pad($archive->rack_number, 2, '0', STR_PAD_LEFT);
        $rowNumber = str_pad($archive->row_number, 2, '0', STR_PAD_LEFT);
        $boxNumber = str_pad($archive->box_number, 2, '0', STR_PAD_LEFT);
        $fileNumberStr = str_pad($fileNumber, 3, '0', STR_PAD_LEFT);

        // Convert to integer format: RRBBFFF (max 9999999)
        return (int) ($rackNumber . $rowNumber . $boxNumber . $fileNumberStr);
    }

    /**
     * Generate definitive number for archive with sequential number per problem and year
     */
    private function generateDefinitiveNumberWithSequentialNumber(Archive $archive, int $sequentialNumber): int
    {
        if (!$archive->rack_number || !$archive->row_number || !$archive->box_number || !$archive->kurun_waktu_start) {
            return 0;
        }

        // Format: RRBBSSS (Rack-Row-Box-Sequential)
        $rackNumber = str_pad($archive->rack_number, 2, '0', STR_PAD_LEFT);
        $rowNumber = str_pad($archive->row_number, 2, '0', STR_PAD_LEFT);
        $boxNumber = str_pad($archive->box_number, 2, '0', STR_PAD_LEFT);
        $sequentialStr = str_pad($sequentialNumber, 3, '0', STR_PAD_LEFT);

        // Convert to integer format: RRBBSSS (max 9999999)
        $definitiveNumber = (int) ($rackNumber . $rowNumber . $boxNumber . $sequentialStr);

        // Debug log
        \Log::info("Definitive number generation", [
            'archive_id' => $archive->id,
            'rack_number' => $archive->rack_number,
            'row_number' => $archive->row_number,
            'box_number' => $archive->box_number,
            'sequential_number' => $sequentialNumber,
            'rack_padded' => $rackNumber,
            'row_padded' => $rowNumber,
            'box_padded' => $boxNumber,
            'sequential_padded' => $sequentialStr,
            'definitive_number' => $definitiveNumber
        ]);

        return $definitiveNumber;
    }

    /**
     * Generate simple definitive number per year (1, 2, 3, etc.)
     */
    private function generateSimpleDefinitiveNumberPerYear(Archive $archive): int
    {
        $classificationId = $archive->classification_id;
        $year = $archive->kurun_waktu_start->year;

        // Count archives with same classification and year, ordered by creation date
        $count = Archive::where('classification_id', $classificationId)
                       ->whereYear('kurun_waktu_start', $year)
                       ->where('id', '<=', $archive->id) // Count archives created before or at same time
                       ->count();

        // Definitive number restarts at 1 for each year
        return $count;
    }
}
