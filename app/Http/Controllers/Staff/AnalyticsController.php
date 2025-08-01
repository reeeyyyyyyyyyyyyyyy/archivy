<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard for staff
     */
    public function index()
    {
        $user = Auth::user();

        // Get basic statistics
        $totalArchives = Archive::count();
        $myContributions = Archive::where('created_by', $user->id)->count();
        $thisMonthArchives = Archive::where('created_by', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Calculate average processing time (mock data for now)
        $avgProcessingTime = '2.5';

        // My archive status distribution
        $myStatusDistribution = Archive::where('created_by', $user->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // My monthly performance trend (last 12 months)
        $myMonthlyTrend = Archive::where('created_by', $user->id)
            ->select(
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // My category distribution
        $myCategoryDistribution = Archive::where('created_by', $user->id)
            ->with('category')
            ->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->category->nama_kategori ?? 'Unknown',
                    'count' => $item->count
                ];
            });

        // My active archives
        $myActiveArchives = Archive::where('created_by', $user->id)
            ->where('status', 'Aktif')
            ->count();

        // My recent archives (last 7 days)
        $myRecentArchives = Archive::where('created_by', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        // My productivity score (based on monthly average)
        $monthlyAverage = Archive::where('created_by', $user->id)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->count() / 6;

        $productivityScore = $thisMonthArchives >= $monthlyAverage ? 'Excellent' : 'Good';

        // Top users (staff and intern only)
        $topUsers = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['staff', 'intern']);
            })
            ->withCount(['archives' => function($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonths(6));
            }])
            ->orderBy('archives_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'count' => $user->archives_count
                ];
            });

        // Performance metrics
        $performanceMetrics = $this->getPerformanceMetrics($user);

        // Recent activity (last 30 days)
        $recentActivity = Archive::with(['category', 'createdByUser'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('staff.analytics.dashboard', compact(
            'totalArchives',
            'myContributions',
            'thisMonthArchives',
            'avgProcessingTime',
            'myStatusDistribution',
            'myMonthlyTrend',
            'myCategoryDistribution',
            'myActiveArchives',
            'myRecentArchives',
            'productivityScore',
            'topUsers',
            'performanceMetrics',
            'recentActivity'
        ));
    }

    /**
     * Get performance metrics for the user
     */
    private function getPerformanceMetrics($user)
    {
        // Average archives per day (last 30 days)
        $avgArchivesPerDay = Archive::where('created_by', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count() / 30;

        // Completion rate (archives with complete metadata)
        $totalMyArchives = Archive::where('created_by', $user->id)->count();
        $completeArchives = Archive::where('created_by', $user->id)
            ->whereNotNull('index_number')
            ->whereNotNull('description')
            ->whereNotNull('category_id')
            ->count();

        $completionRate = $totalMyArchives > 0 ? round(($completeArchives / $totalMyArchives) * 100, 1) : 0;

        // Most active day
        $mostActiveDay = Archive::where('created_by', $user->id)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subMonths(1))
            ->groupBy('date')
            ->orderBy('count', 'desc')
            ->first();

        return [
            'avg_archives_per_day' => round($avgArchivesPerDay, 1),
            'completion_rate' => $completionRate,
            'most_active_day' => $mostActiveDay
        ];
    }

    /**
     * Get archive data for charts
     */
    public function getArchiveData(Request $request)
    {
        $user = Auth::user();

        $data = [
            'status' => $this->getMyStatusData($user),
            'monthly' => $this->getMyMonthlyData($user),
            'category' => $this->getMyCategoryData($user),
        ];

        return response()->json($data);
    }

    /**
     * Get my status distribution data
     */
    private function getMyStatusData($user)
    {
        $statusData = Archive::where('created_by', $user->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            'Aktif' => '#10B981',
            'Inaktif' => '#F59E0B',
            'Permanen' => '#8B5CF6',
            'Musnah' => '#EF4444'
        ];

        foreach ($statusData as $item) {
            $labels[] = $item->status;
            $data[] = $item->count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_values($colors)
        ];
    }

    /**
     * Get my monthly performance data
     */
    private function getMyMonthlyData($user)
    {
        $monthlyData = Archive::where('created_by', $user->id)
            ->select(
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels = [];
        $data = [];

        foreach ($monthlyData as $item) {
            $date = Carbon::createFromDate($item->year, $item->month, 1);
            $labels[] = $date->format('M Y');
            $data[] = $item->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get my category distribution data
     */
    private function getMyCategoryData($user)
    {
        $categoryData = Archive::where('created_by', $user->id)
            ->with('category')
            ->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];

        foreach ($categoryData as $item) {
            $labels[] = $item->category->name ?? 'Unknown';
            $data[] = $item->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
