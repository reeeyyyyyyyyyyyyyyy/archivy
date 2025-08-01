<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Archive;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Archive Statistics
        $totalArchives = Archive::count();
        $activeArchives = Archive::where('status', 'Aktif')->count();
        $inactiveArchives = Archive::where('status', 'Inaktif')->count();
        $permanentArchives = Archive::where('status', 'Permanen')->count();
        $destroyedArchives = Archive::where('status', 'Musnah')->count();
        
        // This Month Archives
        $thisMonthArchives = Archive::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();
        
        // Archives approaching retention (next 30 days)
        $nearRetention = Archive::where('status', 'Aktif')
                               ->where('transition_active_due', '<=', now()->addDays(30))
                               ->where('transition_active_due', '>', now())
                               ->count();
        
        // Recent Archives (last 10)
        $recentArchives = Archive::with(['category', 'classification', 'createdByUser'])
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
        
        // Monthly Data for Chart (current year)
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $count = Archive::whereMonth('created_at', $i)
                           ->whereYear('created_at', now()->year)
                           ->count();
            $monthlyData[] = $count;
        }
        
        // Master Data Counts
        $categoryCount = Category::count();
        $classificationCount = Classification::count();

        return view('admin.dashboard', compact(
            'totalArchives',
            'activeArchives', 
            'inactiveArchives',
            'permanentArchives',
            'destroyedArchives',
            'thisMonthArchives',
            'nearRetention',
            'recentArchives',
            'monthlyData',
            'categoryCount',
            'classificationCount'
        ));
    }
}
