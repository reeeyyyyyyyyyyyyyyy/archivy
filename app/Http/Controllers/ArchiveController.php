<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Http\Requests\StoreArchiveRequest;
use App\Http\Requests\UpdateArchiveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;

class ArchiveController extends Controller
{
    public function index()
    {
        return $this->showArchivesByStatus('Aktif', 'Arsip Aktif');
    }

    public function inaktif()
    {
        return $this->showArchivesByStatus('Inaktif', 'Arsip Inaktif');
    }

    public function permanen()
    {
        return $this->showArchivesByStatus('Permanen', 'Arsip Permanen');
    }

    public function musnah()
    {
        return $this->showArchivesByStatus('Musnah', 'Arsip Musnah');
    }

    private function showArchivesByStatus(string $status, string $title)
    {
        $archives = Archive::where('status', $status)
                           ->with(['category', 'classification']) // KOREKSI: Relasi dimuat langsung dari Archive
                           ->latest()
                           ->paginate(10);
        
        // Debugging: Dump the archives collection to see what data is being fetched
        // dd($archives); 

        return view('admin.archives.index', compact('archives', 'title'));
    }

    public function getClassificationDetails(Classification $classification)
    {
        $classification->load('category');
        return response()->json($classification);
    }

    public function getClassificationsByCategory(Request $request)
    {
        $classifications = Classification::query()
            ->where('category_id', $request->query('category_id'))
            ->with('category')
            ->get();
        return response()->json($classifications);
    }

    private function generateIndexNumber(Classification $classification, $kurunWaktuStart)
    {
        $year = Carbon::parse($kurunWaktuStart)->year;
        $category = $classification->category;
        $statusChar = substr($category->nasib_akhir, 0, 1);
        $lastArchiveCount = Archive::whereYear('kurun_waktu_start', $year)->count();
        $nextId = $lastArchiveCount + 1;

        return sprintf('%04d/%s/%s/%d', $nextId, $classification->code, $statusChar, $year);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $classifications = Classification::with('category')->get(); 
        return view('admin.archives.create', compact('categories', 'classifications'));
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
            $transitionActiveDue = $kurunWaktuStart->copy()->addYears($category->retention_active);
            $transitionInactiveDue = $transitionActiveDue->copy()->addYears($category->retention_inactive);

            $archive = Archive::create(array_merge($validated, [
                'category_id' => $category->id,
                'index_number' => $indexNumber,
                'retention_active' => $category->retention_active,
                'retention_inactive' => $category->retention_inactive,
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'status' => 'Aktif',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]));

            return redirect()->route('admin.archives.index')->with('success', 'Archive created successfully.');
        } catch (Throwable $e) {
            dd($e->getMessage(), $e->getTraceAsString(), $validated); 
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Archive $archive)
    {
        // Untuk halaman show, Anda biasanya ingin relasi bersarang seperti classification.category
        $archive->load(['category', 'classification.category', 'createdByUser', 'updatedByUser']); 
        return view('admin.archives.show', compact('archive'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Archive $archive)
    {
        $categories = Category::all();
        $classifications = Classification::with('category')->get(); 
        return view('admin.archives.edit', compact('archive', 'categories', 'classifications'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArchiveRequest $request, Archive $archive)
    {
        $validated = $request->validated();

        $classification = Classification::with('category')->findOrFail($validated['classification_id']);
        $category = $classification->category;

        $kurunWaktuStart = Carbon::parse($validated['kurun_waktu_start']);
        $transitionActiveDue = $kurunWaktuStart->copy()->addYears($category->retention_active);
        $transitionInactiveDue = $transitionActiveDue->copy()->addYears($category->retention_inactive);

        $archive->update(array_merge($validated, [
            'category_id' => $category->id,
            'retention_active' => $category->retention_active,
            'retention_inactive' => $category->retention_inactive,
            'transition_active_due' => $transitionActiveDue,
            'transition_inactive_due' => $transitionInactiveDue,
            'updated_by' => auth()->id(),
        ]));

        return redirect()->route('admin.archives.index')->with('success', 'Archive updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Archive $archive)
    {
        $archive->delete();
        return redirect()->route('admin.archives.index')->with('success', 'Archive deleted successfully.');
    }
}