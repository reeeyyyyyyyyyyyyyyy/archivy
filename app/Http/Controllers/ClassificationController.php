<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassificationRequest;
use App\Http\Requests\UpdateClassificationRequest;
use App\Models\Classification;
use App\Models\Category;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Classification::with('category');

        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $classifications = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('admin.classifications.index', compact('classifications', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.classifications.create', compact('categories'));
    }

    public function store(StoreClassificationRequest $request)
    {
        try {
            $classification = Classification::create($request->validated());
            return redirect()->route('admin.classifications.index')->with('success', "✅ Berhasil membuat klasifikasi '{$classification->code} - {$classification->nama_klasifikasi}'!");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', '❌ Gagal membuat klasifikasi. Silakan coba lagi.');
        }
    }

    public function show(Classification $classification)
    {
        $classification->load('category');
        return view('admin.classifications.show', compact('classification'));
    }

    public function edit(Classification $classification)
    {
        $categories = Category::all();
        return view('admin.classifications.edit', compact('classification', 'categories'));
    }

    public function update(UpdateClassificationRequest $request, Classification $classification)
    {
        try {
            $oldCode = $classification->code;
            $oldName = $classification->nama_klasifikasi;
        $classification->update($request->validated());
            return redirect()->route('admin.classifications.index')->with('success', "✅ Berhasil mengubah klasifikasi '{$oldCode} - {$oldName}' menjadi '{$classification->code} - {$classification->nama_klasifikasi}'!");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', '❌ Gagal mengubah klasifikasi. Silakan coba lagi.');
        }
    }

    public function destroy(Classification $classification)
    {
        try {
            $classificationName = $classification->code . ' - ' . $classification->nama_klasifikasi;
            $archiveCount = $classification->archives()->count();

            // Delete related archives first
            $classification->archives()->delete();

            // Delete the classification
            $classification->delete();

            $message = $archiveCount > 0
                ? "✅ Berhasil menghapus klasifikasi '{$classificationName}' beserta {$archiveCount} arsip terkait!"
                : "✅ Berhasil menghapus klasifikasi '{$classificationName}'!";

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal menghapus klasifikasi. Klasifikasi mungkin masih digunakan oleh data lain.');
        }
    }

    public function getFilteredClassifications(Request $request)
    {
        $query = Classification::query();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id); // Filter directly by category_id
        }

        // Ensure category relationship is loaded for filtering or details
        return response()->json($query->with('category')->get());
    }
}
