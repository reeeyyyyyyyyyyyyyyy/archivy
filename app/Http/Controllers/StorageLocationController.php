<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorageLocationController extends Controller
{
    /**
     * Display archives without storage location for current user
     */
    public function index()
    {
        $user = Auth::user();

        // Get archives created by current user that don't have complete storage location
        $archives = Archive::with(['category', 'classification'])
            ->where('created_by', $user->id)
            ->withoutLocation()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('storage.index', compact('archives'));
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

        // Suggest next box and file numbers
        $nextBoxNumber = Archive::getNextBoxNumber();

        return view('storage.create', compact('archive', 'nextBoxNumber'));
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

        $archive = Archive::where('id', $archiveId)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        // Get next file number for the specified box
        $fileNumber = Archive::getNextFileNumber($request->box_number);

        DB::transaction(function() use ($archive, $request, $fileNumber) {
            $archive->update([
                'box_number' => $request->box_number,
                'file_number' => $fileNumber,
                'rack_number' => $request->rack_number,
                'row_number' => $request->row_number,
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()->route('storage.index')
            ->with('success', "Lokasi penyimpanan berhasil di-set untuk arsip: {$archive->index_number}. File Number: {$fileNumber}");
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
}
