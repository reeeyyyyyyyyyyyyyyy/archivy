<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalFilesController extends Controller
{
    /**
     * Display a listing of personal files
     */
    public function index(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser'])
            ->where('manual_nasib_akhir', 'Masuk ke Berkas Perseorangan')
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

        // Get all categories for filter
        $categories = Category::orderBy('nama_kategori')->get();

        $title = 'Berkas Perseorangan';
        $showAddButton = false;
        $showActionButtons = true;

        return view('admin.personal-files.index', compact('archives', 'title', 'showAddButton', 'showActionButtons', 'categories'));
    }

    /**
     * Show the form for creating a new personal file
     */
    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();

        return view('admin.personal-files.create', compact('categories', 'classifications'));
    }

    /**
     * Store a newly created personal file
     */
    public function store(Request $request)
    {
        // Redirect to regular archive creation with pre-filled nasib akhir
        return redirect()->route('admin.archives.create', ['nasib_akhir' => 'Masuk ke Berkas Perseorangan']);
    }

    /**
     * Display the specified personal file
     */
    public function show(Archive $personalFile)
    {
        if ($personalFile->manual_nasib_akhir !== 'Masuk ke Berkas Perseorangan') {
            abort(404);
        }

        return view('admin.personal-files.show', compact('personalFile'));
    }

    /**
     * Show the form for editing the specified personal file
     */
    public function edit(Archive $personalFile)
    {
        if ($personalFile->manual_nasib_akhir !== 'Masuk ke Berkas Perseorangan') {
            abort(404);
        }

        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('nama_klasifikasi')->get();

        return view('admin.personal-files.edit', compact('personalFile', 'categories', 'classifications'));
    }

    /**
     * Update the specified personal file
     */
    public function update(Request $request, Archive $personalFile)
    {
        if ($personalFile->manual_nasib_akhir !== 'Masuk ke Berkas Perseorangan') {
            abort(404);
        }

        // Redirect to regular archive edit
        return redirect()->route('admin.archives.edit', $personalFile);
    }

    /**
     * Remove the specified personal file
     */
    public function destroy(Archive $personalFile)
    {
        if ($personalFile->manual_nasib_akhir !== 'Masuk ke Berkas Perseorangan') {
            abort(404);
        }

        try {
            $personalFile->delete();
            return redirect()->route('admin.personal-files.index')
                ->with('success', 'Berkas perseorangan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.personal-files.index')
                ->with('error', 'Gagal menghapus berkas perseorangan: ' . $e->getMessage());
        }
    }
}
