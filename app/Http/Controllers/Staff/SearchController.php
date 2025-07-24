<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Display the advanced search form and results
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('code')->get();

        // Filter users based on role (staff and intern only)
        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['staff', 'intern']);
        })->orderBy('name')->get();

        $statuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah'];

        // If request has search parameters, perform search
        if ($request->filled('search') || $request->filled('status') || $request->filled('category_id') ||
            $request->filled('classification_id') || $request->filled('created_by') ||
            $request->filled('date_from') || $request->filled('date_to')) {

            $query = Archive::with(['category', 'classification', 'createdByUser']);

            // Filter by user role (staff and intern can only see their own archives)
            if ($user->hasRole('staff') || $user->hasRole('intern')) {
                $query->whereHas('createdByUser.roles', function($q) {
                    $q->whereIn('name', ['staff', 'intern']);
                });
            }

            // Apply search filters
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('index_number', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('ket', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('classification_id')) {
                $query->where('classification_id', $request->classification_id);
            }

            if ($request->filled('created_by')) {
                $query->where('created_by', $request->created_by);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $archives = $query->orderBy('created_at', 'desc')->paginate(25);
        } else {
            $archives = null;
        }

        // Determine view path based on user role
        $viewPath = $user->hasRole('admin') ? 'admin.search.index' :
                   ($user->hasRole('staff') ? 'staff.search.index' : 'intern.search.index');

        return view($viewPath, compact(
            'archives',
            'categories',
            'classifications',
            'users',
            'statuses'
        ));
    }
}
