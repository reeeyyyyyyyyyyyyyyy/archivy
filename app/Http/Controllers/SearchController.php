<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Show advanced search form
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $classifications = Classification::with('category')->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $statuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah'];
        
        return view('admin.search.index', compact('categories', 'classifications', 'users', 'statuses'));
    }
    
    /**
     * Perform advanced search
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'term' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'classification_id' => 'nullable|exists:classifications,id',
            'status' => 'nullable|in:Aktif,Inaktif,Permanen,Musnah',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'created_by' => 'nullable|exists:users,id',
            'approaching_transition' => 'nullable|integer|min:1|max:365',
            'per_page' => 'nullable|integer|min:5|max:100'
        ]);
        
        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser']);
        
        // Apply search filters using scopes
        $query->search($validated['term'] ?? null)
              ->byCategory($validated['category_id'] ?? null)
              ->byClassification($validated['classification_id'] ?? null)
              ->byStatus($validated['status'] ?? null)
              ->byDateRange($validated['date_from'] ?? null, $validated['date_to'] ?? null)
              ->byCreatedUser($validated['created_by'] ?? null);
        
        // Special filter for approaching transition
        if (isset($validated['approaching_transition'])) {
            $query->approachingTransition($validated['approaching_transition']);
        }
        
        $perPage = $validated['per_page'] ?? 15;
        $archives = $query->latest('created_at')->paginate($perPage);
        
        // Add search term highlighting
        $searchTerm = $validated['term'] ?? '';
        
        // Get search statistics (fix PostgreSQL DISTINCT ON issue)
        $searchStats = [
            'total_found' => $archives->total(),
            'search_term' => $searchTerm,
            'filters_applied' => collect($validated)->filter()->count(),
            'categories_found' => Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser'])
                ->search($validated['term'] ?? null)
                ->byCategory($validated['category_id'] ?? null)
                ->byClassification($validated['classification_id'] ?? null)
                ->byStatus($validated['status'] ?? null)
                ->byDateRange($validated['date_from'] ?? null, $validated['date_to'] ?? null)
                ->byCreatedUser($validated['created_by'] ?? null)
                ->distinct()
                ->count('category_id'),
            'statuses_found' => Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser'])
                ->search($validated['term'] ?? null)
                ->byCategory($validated['category_id'] ?? null)
                ->byClassification($validated['classification_id'] ?? null)
                ->byStatus($validated['status'] ?? null)
                ->byDateRange($validated['date_from'] ?? null, $validated['date_to'] ?? null)
                ->byCreatedUser($validated['created_by'] ?? null)
                ->groupBy('status')
                ->pluck('status')
                ->count()
        ];
        
        if ($request->ajax()) {
            return response()->json([
                'archives' => $archives->items(),
                'stats' => $searchStats,
                'pagination' => [
                    'current_page' => $archives->currentPage(),
                    'last_page' => $archives->lastPage(),
                    'total' => $archives->total()
                ]
            ]);
        }
        
        $categories = Category::orderBy('name')->get();
        $classifications = Classification::with('category')->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $statuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah'];
        
        return view('admin.search.index', compact(
            'archives', 
            'searchTerm', 
            'searchStats', 
            'categories', 
            'classifications', 
            'users', 
            'statuses'
        ));
    }
    
    /**
     * Get autocomplete suggestions via AJAX
     */
    public function autocomplete(Request $request)
    {
        $term = $request->get('term', '');
        $type = $request->get('type', 'all'); // all, index, uraian, category, classification
        
        if (strlen($term) < 2) {
            return response()->json([]);
        }
        
        $suggestions = collect();
        
        switch ($type) {
            case 'index':
                $suggestions = Archive::where('index_number', 'ILIKE', "%{$term}%")
                    ->distinct()
                    ->pluck('index_number')
                    ->take(10);
                break;
                
            case 'uraian':
                $suggestions = Archive::where('uraian', 'ILIKE', "%{$term}%")
                    ->distinct()
                    ->pluck('uraian')
                    ->take(10);
                break;
                
            case 'category':
                $suggestions = Category::where('name', 'ILIKE', "%{$term}%")
                    ->pluck('name')
                    ->take(10);
                break;
                
            case 'classification':
                $suggestions = Classification::where('name', 'ILIKE', "%{$term}%")
                    ->orWhere('code', 'ILIKE', "%{$term}%")
                    ->get()
                    ->map(function ($item) {
                        return $item->code . ' - ' . $item->name;
                    })
                    ->take(10);
                break;
                
            default: // 'all'
                $indexSuggestions = Archive::where('index_number', 'ILIKE', "%{$term}%")
                    ->distinct()
                    ->pluck('index_number')
                    ->take(5);
                    
                $uraianSuggestions = Archive::where('uraian', 'ILIKE', "%{$term}%")
                    ->distinct()
                    ->pluck('uraian')
                    ->take(5);
                    
                $suggestions = $indexSuggestions->merge($uraianSuggestions);
                break;
        }
        
        return response()->json($suggestions->values());
    }
    
    /**
     * Get quick search results via AJAX
     */
    public function quickSearch(Request $request)
    {
        $term = $request->get('q', '');
        
        if (strlen($term) < 2) {
            return response()->json([]);
        }
        
        $archives = Archive::search($term)
            ->with(['category', 'classification'])
            ->take(10)
            ->get()
            ->map(function ($archive) use ($term) {
                return [
                    'id' => $archive->id,
                    'index_number' => $archive->index_number,
                    'uraian' => Str::limit($archive->uraian, 50),
                    'category' => $archive->category->name,
                    'classification' => $archive->classification->code . ' - ' . $archive->classification->name,
                    'status' => $archive->status,
                    'url' => route('admin.archives.show', $archive),
                    'highlighted_uraian' => $this->highlightSearchTerm($archive->uraian, $term)
                ];
            });
        
        return response()->json($archives);
    }
    
    /**
     * Get filter options based on current search
     */
    public function getFilterOptions(Request $request)
    {
        $term = $request->get('term', '');
        
        $query = Archive::query();
        
        if (!empty($term)) {
            $query->search($term);
        }
        
        $categories = $query->clone()
            ->select('categories.id', 'categories.name', DB::raw('COUNT(*) as count'))
            ->join('categories', 'archives.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('count', 'desc')
            ->get();
            
        $statuses = $query->clone()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();
        
        return response()->json([
            'categories' => $categories,
            'statuses' => $statuses
        ]);
    }
    
    /**
     * Highlight search term in text
     */
    private function highlightSearchTerm($text, $term)
    {
        if (empty($term)) {
            return $text;
        }
        
        return preg_replace(
            '/(' . preg_quote($term, '/') . ')/i',
            '<mark class="bg-yellow-200 text-yellow-900 px-1 rounded">$1</mark>',
            $text
        );
    }
    
    /**
     * Export search results
     */
    public function exportResults(Request $request)
    {
        // This will integrate with the existing Excel export functionality
        // For now, redirect to the export form with search parameters
        return redirect()->route('admin.archives.export-form', ['status' => 'all'])
            ->with('search_params', $request->all());
    }
} 