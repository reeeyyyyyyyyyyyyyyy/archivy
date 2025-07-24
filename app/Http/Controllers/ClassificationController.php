<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassificationRequest;
use App\Http\Requests\UpdateClassificationRequest;
use App\Models\Classification;
use App\Models\Category;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index()
    {
        $classifications = Classification::with('category')->latest()->paginate(10);
        return view('admin.classifications.index', compact('classifications'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.classifications.create', compact('categories'));
    }

    public function store(StoreClassificationRequest $request)
    {
        Classification::create($request->validated());
        return redirect()->route('admin.classifications.index')->with('success', 'Classification created successfully.');
    }

    public function show(Classification $classification)
    {
        //
    }

    public function edit(Classification $classification)
    {
        $categories = Category::all();
        return view('admin.classifications.edit', compact('classification', 'categories'));
    }

    public function update(UpdateClassificationRequest $request, Classification $classification)
    {
        $classification->update($request->validated());
        return redirect()->route('admin.classifications.index')->with('success', 'Classification updated successfully.');
    }

    public function destroy(Classification $classification)
    {
        try {
            $classification->archives()->delete();
            $classification->delete();
            return redirect()->back()->with('success', 'Klasifikasi dan semua arsip terkait berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus klasifikasi: ' . $e->getMessage());
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
