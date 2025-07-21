<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Classification;

class DashboardController extends Controller
{
    public function index()
    {
        $categoryCount = Category::count();
        $classificationCount = Classification::count();

        return view('admin.dashboard', compact('categoryCount', 'classificationCount'));
    }
}
