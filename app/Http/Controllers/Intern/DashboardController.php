<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display intern dashboard for Mahasiswa Magang
     */
    public function index()
    {
        // Basic archive statistics
        $totalArchives = Archive::count();
        $activeArchives = Archive::aktif()->count();
        $inactiveArchives = Archive::inaktif()->count();
        $permanentArchives = Archive::permanen()->count();
        $destroyedArchives = Archive::musnah()->count();

        // My contributions this month
        $thisMonthArchives = Archive::where('created_by', auth()->id())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Total archives I've created
        $myTotalArchives = Archive::where('created_by', auth()->id())->count();

        // Recent archives I've worked on
        $myRecentArchives = Archive::with(['category', 'classification'])
            ->where('created_by', auth()->id())
            ->orWhere('updated_by', auth()->id())
            ->latest()
            ->take(8)
            ->get();

        // Recent system archives (for learning/reference)
        $recentArchives = Archive::with(['category', 'classification', 'createdByUser'])
            ->latest()
            ->take(5)
            ->get();

        // Master Data Counts (for reference)
        $categoryCount = Category::count();
        $classificationCount = Classification::count();

        // Learning statistics
        $weeklyContribution = Archive::where('created_by', auth()->id())
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $todayContribution = Archive::where('created_by', auth()->id())
            ->whereDate('created_at', today())
            ->count();

        return view('intern.dashboard', compact(
            'totalArchives',
            'activeArchives', 
            'inactiveArchives',
            'permanentArchives',
            'destroyedArchives',
            'thisMonthArchives',
            'myTotalArchives',
            'myRecentArchives',
            'recentArchives',
            'categoryCount',
            'classificationCount',
            'weeklyContribution',
            'todayContribution'
        ));
    }
}
