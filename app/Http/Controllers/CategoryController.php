<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = Category::create($request->validated());
            return redirect()->route('admin.categories.index')->with('success', "✅ Berhasil membuat kategori $category->nama_kategori!");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', '❌ Gagal membuat kategori. Silakan coba lagi.');
        }
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $oldName = $category->nama_kategori;
            $category->update($request->validated());
            return redirect()->route('admin.categories.index')->with('success', "✅ Berhasil mengubah kategori $oldName menjadi $category->nama_kategori!");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', '❌ Gagal mengubah kategori. Silakan coba lagi.');
        }
    }

    public function destroy(Category $category)
    {
        try {
            $categoryName = $category->nama_kategori;
            $archiveCount = $category->archives()->count();

            // Delete related archives first
            $category->archives()->delete();

            // Delete the category
            $category->delete();

            $message = $archiveCount > 0
                ? "✅ Berhasil menghapus kategori $categoryName beserta $archiveCount arsip terkait!"
                : "✅ Berhasil menghapus kategori $categoryName!";

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal menghapus kategori. Kategori mungkin masih digunakan oleh data lain.');
        }
    }
}
