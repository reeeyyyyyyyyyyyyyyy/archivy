<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\StorageBox;
use App\Models\StorageRack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StorageLocationController extends Controller
{
    /**
     * Display archives without storage location for current user
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get archives created by current user that don't have complete storage location
        $query = Archive::with(['category', 'classification']);

        // For all roles, show only their own archives without location and not Musnah status
        $query->where('created_by', $user->id)
              ->where('status', '!=', 'Musnah')
              ->withoutLocation();

        // Apply filters
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        if ($request->filled('year_filter')) {
            $query->whereYear('kurun_waktu_start', $request->year_filter);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('index_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_filter')) {
            $query->where('category_id', $request->category_filter);
        }

        if ($request->filled('classification_filter')) {
            $query->where('classification_id', $request->classification_filter);
        }

        $archives = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter data
        $categories = \App\Models\Category::orderBy('nama_kategori')->get();
        $classifications = \App\Models\Classification::with('category')->orderBy('code')->get();

        // Determine view path based on user role using Spatie Permission
        if ($user->roles->contains('name', 'admin')) {
            $viewPath = 'admin.storage.index';
        } elseif ($user->roles->contains('name', 'staff')) {
            $viewPath = 'staff.storage.index';
        } elseif ($user->roles->contains('name', 'intern')) {
            $viewPath = 'intern.storage.index';
        } else {
            $viewPath = 'staff.storage.index'; // Default fallback
        }

        return view($viewPath, compact('archives', 'categories', 'classifications'));
    }

    /**
     * Show form to set storage location for specific archive
     */
    public function create($archiveId)
    {
        $user = Auth::user();

        $query = Archive::with(['category', 'classification'])
            ->where('id', $archiveId);

        // For non-staff users, only allow access to their own archives
        if (!$user->roles->contains('name', 'staff')) {
            $query->where('created_by', $user->id);
        }

        $archive = $query->firstOrFail();

        // Check if archive status is Musnah - if so, redirect with error
        if ($archive->status === 'Musnah') {
            return redirect()->back()->with('error', 'Arsip dengan status Musnah tidak dapat diatur lokasinya.');
        }

        // Get archive year for filtering
        $archiveYear = $archive->kurun_waktu_start ? $archive->kurun_waktu_start->year : null;

        // Get all active racks with available boxes and year filter
        $racks = \App\Models\StorageRack::with(['rows', 'boxes'])
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get()
            ->filter(function ($rack) use ($archiveYear) {
                // Filter by year if archive has year and rack has year filter
                if ($archiveYear && ($rack->year_start || $rack->year_end)) {
                    if ($rack->year_start && $rack->year_end) {
                        // Range filter
                        if ($archiveYear < $rack->year_start || $archiveYear > $rack->year_end) {
                            return false;
                        }
                    } elseif ($rack->year_start) {
                        // Start year only
                        if ($archiveYear < $rack->year_start) {
                            return false;
                        }
                    } elseif ($rack->year_end) {
                        // End year only
                        if ($archiveYear > $rack->year_end) {
                            return false;
                        }
                    }
                }

                // Calculate available boxes using real-time data
                $availableBoxes = $rack->boxes->filter(function ($box) use ($rack) {
                    // Get real-time archive count
                    $realTimeArchiveCount = Archive::where('rack_number', $rack->id)
                        ->where('box_number', $box->box_number)
                        ->count();
                    return $realTimeArchiveCount < $box->capacity; // Available if not full
                });

                return $availableBoxes->count() > 0;
            });

        // Get available years from archives for filtering
        $availableYears = Archive::selectRaw('EXTRACT(YEAR FROM kurun_waktu_start) as year')
            ->whereNotNull('kurun_waktu_start')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(function ($year) {
                return (int) $year;
            });

        // Add next available box data for each rack
        foreach ($racks as $rack) {
            // Load boxes with their relationships
            $rack->load(['boxes' => function ($query) {
                $query->orderBy('box_number');
            }]);

            // Calculate available boxes using real-time data
            $availableBoxes = $rack->boxes->filter(function ($box) use ($rack) {
                $realTimeArchiveCount = Archive::where('rack_number', $rack->id)
                    ->where('box_number', $box->box_number)
                    ->count();
                return $realTimeArchiveCount < $box->capacity;
            });

            $partiallyFullBoxes = $rack->boxes->filter(function ($box) use ($rack) {
                $realTimeArchiveCount = Archive::where('rack_number', $rack->id)
                    ->where('box_number', $box->box_number)
                    ->count();
                return $realTimeArchiveCount >= $box->capacity / 2 && $realTimeArchiveCount < $box->capacity;
            });

            $fullBoxes = $rack->boxes->filter(function ($box) use ($rack) {
                $realTimeArchiveCount = Archive::where('rack_number', $rack->id)
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
                $realTimeArchiveCount = Archive::where('rack_number', $rack->id)
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

        // Calculate next box number for the first available rack (for default suggestion)
        $nextBoxNumber = 1;
        if ($racks->isNotEmpty()) {
            $firstRack = $racks->first();
            $maxBoxNumber = StorageBox::where('rack_id', $firstRack->id)->max('box_number');
            $nextBoxNumber = $maxBoxNumber ? $maxBoxNumber + 1 : 1;
        }

        // Determine view path based on user role using Spatie Permission
        if ($user->roles->contains('name', 'admin')) {
            $viewPath = 'admin.storage.set-location';
        } elseif ($user->roles->contains('name', 'staff')) {
            $viewPath = 'staff.storage.set-location';
        } elseif ($user->roles->contains('name', 'intern')) {
            $viewPath = 'intern.storage.set-location';
        } else {
            $viewPath = 'staff.storage.set-location'; // Default fallback
        }

        return view($viewPath, compact('archive', 'racks', 'availableYears', 'nextBoxNumber'));
    }

    /**
     * Store storage location for archive
     */
    public function store(Request $request, $archiveId)
    {
        $request->validate([
            'box_number' => 'required|integer|min:1',
            'rack_number' => 'required|integer|min:1',
            'row_number' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        try {
            return DB::transaction(function () use ($request, $archiveId, $user) {
                Log::info('Attempting to store location for archive', ['archive_id' => $archiveId, 'user' => $user->id]);

                // Lock the archive to prevent concurrent modifications
                $query = Archive::where('id', $archiveId);

                // For non-staff users, only allow access to their own archives
                if ($user->roles->contains('name', 'staff')) {
                    $query->where('created_by', $user->id);
                }

                $archive = $query->lockForUpdate()->firstOrFail();
                Log::info('Archive found', ['archive' => $archive->id]);

                // Check if archive already has location
                if ($archive->box_number) {
                    $route = $this->getRedirectRoute($user);
                    Log::warning('Archive already has location', ['archive' => $archive->id]);
                    return redirect()->route($route)
                        ->with('error', "Arsip sudah memiliki lokasi: Rak {$archive->rack_number}, Box {$archive->box_number}");
                }

                // Lock the storage box to prevent concurrent access
                $storageBox = StorageBox::where('rack_id', $request->rack_number)
                    ->where('box_number', $request->box_number)
                    ->lockForUpdate()
                    ->first();

                if (!$storageBox) {
                    $route = $this->getRedirectRoute($user);
                    Log::error('Box not found', ['box_number' => $request->box_number]);
                    return redirect()->route($route)
                        ->with('error', "Box {$request->box_number} tidak ditemukan!");
                }

                // Check box capacity
                if ($storageBox->status === 'full' || $storageBox->archive_count >= $storageBox->capacity) {
                    $route = $this->getRedirectRoute($user);
                    Log::warning('Box is full', ['box' => $storageBox->id]);
                    return redirect()->route($route)
                        ->with('error', "Box {$request->box_number} sudah penuh atau melebihi kapasitas!");
                }

                // Get next file number following correct definitive number rules
                $fileNumber = Archive::getNextFileNumberCorrect(
                    $request->rack_number,
                    $request->box_number,
                    $archive->classification_id,
                    $archive->kurun_waktu_start->year
                );
                Log::info('Next file number determined', ['file_number' => $fileNumber]);

                // Update storage box count
                $storageBox->increment('archive_count');
                $storageBox->updateStatus();
                Log::info('Box updated', ['box' => $storageBox->id, 'new_count' => $storageBox->archive_count]);

                // Storage box count is automatically updated by the increment() method above
                // No need for manual command

                // Update archive with location
                $archive->update([
                    'box_number' => $request->box_number,
                    'file_number' => $fileNumber,
                    'rack_number' => $request->rack_number,
                    'row_number' => $request->row_number,
                    'updated_by' => $user->id,
                ]);
                Log::info('Archive location updated', ['archive' => $archive->id]);

                $route = $this->getRedirectRoute($user);
                return redirect()->route($route)
                    ->with('success', "Lokasi penyimpanan berhasil di-set untuk arsip: {$archive->index_number}. File Number: {$fileNumber}");
            });
        } catch (\Exception $e) {
            Log::error('Error storing location', [
                'error' => $e->getMessage(),
                'archive_id' => $archiveId,
                'user' => $user->id
            ]);

            $route = $this->getRedirectRoute($user);
            return redirect()->route($route)
                ->with('error', 'Terjadi kesalahan saat menyimpan lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to get redirect route based on user role
     */
    protected function getRedirectRoute($user)
    {
        if ($user->roles->contains('name', 'admin')) {
            return 'admin.storage.index';
        } elseif ($user->roles->contains('name', 'staff')) {
            return 'staff.storage.index';
        } elseif ($user->roles->contains('name', 'intern')) {
            return 'intern.storage.index';
        } else {
            return 'staff.storage.index'; // Default fallback
        }
    }

    /**
     * Get box contents grouped by category (masalah)
     */
    public function getBoxContents($rackId, $boxNumber = null)
    {
        try {
            // If only one parameter is provided, treat it as boxNumber
            if ($boxNumber === null) {
                $boxNumber = $rackId;
                $rackId = null;
            }

            $query = Archive::where('box_number', $boxNumber);

            // If rackId is provided, filter by rack_number
            if ($rackId !== null) {
                $query->where('rack_number', $rackId);
            } else {
                // Get rack ID from current page context
                $currentRackId = request()->route('rack') ?? request()->input('rack_id');
                if ($currentRackId) {
                    $query->where('rack_number', $currentRackId);
                } else {
                    // If no rackId, ensure archive has rack_number
                    $query->whereNotNull('rack_number');
                }
            }

            $archives = $query->whereNotNull('row_number')
                ->whereNotNull('file_number')
                ->with(['category', 'classification'])
                ->orderBy('kurun_waktu_start', 'asc')
                ->orderBy('file_number', 'asc')
                ->get();

            // Group archives by category (masalah)
            $groupedArchives = $archives->groupBy('category.nama_kategori');

            $result = [];
            foreach ($groupedArchives as $categoryName => $categoryArchives) {
                $result[] = [
                    'category' => $categoryName,
                    'archives' => $categoryArchives->map(function($archive) {
                        return [
                            'id' => $archive->id,
                            'index_number' => $archive->index_number,
                            'description' => $archive->description,
                            'file_number' => $archive->file_number,
                            'year' => $archive->kurun_waktu_start->format('Y'),
                            'classification' => $archive->classification->nama_klasifikasi,
                            'lampiran_surat' => $archive->lampiran_surat
                        ];
                    })->toArray()
                ];
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error getting box contents', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to get box contents'
            ], 500);
        }
    }

    /**
     * Get suggested file number for a box
     */
    public function getSuggestedFileNumber($rackId, $boxNumber)
    {
        try {
            // Get next file number for the specific rack and box
            $existingFileNumbers = Archive::where('rack_number', $rackId)
                ->where('box_number', $boxNumber)
                ->pluck('file_number')
                ->sort()
                ->values();

            // If no archives in box, start with 1
            if ($existingFileNumbers->isEmpty()) {
                $nextFileNumber = 1;
            } else {
                // Find the first gap in file numbers
                $expectedFileNumber = 1;
                foreach ($existingFileNumbers as $existingFileNumber) {
                    if ($existingFileNumber > $expectedFileNumber) {
                        // Found a gap, return the missing number
                        $nextFileNumber = $expectedFileNumber;
                        break;
                    }
                    $expectedFileNumber = $existingFileNumber + 1;
                }

                // No gaps found, return the next number after the highest
                if (!isset($nextFileNumber)) {
                    $nextFileNumber = $existingFileNumbers->max() + 1;
                }
            }

            return response()->json([
                'next_file_number' => $nextFileNumber
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting suggested file number', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to get next file number'
            ], 500);
        }
    }

    /**
     * Get suggested file number for a box with classification and year
     */
    public function getSuggestedFileNumberForClassification(Request $request)
    {
        try {
            $rackId = $request->rack_id;
            $boxNumber = $request->box_number;
            $classificationId = $request->classification_id;
            $year = $request->year;

            // Get next file number for the specific rack, box, classification, and year
            $nextFileNumber = Archive::getNextFileNumberForClassification(
                $rackId,
                $boxNumber,
                $classificationId,
                $year
            );

            return response()->json([
                'next_file_number' => $nextFileNumber
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting suggested file number for classification', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to get next file number'
            ], 500);
        }
    }

    /**
     * Get boxes for a specific rack and row
     */
    public function getBoxesForRackRow(Request $request)
    {
        try {
            $rackId = $request->rack_id;
            $rowNumber = $request->row_number;

            $boxes = StorageBox::where('rack_id', $rackId)
                ->whereHas('row', function ($query) use ($rowNumber) {
                    $query->where('row_number', $rowNumber);
                })
                ->orderBy('box_number')
                ->get(['id', 'box_number', 'archive_count', 'capacity']);

            return response()->json([
                'success' => true,
                'boxes' => $boxes
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting boxes for rack row', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error getting boxes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get rows for a specific rack
     */
    public function getRackRows(Request $request)
    {
        try {
            $rackId = $request->rack_id;

            $rows = \App\Models\StorageRow::where('rack_id', $rackId)
                ->orderBy('row_number')
                ->get(['id', 'row_number', 'total_boxes', 'available_boxes']);

            return response()->json([
                'success' => true,
                'rows' => $rows
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting rack rows', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error getting rows: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show form for generating box and file numbers
     */
    public function generateBoxFileNumbersForm()
    {
        // Get archives without complete storage locations
        $archivesWithoutLocation = Archive::whereNull('rack_number')
            ->orWhereNull('box_number')
            ->orWhereNull('file_number')
            ->count();

        // Get available racks
        $racks = StorageRack::where('status', 'active')->get();

        // Get statistics
        $totalArchives = Archive::count();
        $archivesWithLocation = Archive::whereNotNull('rack_number')
            ->whereNotNull('box_number')
            ->whereNotNull('file_number')
            ->count();

        return view('admin.storage.generate-box-file-numbers', compact(
            'archivesWithoutLocation',
            'racks',
            'totalArchives',
            'archivesWithLocation'
        ));
    }

    /**
     * Process automatic box and file number generation
     */
    public function generateBoxFileNumbers(Request $request)
    {
        $request->validate([
            'rack_id' => 'nullable|exists:storage_racks,id',
            'dry_run' => 'boolean',
            'action' => 'required|in:preview,generate'
        ]);

        $rackId = $request->input('rack_id');
        $dryRun = $request->boolean('dry_run', false);
        $action = $request->input('action');

        // Build command
        $command = 'storage:generate-box-file-numbers';
        $params = [];

        if ($rackId) {
            $params[] = "--rack-id={$rackId}";
        }

        if ($action === 'preview' || $dryRun) {
            $params[] = '--dry-run';
        }

        // Execute command
        $output = [];
        $returnCode = 0;

        try {
            $commandString = "php artisan {$command} " . implode(' ', $params);
            exec($commandString, $output, $returnCode);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menjalankan perintah: ' . $e->getMessage()]);
        }

        $outputText = implode("\n", $output);

        if ($returnCode === 0) {
            $message = ($action === 'preview' || $dryRun) ? 'Preview berhasil dibuat' : 'Generasi nomor box dan file berhasil';
            return redirect()->back()->with('success', $message)->with('command_output', $outputText);
        } else {
            return redirect()->back()->withErrors(['error' => 'Gagal menjalankan perintah. Output: ' . $outputText]);
        }
    }

    /**
     * Show form for generating box labels
     */
    public function generateBoxLabelsForm()
    {
        // Get boxes with archives
        $boxes = StorageBox::where('archive_count', '>', 0)
            ->orderBy('box_number')
            ->get();

        // Get statistics
        $totalBoxes = StorageBox::count();
        $boxesWithArchives = StorageBox::where('archive_count', '>', 0)->count();

        return view('admin.storage.generate-box-labels', compact('boxes', 'totalBoxes', 'boxesWithArchives'));
    }

    /**
     * Process box labels generation
     */
    public function generateBoxLabels(Request $request)
    {
        $request->validate([
            'rack_id' => 'required|exists:storage_racks,id',
            'box_start' => 'required|integer',
            'box_end' => 'required|integer|gte:box_start',
            'format' => 'required|in:pdf,word,excel'
        ]);

        $rackId = $request->input('rack_id');
        $boxStart = $request->input('box_start');
        $boxEnd = $request->input('box_end');
        $format = $request->input('format');

        $rack = StorageRack::find($rackId);
        if (!$rack) {
            return response()->json([
                'success' => false,
                'message' => 'Rack not found.'
            ], 400);
        }

        // Get boxes in the specified range
        $boxes = StorageBox::where('rack_id', $rackId)
            ->whereBetween('box_number', [$boxStart, $boxEnd])
            ->orderBy('box_number')
            ->get();

        if ($boxes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No boxes found in the specified range.'
            ], 400);
        }

        try {
            // Capture output using output buffer
            ob_start();
            $exitCode = \Illuminate\Support\Facades\Artisan::call('storage:generate-box-labels', [
                '--rack-id' => $rackId,
                '--box-start' => $boxStart,
                '--box-end' => $boxEnd,
                '--format' => $format
            ]);
            $output = ob_get_clean();

            if ($exitCode === 0) {
                // Extract download URL from command output
                if (preg_match('/Download URL: (.*)/', $output, $matches)) {
                    $downloadUrl = trim($matches[1]);
                } else {
                    // Fallback: generate filename based on current time
                    $extension = $format === 'word' ? '.docx' : ($format === 'excel' ? '.xlsx' : '.pdf');
                    $filename = 'rack_labels_' . $rackId . '_' . date('Y-m-d_H-i-s') . $extension;
                    $downloadUrl = url('storage/' . $filename);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Box labels generated successfully!',
                    'download_url' => $downloadUrl
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate labels. Exit code: ' . $exitCode,
                    'output' => $output
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate box labels: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get boxes for a specific rack
     */
    public function getBoxesForRack(Request $request)
    {
        $rackId = $request->input('rack_id');

        if (!$rackId) {
            return response()->json([
                'success' => false,
                'message' => 'Rack ID is required'
            ], 400);
        }

        try {
            $rack = StorageRack::with(['rows.boxes.archives'])->findOrFail($rackId);

            $boxes = [];
            foreach ($rack->rows as $row) {
                foreach ($row->boxes as $box) {
                    $archiveCount = $box->archives->count();
                    $capacity = $box->capacity ?? 50;

                    $boxes[] = [
                        'box_number' => $box->box_number,
                        'archive_count' => $archiveCount,
                        'capacity' => $capacity
                    ];
                }
            }

            return response()->json($boxes);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch boxes'
            ], 500);
        }
    }

    /**
     * Get rack rows for bulk location assignment
     */
    public function getRackRowsForBulk(Request $request)
    {
        $rackId = $request->input('rack_id');

        if (!$rackId) {
            return response()->json(['error' => 'Rack ID is required'], 400);
        }

        try {
            $rack = StorageRack::with('rows')->findOrFail($rackId);
            $rows = $rack->rows->map(function ($row) {
                return [
                    'id' => $row->id,
                    'row_number' => $row->row_number,
                    'name' => "Baris {$row->row_number}"
                ];
            });

            return response()->json($rows);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch rack rows'], 500);
        }
    }

    /**
     * Get boxes for rack in bulk location assignment
     */
    public function getBoxesForRackBulk(Request $request)
    {
        $rackId = $request->input('rack_id');

        if (!$rackId) {
            return response()->json(['error' => 'Rack ID is required'], 400);
        }

        try {
            $rack = StorageRack::with(['rows.boxes.archives'])->findOrFail($rackId);

            $boxes = [];
            foreach ($rack->rows as $row) {
                foreach ($row->boxes as $box) {
                    $archiveCount = $box->archives->count();
                    $capacity = $box->capacity ?? 50;
                    $status = $archiveCount >= $capacity ? 'full' : ($archiveCount > 0 ? 'occupied' : 'empty');

                    $boxes[] = [
                        'row_number' => $row->row_number,
                        'box_number' => $box->box_number,
                        'archive_count' => $archiveCount,
                        'capacity' => $capacity,
                        'status' => $status,
                        'available_space' => $capacity - $archiveCount
                    ];
                }
            }

            return response()->json($boxes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch boxes'], 500);
        }
    }

    /**
     * Get boxes for specific rack and row in bulk location assignment
     */
    public function getBoxesForRackRowBulk(Request $request)
    {
        $rackId = $request->input('rack_id');
        $rowNumber = $request->input('row_number');

        if (!$rackId || !$rowNumber) {
            return response()->json(['error' => 'Rack ID and Row Number are required'], 400);
        }

        try {
            $rack = StorageRack::with(['rows.boxes.archives'])->findOrFail($rackId);
            $row = $rack->rows->where('row_number', $rowNumber)->first();

            if (!$row) {
                return response()->json(['error' => 'Row not found'], 404);
            }

            $boxes = $row->boxes->map(function ($box) {
                $archiveCount = $box->archives->count();
                $capacity = $box->capacity ?? 50;
                $status = $archiveCount >= $capacity ? 'full' : ($archiveCount > 0 ? 'occupied' : 'empty');

                return [
                    'id' => $box->id,
                    'box_number' => $box->box_number,
                    'archive_count' => $archiveCount,
                    'capacity' => $capacity,
                    'status' => $status,
                    'available_space' => $capacity - $archiveCount
                ];
            });

            return response()->json($boxes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch boxes'], 500);
        }
    }

    /**
     * Get all racks with boxes data for visual grid
     */
    public function getRacks()
    {
        try {
            $racks = StorageRack::with(['boxes' => function ($query) {
                $query->select('id', 'rack_id', 'box_number', 'archive_count', 'capacity');
            }])->get();

            return response()->json($racks);
        } catch (\Exception $e) {
            Log::error('Error fetching racks: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch racks'], 500);
        }
    }
}
