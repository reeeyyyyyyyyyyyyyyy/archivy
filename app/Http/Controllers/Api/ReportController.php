<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard()
    {
        $totalArchives = Archive::count();
        $activeArchives = Archive::where('status', 'Aktif')->count();
        $inactiveArchives = Archive::where('status', 'Inaktif')->count();
        $permanentArchives = Archive::where('status', 'Permanen')->count();
        $destroyedArchives = Archive::where('status', 'Musnah')->count();

        $statusDistribution = [
            'Aktif' => $activeArchives,
            'Inaktif' => $inactiveArchives,
            'Permanen' => $permanentArchives,
            'Musnah' => $destroyedArchives
        ];

        $categoryDistribution = Archive::with('category')
            ->select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->nama_kategori,
                    'total' => $item->total
                ];
            });

        $recentArchives = Archive::with(['category', 'classification', 'createdByUser'])
            ->latest()
            ->take(5)
            ->get();

        $expiringSoon = Archive::where('status', 'Aktif')
            ->where('transition_active_due', '<=', now()->addDays(30))
            ->where('transition_active_due', '>', now())
            ->with(['category', 'classification'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_archives' => $totalArchives,
                'status_distribution' => $statusDistribution,
                'category_distribution' => $categoryDistribution,
                'recent_archives' => $recentArchives,
                'expiring_soon' => $expiringSoon
            ],
            'message' => 'Dashboard data berhasil diambil'
        ]);
    }

    public function retentionReport()
    {
        $expiring30Days = Archive::where('status', 'Aktif')
            ->where('transition_active_due', '<=', now()->addDays(30))
            ->where('transition_active_due', '>', now())
            ->with(['category', 'classification'])
            ->get();

        $expiring7Days = Archive::where('status', 'Aktif')
            ->where('transition_active_due', '<=', now()->addDays(7))
            ->where('transition_active_due', '>', now())
            ->with(['category', 'classification'])
            ->get();

        $expiringToday = Archive::where('status', 'Aktif')
            ->whereDate('transition_active_due', today())
            ->with(['category', 'classification'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'expiring_30_days' => $expiring30Days,
                'expiring_7_days' => $expiring7Days,
                'expiring_today' => $expiringToday
            ],
            'message' => 'Retention report berhasil diambil'
        ]);
    }

    public function categoryReport()
    {
        $categoryStats = Category::withCount('archives')
            ->withCount(['archives as aktif_count' => function ($query) {
                $query->where('status', 'Aktif');
            }])
            ->withCount(['archives as inaktif_count' => function ($query) {
                $query->where('status', 'Inaktif');
            }])
            ->withCount(['archives as permanen_count' => function ($query) {
                $query->where('status', 'Permanen');
            }])
            ->withCount(['archives as musnah_count' => function ($query) {
                $query->where('status', 'Musnah');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categoryStats,
            'message' => 'Category report berhasil diambil'
        ]);
    }

    public function yearlyReport(Request $request)
    {
        $year = $request->get('year', now()->year);

        $yearlyStats = Archive::whereYear('created_at', $year)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $monthlyStats = Archive::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'yearly_stats' => $yearlyStats,
                'monthly_stats' => $monthlyStats
            ],
            'message' => 'Yearly report berhasil diambil'
        ]);
    }
}
