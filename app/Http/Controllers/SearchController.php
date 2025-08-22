<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
        $statuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah', 'Dinilai Kembali'];

        // Initialize empty archives collection for initial load
        $archives = collect();

        $user = Auth::user();
        $viewPath = $user->hasRole('admin') ? 'admin.search.index' :
                   ($user->hasRole('staff') ? 'staff.search.index' : 'intern.search.index');

        return view($viewPath, compact('archives', 'categories', 'classifications', 'users', 'statuses'));
    }

    public function search(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser']);

        // Apply search term
        if ($request->filled('term')) {
            $term = $request->term;
            $query->where(function($q) use ($term) {
                $q->where('index_number', 'ILIKE', "%{$term}%")
                  ->orWhere('description', 'ILIKE', "%{$term}%")
                  ->orWhereHas('category', function($subQ) use ($term) {
                      $subQ->where('nama_kategori', 'ILIKE', "%{$term}%");
                  })
                  ->orWhereHas('classification', function($subQ) use ($term) {
                      $subQ->where('code', 'ILIKE', "%{$term}%")
                           ->orWhere('nama_klasifikasi', 'ILIKE', "%{$term}%");
                  });
            });
        }

        // Apply filters
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
            $query->whereDate('kurun_waktu_start', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('kurun_waktu_start', '<=', $request->date_to);
        }

        // Special filter: Approaching Transition
        if ($request->filled('approaching_transition')) {
            $days = (int) $request->approaching_transition;
            $targetDate = now()->addDays($days);

            $query->where(function($q) use ($targetDate) {
                // Arsip yang akan transisi dari Aktif ke Inaktif
                $q->where('status', 'Aktif')
                  ->whereDate('transition_active_due', '<=', $targetDate)
                  ->whereDate('transition_active_due', '>=', now())

                  // ATAU arsip yang akan transisi dari Inaktif ke status final
                  ->orWhere(function($subQ) use ($targetDate) {
                      $subQ->where('status', 'Inaktif')
                           ->whereDate('transition_inactive_due', '<=', $targetDate)
                           ->whereDate('transition_inactive_due', '>=', now());
                  });
            });
        }

        // Get paginated results
        $archives = $query->orderBy('created_at', 'desc')->paginate(15);

        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::orderBy('code')->get();
        $users = $this->getFilterUsers();
        $statuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah'];

        $user = Auth::user();
        $viewPath = $user->hasRole('admin') ? 'admin.search.index' :
                   ($user->hasRole('staff') ? 'staff.search.index' : 'intern.search.index');

        return view($viewPath, compact('archives', 'categories', 'classifications', 'users', 'statuses'));
    }

    /**
     * Autocomplete search for quick suggestions
     */
    public function autocomplete(Request $request)
    {
        if (!$request->filled('term')) {
            return response()->json([]);
        }

        $term = $request->term;
        $suggestions = Archive::with(['category', 'classification'])
            ->where(function($q) use ($term) {
                $q->where('index_number', 'ILIKE', "%{$term}%")
                  ->orWhere('description', 'ILIKE', "%{$term}%");
            })
            ->limit(10)
            ->get()
            ->map(function($archive) {
                return [
                    'id' => $archive->id,
                    'text' => $archive->index_number . ' - ' . $archive->description,
                    'category' => $archive->category->nama_kategori ?? '-',
                    'status' => $archive->status
                ];
            });

        return response()->json($suggestions);
    }

    /**
     * Export search results to Excel
     */
    public function exportResults(Request $request)
    {
        try {
            $query = Archive::with(['category', 'classification', 'createdByUser']);

            // Apply same filters as search
            if ($request->filled('term')) {
                $term = $request->term;
                $query->where(function($q) use ($term) {
                    $q->where('index_number', 'ILIKE', "%{$term}%")
                      ->orWhere('description', 'ILIKE', "%{$term}%")
                      ->orWhereHas('category', function($subQ) use ($term) {
                          $subQ->where('nama_kategori', 'ILIKE', "%{$term}%");
                      })
                      ->orWhereHas('classification', function($subQ) use ($term) {
                          $subQ->where('code', 'ILIKE', "%{$term}%")
                               ->orWhere('nama_klasifikasi', 'ILIKE', "%{$term}%");
                      });
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
                $query->whereDate('kurun_waktu_start', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('kurun_waktu_start', '<=', $request->date_to);
            }

            $archives = $query->orderBy('created_at', 'desc')->get();

            $fileName = 'hasil-pencarian-arsip-' . date('Y-m-d-His') . '.xlsx';

            return Excel::download(new \App\Exports\ArchiveExportWithHeader('search', $archives), $fileName);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal mengeksport hasil pencarian. Silakan coba lagi.');
        }
    }

    private function getFilterUsers()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return User::orderBy('name')->get();
        } elseif ($user->hasRole('staff')) {
            // Staff can see archives created by staff and intern users
            return User::whereHas('roles', function($q) {
                $q->whereIn('name', ['staff', 'intern']);
            })->orderBy('name')->get();
        } elseif ($user->hasRole('intern')) {
            // Intern can only see archives created by intern users
            return User::whereHas('roles', function($q) {
                $q->where('name', 'intern');
            })->orderBy('name')->get();
        } else {
            return collect(); // Return empty collection for unknown roles
        }
    }
}
