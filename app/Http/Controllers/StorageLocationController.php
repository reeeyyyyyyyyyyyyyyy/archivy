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
        $query = Archive::with(['category', 'classification'])
            ->where('created_by', $user->id)
            ->withoutLocation();

        // Apply filters
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
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

        return view('admin.storage.index', compact('archives', 'categories', 'classifications'));
    }

    /**
     * Show form to set storage location for specific archive
     */
    public function create($archiveId)
    {
        $archive = Archive::with(['category', 'classification'])
            ->where('id', $archiveId)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        // Get all active racks with available boxes
        $racks = \App\Models\StorageRack::with(['rows', 'boxes'])
            ->where('status', 'active')
            ->get()
            ->filter(function($rack) {
                // Calculate available boxes using new formula
                $capacity = $rack->capacity_per_box;
                $n = $capacity;
                $halfN = $n / 2;

                $availableBoxes = $rack->boxes->filter(function($box) use ($n, $halfN) {
                    return $box->archive_count < $halfN; // Available if less than half capacity
                });

                return $availableBoxes->count() > 0;
            });

                    // Add next available box data for each rack
            foreach ($racks as $rack) {
                // Load boxes with their relationships
                $rack->load(['boxes' => function($query) {
                    $query->orderBy('box_number');
                }]);

                // Calculate available boxes using new formula
                $capacity = $rack->capacity_per_box;
                $n = $capacity;
                $halfN = $n / 2;

                $availableBoxes = $rack->boxes->filter(function($box) use ($halfN) {
                    return $box->archive_count < $halfN;
                });

                $partiallyFullBoxes = $rack->boxes->filter(function($box) use ($n, $halfN) {
                    return $box->archive_count >= $halfN && $box->archive_count < $n;
                });

                $fullBoxes = $rack->boxes->filter(function($box) use ($n) {
                    return $box->archive_count >= $n;
                });

                // Set calculated counts
                $rack->available_boxes_count = $availableBoxes->count();
                $rack->partially_full_boxes_count = $partiallyFullBoxes->count();
                $rack->full_boxes_count = $fullBoxes->count();

                // Ensure boxes have all required data
                foreach ($rack->boxes as $box) {
                    $box->row_number = $box->row ? $box->row->row_number : 0;
                    $box->box_number = $box->box_number;
                    $box->archive_count = $box->archive_count;
                    $box->capacity = $box->capacity;

                    // Calculate status using new formula
                    if ($box->archive_count >= $n) {
                        $box->status = 'full';
                    } elseif ($box->archive_count >= $halfN) {
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
        $racks = $racks->values(); // Reset array keys

        return view('admin.storage.set-location', compact('archive', 'racks'));
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

        return DB::transaction(function() use ($request, $archiveId) {
            // Lock the archive to prevent concurrent modifications
            $archive = Archive::where('id', $archiveId)
                ->where('created_by', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            // Check if archive already has location
            if ($archive->box_number) {
                return redirect()->route('admin.storage.index')
                    ->with('error', "Arsip sudah memiliki lokasi: Rak {$archive->rack_number}, Box {$archive->box_number}");
            }

            // Lock the storage box to prevent concurrent access
            $storageBox = StorageBox::where('box_number', $request->box_number)
                ->lockForUpdate()
                ->first();

            if (!$storageBox) {
                return redirect()->route('admin.storage.index')
                    ->with('error', "Box {$request->box_number} tidak ditemukan!");
            }

            // Check if box is full
            if ($storageBox->status === 'full') {
                return redirect()->route('admin.storage.index')
                    ->with('error', "Box {$request->box_number} sudah penuh!");
            }

            // Check if box capacity is exceeded
            if ($storageBox->archive_count >= $storageBox->capacity) {
                return redirect()->route('admin.storage.index')
                    ->with('error', "Box {$request->box_number} sudah mencapai kapasitas maksimal!");
            }

            // Get next file number for the specified box
            $fileNumber = Archive::getNextFileNumber($request->box_number);

            // Update storage box count
            $storageBox->increment('archive_count');
            $storageBox->updateStatus(); // Update status based on capacity

            // Update archive with location
            $archive->update([
                'box_number' => $request->box_number,
                'file_number' => $fileNumber,
                'rack_number' => $request->rack_number,
                'row_number' => $request->row_number,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('admin.storage.index')
                ->with('success', "Lokasi penyimpanan berhasil di-set untuk arsip: {$archive->index_number}. File Number: {$fileNumber}");
        });
    }

    /**
     * Get archives in a specific box for file numbering reference
     */
    public function getBoxContents($boxNumber)
    {
        $archives = Archive::where('box_number', $boxNumber)
            ->where('created_by', Auth::id())
            ->orderBy('file_number')
            ->get(['id', 'index_number', 'description', 'file_number']);

        return response()->json($archives);
    }

    /**
     * Get suggested file number for a box
     */
    public function getSuggestedFileNumber($boxNumber)
    {
        $nextFileNumber = Archive::getNextFileNumber($boxNumber);

        return response()->json([
            'next_file_number' => $nextFileNumber
        ]);
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
            'box_numbers' => 'nullable|string',
            'format' => 'required|in:pdf,word',
            'action' => 'required|in:preview,generate'
        ]);

        $boxNumbers = $request->input('box_numbers');
        $format = $request->input('format');
        $action = $request->input('action');

        // Build command
        $command = 'storage:generate-box-labels';
        $params = ["--format={$format}"];

        if ($boxNumbers) {
            $params[] = "--box-numbers={$boxNumbers}";
        }

        if ($action === 'preview') {
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
            $message = $action === 'preview' ? 'Preview berhasil dibuat' : 'Label box berhasil dibuat';

            if ($action === 'generate' && $format === 'pdf') {
                $pdfPath = storage_path('app/public/box-labels.pdf');
                if (file_exists($pdfPath)) {
                    return response()->download($pdfPath, 'box-labels.pdf');
                }
            }

            return redirect()->back()->with('success', $message)->with('command_output', $outputText);
        } else {
            return redirect()->back()->withErrors(['error' => 'Gagal menjalankan perintah. Output: ' . $outputText]);
        }
    }
}
