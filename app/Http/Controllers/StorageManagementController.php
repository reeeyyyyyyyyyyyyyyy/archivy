<?php

namespace App\Http\Controllers;

use App\Models\StorageRack;
use App\Models\StorageRow;
use App\Models\StorageBox;
use App\Models\StorageCapacitySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorageManagementController extends Controller
{
    public function index()
    {
        $racks = StorageRack::with(['rows', 'boxes'])->get();
        return view('admin.storage-management.index', compact('racks'));
    }

    public function create()
    {
        return view('admin.storage-management.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'total_rows' => 'required|integer|min:1|max:20',
            'capacity_per_box' => 'required|integer|min:10|max:100',
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string',
        ]);

        return DB::transaction(function() use ($request) {
            // Create the rack
            $rack = StorageRack::create([
                'name' => $request->name,
                'description' => $request->description,
                'total_rows' => $request->total_rows,
                'total_boxes' => $request->total_rows * 4, // 4 boxes per row
                'capacity_per_box' => $request->capacity_per_box,
                'status' => $request->status,
            ]);

            // Get the next available box number to avoid conflicts
            $nextBoxNumber = StorageBox::max('box_number') + 1;

            // Create rows and boxes
            for ($rowNumber = 1; $rowNumber <= $request->total_rows; $rowNumber++) {
                $row = StorageRow::create([
                    'rack_id' => $rack->id,
                    'row_number' => $rowNumber,
                    'total_boxes' => 4,
                    'available_boxes' => 4,
                    'status' => 'available',
                ]);

                // Create 4 boxes per row
                for ($boxInRow = 1; $boxInRow <= 4; $boxInRow++) {
                    StorageBox::create([
                        'rack_id' => $rack->id,
                        'row_id' => $row->id,
                        'box_number' => $nextBoxNumber,
                        'archive_count' => 0,
                        'capacity' => $request->capacity_per_box,
                        'status' => 'available',
                    ]);
                    $nextBoxNumber++;
                }
            }

            // Create capacity settings
            StorageCapacitySetting::create([
                'rack_id' => $rack->id,
                'default_capacity_per_box' => $request->capacity_per_box,
                'warning_threshold' => (int)($request->capacity_per_box * 0.8), // 80% of capacity
                'auto_assign' => true,
            ]);

            return redirect()->route('admin.storage-management.index')
                ->with('success', "Rak '{$rack->name}' berhasil dibuat dengan {$rack->total_boxes} box!");
        });
    }

    public function show(StorageRack $rack)
    {
        $rack->load(['rows', 'boxes', 'capacitySettings']);

        // Get archives in this rack
        $archives = \App\Models\Archive::where('rack_number', $rack->id)
            ->with(['category', 'classification', 'createdByUser'])
            ->orderBy('box_number')
            ->orderBy('file_number')
            ->get();

        return view('admin.storage-management.show', compact('rack', 'archives'));
    }

    public function edit(StorageRack $rack)
    {
        return view('admin.storage-management.edit', compact('rack'));
    }

    public function update(Request $request, StorageRack $rack)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'capacity_per_box' => 'required|integer|min:10|max:100',
            'status' => 'required|in:active,inactive,maintenance'
        ]);

        $rack->update([
            'name' => $request->name,
            'description' => $request->description,
            'capacity_per_box' => $request->capacity_per_box,
            'status' => $request->status
        ]);

        // Update capacity settings
        $rack->capacitySettings()->update([
            'default_capacity_per_box' => $request->capacity_per_box,
            'warning_threshold' => (int)($request->capacity_per_box * 0.8)
        ]);

        return redirect()->route('admin.storage-management.index')
            ->with('success', 'Rak berhasil diperbarui!');
    }

    public function destroy(StorageRack $rack)
    {
        // Check if rack has archives (linked directly to rack_number)
        $archiveCount = \App\Models\Archive::where('rack_number', $rack->id)->count();

        if ($archiveCount > 0) {
            return redirect()->route('admin.storage-management.index')
                ->with('error', "Gagal! Tidak dapat menghapus rak $rack->name karena masih ada {$archiveCount} arsip di dalamnya. Pindahkan arsip terlebih dahulu sebelum menghapus rak.");
        }

        // Check if any boxes have archives (using archive_count field)
        $boxesWithArchives = \App\Models\StorageBox::where('rack_id', $rack->id)
            ->where('archive_count', '>', 0)
            ->count();

        if ($boxesWithArchives > 0) {
            return redirect()->route('admin.storage-management.index')
                ->with('error', "Gagal! Tidak dapat menghapus rak '{$rack->name}' karena masih ada {$boxesWithArchives} box yang berisi arsip. Pindahkan arsip terlebih dahulu sebelum menghapus rak.");
        }

        try {
            // Delete all boxes and rows first
            \App\Models\StorageBox::where('rack_id', $rack->id)->delete();
            \App\Models\StorageRow::where('rack_id', $rack->id)->delete();
            \App\Models\StorageCapacitySetting::where('rack_id', $rack->id)->delete();

            // Delete the rack
            $rack->delete();

            return redirect()->route('admin.storage-management.index')
                ->with('success', "Rak '{$rack->name}' berhasil dihapus!");
        } catch (\Exception $e) {
            return redirect()->route('admin.storage-management.index')
                ->with('error', "Gagal menghapus rak: " . $e->getMessage());
        }
    }
}
