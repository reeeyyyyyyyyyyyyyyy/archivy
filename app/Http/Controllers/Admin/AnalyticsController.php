<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard
     */
    public function index()
    {
        // Get basic statistics
        $totalArchives = Archive::count();
        $totalCategories = Category::count();
        $totalClassifications = Classification::count();
        $totalUsers = User::count();

        // Archive status distribution
        $statusDistribution = Archive::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Monthly archive creation trend (last 12 months)
        $monthlyTrend = Archive::select(
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Category distribution
        $categoryDistribution = Archive::with('category')
            ->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->category->name ?? 'Unknown',
                    'count' => $item->count
                ];
            });

        // Top users by archive count
        $topUsers = Archive::with('createdByUser')
            ->select('created_by', DB::raw('count(*) as count'))
            ->groupBy('created_by')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->createdByUser->name ?? 'Unknown',
                    'count' => $item->count
                ];
            });

        // Archive status transition analysis (only approaching, no overdue since system is automated)
        $statusTransitions = [
            'approaching_inactive' => Archive::where('status', 'Aktif')
                ->whereDate('transition_active_due', '<=', Carbon::now()->addDays(30))
                ->whereDate('transition_active_due', '>', Carbon::now())
                ->count(),
            'approaching_permanent' => Archive::where('status', 'Inaktif')
                ->whereDate('transition_inactive_due', '<=', Carbon::now()->addDays(30))
                ->whereDate('transition_inactive_due', '>', Carbon::now())
                ->count(),
        ];

        // Yearly archive distribution
        $yearlyDistribution = Archive::select(
                DB::raw('EXTRACT(YEAR FROM kurun_waktu_start) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->limit(10)
            ->get();

        // Retention analysis
        $retentionAnalysis = Archive::select(
                'retention_active',
                'retention_inactive',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('retention_active', 'retention_inactive')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Recent activity (last 30 days)
        $recentActivity = Archive::with(['category', 'classification', 'createdByUser'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Performance metrics
        $performanceMetrics = [
            'avg_archives_per_day' => Archive::where('created_at', '>=', Carbon::now()->subDays(30))
                ->count() / 30,
            'most_active_day' => Archive::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('count', 'desc')
                ->first(),
            'completion_rate' => $this->calculateCompletionRate(),
        ];

        return view('admin.analytics.dashboard', compact(
            'totalArchives',
            'totalCategories', 
            'totalClassifications',
            'totalUsers',
            'statusDistribution',
            'monthlyTrend',
            'categoryDistribution',
            'topUsers',
            'statusTransitions',
            'yearlyDistribution',
            'retentionAnalysis',
            'recentActivity',
            'performanceMetrics'
        ));
    }

    /**
     * Get archive data for API calls
     */
    public function getArchiveData(Request $request)
    {
        $type = $request->get('type', 'status');
        
        switch ($type) {
            case 'status':
                return $this->getStatusData();
            case 'monthly':
                return $this->getMonthlyData();
            case 'category':
                return $this->getCategoryData();
            case 'retention':
                return $this->getRetentionData();
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }

    /**
     * Calculate completion rate (archives that have proper metadata)
     */
    private function calculateCompletionRate()
    {
        $totalArchives = Archive::count();
        if ($totalArchives === 0) return 0;

        $completeArchives = Archive::whereNotNull('index_number')
            ->whereNotNull('uraian')
            ->whereNotNull('category_id')
            ->whereNotNull('classification_id')
            ->count();

        return round(($completeArchives / $totalArchives) * 100, 2);
    }

    /**
     * Get status distribution data
     */
    private function getStatusData()
    {
        return Archive::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
    }

    /**
     * Get monthly trend data
     */
    private function getMonthlyData()
    {
        return Archive::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get category distribution data
     */
    private function getCategoryData()
    {
        return Archive::with('category')
            ->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->category->name ?? 'Unknown',
                    'count' => $item->count
                ];
            });
    }

    /**
     * Get retention pattern data
     */
    private function getRetentionData()
    {
        return Archive::select(
                DB::raw('CONCAT(retention_active, "+", retention_inactive) as pattern'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('retention_active', 'retention_inactive')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Export analytics report
     */
    public function exportPdf(Request $request)
    {
        // Gather comprehensive analytics data
        $data = [
            'generated_at' => Carbon::now(),
            'period' => Carbon::now()->format('F Y'),
            'total_archives' => Archive::count(),
            'total_categories' => Category::count(),
            'total_classifications' => Classification::count(),
            'total_users' => User::count(),
            
            // Status distribution with totals
            'status_distribution' => Archive::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
                
            // Monthly trends (last 12 months)
            'monthly_trends' => Archive::select(
                    DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                    DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get(),
                
            // Category distribution (all categories)
            'category_distribution' => Category::withCount('archives')
                ->orderBy('archives_count', 'desc')
                ->get(),
                
            // Performance metrics
            'performance_metrics' => [
                'avg_archives_per_day' => Archive::where('created_at', '>=', Carbon::now()->subDays(30))->count() / 30,
                'completion_rate' => $this->calculateCompletionRate(),
                'most_active_month' => Archive::select(
                        DB::raw('TO_CHAR(created_at, \'Month YYYY\') as month'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->where('created_at', '>=', Carbon::now()->subMonths(12))
                    ->groupBy(DB::raw('TO_CHAR(created_at, \'Month YYYY\')'))
                    ->orderBy('count', 'desc')
                    ->first(),
            ],
            
            // System health
            'system_health' => [
                'database_size' => Archive::count(),
                'last_archive' => Archive::latest()->first(),
                'oldest_archive' => Archive::oldest()->first(),
            ],
            
            // Status transitions
            'status_transitions' => [
                'approaching_inactive' => Archive::where('status', 'Aktif')
                    ->whereDate('transition_active_due', '<=', Carbon::now()->addDays(30))
                    ->whereDate('transition_active_due', '>', Carbon::now())
                    ->count(),
                'approaching_permanent' => Archive::where('status', 'Inaktif')
                    ->whereDate('transition_inactive_due', '<=', Carbon::now()->addDays(30))
                    ->whereDate('transition_inactive_due', '>', Carbon::now())
                    ->count(),
            ]
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.analytics.report-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => public_path(),
            ]);

        $filename = 'laporan-analytics-arsipin-' . Carbon::now()->format('Y-m-d-H-i') . '.pdf';

        return $pdf->download($filename);
    }
}
