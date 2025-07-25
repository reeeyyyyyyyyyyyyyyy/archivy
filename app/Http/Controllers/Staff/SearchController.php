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
     * Display the search form and results
     */
    public function index(Request $request)
    {
        // Get filter data
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('code')->get();

        // Filter users based on role (staff and intern only)
        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['staff', 'intern']);
        })->orderBy('name')->get();

        $statuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah'];
        $archives = collect(); // Initialize empty collection

        // If request has search parameters, perform search
        if ($request->filled('term') || $request->filled('status') || $request->filled('created_by')) {

            $query = Archive::with(['category', 'classification', 'createdByUser']);

            // Filter by user role (staff can see staff and intern archives)
            $query->whereHas('createdByUser.roles', function($q) {
                $q->whereIn('name', ['staff', 'intern']);
            });

            // Apply search filters
            if ($request->filled('term')) {
                $searchTerm = $request->term;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('index_number', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhereHas('category', function($catQuery) use ($searchTerm) {
                          $catQuery->where('nama_kategori', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('classification', function($classQuery) use ($searchTerm) {
                          $classQuery->where('nama_klasifikasi', 'like', "%{$searchTerm}%")
                                     ->orWhere('code', 'like', "%{$searchTerm}%");
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('created_by')) {
                $query->where('created_by', $request->created_by);
            }

            $archives = $query->orderBy('created_at', 'desc')->paginate(15);
        }

        return view('staff.search.index', compact(
            'archives',
            'categories',
            'classifications',
            'users',
            'statuses'
        ));
    }

    /**
     * Perform search and return results
     */
    public function search(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Autocomplete for search
     */
    public function autocomplete(Request $request)
    {
        $term = $request->get('term');

        $results = Archive::where('index_number', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%")
            ->orWhereHas('category', function($query) use ($term) {
                $query->where('nama_kategori', 'like', "%{$term}%");
            })
            ->orWhereHas('classification', function($query) use ($term) {
                $query->where('nama_klasifikasi', 'like', "%{$term}%")
                      ->orWhere('code', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get(['id', 'index_number', 'description'])
            ->map(function($archive) {
                return [
                    'id' => $archive->id,
                    'value' => $archive->index_number . ' - ' . $archive->description,
                    'label' => $archive->index_number . ' - ' . $archive->description
                ];
            });

        return response()->json($results);
    }

    /**
     * Export search results
     */
    public function exportResults(Request $request)
    {
        // Implementation for exporting search results
        return redirect()->back()->with('info', 'Export feature will be implemented soon.');
    }
}
