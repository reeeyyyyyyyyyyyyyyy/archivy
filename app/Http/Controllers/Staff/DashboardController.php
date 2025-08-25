<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display staff dashboard for Pegawai TU
     */
    public function index()
    {
        // Archive statistics
        $totalArchives = Archive::count();
        $activeArchives = Archive::aktif()->count();
        $inactiveArchives = Archive::inaktif()->count();
        $permanentArchives = Archive::permanen()->count();
        $destroyedArchives = Archive::musnah()->count();

        // Recent activities - archives created this month (PERSONAL)
        $thisMonthArchives = Archive::where('created_by', auth()->id())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Weekly archives created by this staff (PERSONAL)
        $thisWeekArchives = Archive::where('created_by', auth()->id())
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Archives approaching retention (next 30 days)
        $nearRetention = Archive::where('transition_active_due', '<=', now()->addDays(30))
            ->where('status', 'Aktif')
            ->count();

        // Recent archives (for staff view)
        $recentArchives = Archive::with(['category', 'classification', 'createdByUser'])
            ->latest()
            ->take(8)
            ->get();

        // Monthly creation data for chart
        $monthlyData = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Archive::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $monthlyData->push([
                'month' => $date->format('M Y'),
                'count' => $count
            ]);
        }

        // Master Data Counts
        $categoryCount = Category::count();
        $classificationCount = Classification::count();

        // Staff-specific data
        $myArchives = Archive::where('created_by', auth()->id())->count();
        $myRecentArchives = Archive::where('created_by', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('staff.dashboard', compact(
            'totalArchives',
            'activeArchives',
            'inactiveArchives',
            'permanentArchives',
            'destroyedArchives',
            'thisMonthArchives',
            'thisWeekArchives',
            'nearRetention',
            'recentArchives',
            'monthlyData',
            'categoryCount',
            'classificationCount',
            'myArchives',
            'myRecentArchives'
        ));
    }
}
