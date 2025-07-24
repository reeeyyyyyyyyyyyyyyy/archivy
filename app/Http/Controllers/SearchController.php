<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Display advanced search form and results
     */
    public function index(Request $request)
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::orderBy('code')->get();
        $users = $this->getFilterUsers();
        $statuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah'];
        return view('admin.search.index', compact('categories', 'classifications', 'users', 'statuses'));
    }

    public function search(Request $request)
    {
        $archives = Archive::all();
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::orderBy('code')->get();
        $users = $this->getFilterUsers();
        $statuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah'];
        return view('admin.search.index', compact('archives', 'categories', 'classifications', 'users', 'statuses'));
    }

    private function getFilterUsers()
    {
        return \App\Models\User::orderBy('name')->get();
    }
}
