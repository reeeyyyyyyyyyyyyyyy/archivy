<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show retention report dashboard
     */
    public function retentionDashboard(Request $request)
    {
        $period = (int) $request->get('period', 30); // Cast to int - Default 30 days
        $today = today();

        // Get archives approaching active transition (Aktif -> Inaktif)
        $approachingInactive = Archive::aktif()
            ->whereBetween('transition_active_due', [$today, $today->copy()->addDays($period)])
            ->with(['category', 'classification'])
            ->orderBy('transition_active_due')
            ->get();

        // Get archives approaching final transition (Inaktif -> Permanen/Musnah)
        $approachingFinal = Archive::inaktif()
            ->whereBetween('transition_inactive_due', [$today, $today->copy()->addDays($period)])
            ->with(['category', 'classification'])
            ->orderBy('transition_inactive_due')
            ->get();

        // Summary statistics
        $stats = [
            'total_archives' => Archive::count(),
            'aktif' => Archive::aktif()->count(),
            'inaktif' => Archive::inaktif()->count(),
            'permanen' => Archive::permanen()->count(),
            'musnah' => Archive::musnah()->count(),
            'approaching_inactive' => $approachingInactive->count(),
            'approaching_final' => $approachingFinal->count(),
        ];

        // Monthly transition trends (last 12 months)
        $monthlyTrends = DB::table('archives')
            ->select(
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('COUNT(*) as total'),
                'status'
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month', 'status')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Archives by category for pie chart
        $archivesByCategory = Archive::select('categories.nama_kategori', DB::raw('COUNT(*) as count'))
            ->join('categories', 'archives.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.nama_kategori')
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.reports.retention-dashboard', compact(
            'approachingInactive',
            'approachingFinal',
            'stats',
            'period',
            'monthlyTrends',
            'archivesByCategory'
        ));
    }

    /**
     * Get retention alerts via AJAX
     */
    public function retentionAlerts(Request $request)
    {
        $period = (int) $request->get('period', 30); // Cast to int
        $type = $request->get('type', 'all'); // all, inactive, final
        $today = today();

        $alerts = collect();

        if ($type === 'all' || $type === 'inactive') {
            $inactiveAlerts = Archive::aktif()
                ->whereBetween('transition_active_due', [$today, $today->copy()->addDays($period)])
                ->with(['category', 'classification'])
                ->get()
                ->map(function ($archive) use ($today) {
                    return [
                        'id' => $archive->id,
                        'type' => 'Transisi ke Inaktif',
                        'index_number' => $archive->index_number,
                        'uraian' => $archive->description,
                        'category' => $archive->category->nama_kategori,
                        'current_status' => 'Aktif',
                        'next_status' => 'Inaktif',
                        'due_date' => $archive->transition_active_due,
                        'days_remaining' => $today->diffInDays($archive->transition_active_due, false),
                        'priority' => $this->getPriority($today->diffInDays($archive->transition_active_due, false))
                    ];
                });

            $alerts = $alerts->merge($inactiveAlerts);
        }

        if ($type === 'all' || $type === 'final') {
            $finalAlerts = Archive::inaktif()
                ->whereBetween('transition_inactive_due', [$today, $today->copy()->addDays($period)])
                ->with(['category', 'classification'])
                ->get()
                ->map(function ($archive) use ($today) {
                    $finalStatus = match (true) {
                        str_starts_with($archive->category->nasib_akhir, 'Musnah') => 'Musnah',
                        $archive->category->nasib_akhir === 'Permanen' => 'Permanen',
                        default => 'Permanen'
                    };

                    return [
                        'id' => $archive->id,
                        'type' => 'Transisi ke ' . $finalStatus,
                        'index_number' => $archive->index_number,
                        'uraian' => $archive->description,
                        'category' => $archive->category->nama_kategori,
                        'current_status' => 'Inaktif',
                        'next_status' => $finalStatus,
                        'due_date' => $archive->transition_inactive_due,
                        'days_remaining' => $today->diffInDays($archive->transition_inactive_due, false),
                        'priority' => $this->getPriority($today->diffInDays($archive->transition_inactive_due, false)),
                        'nasib_akhir' => $archive->classification->nasib_akhir ?? $archive->category->nasib_akhir ?? 'N/A',
                    ];
                });

            $alerts = $alerts->merge($finalAlerts);
        }

        // Sort by days remaining (most urgent first)
        $alerts = $alerts->sortBy('days_remaining');

        return response()->json($alerts->values());
    }

    /**
     * Export retention report to Excel
     */
    public function exportRetentionReport(Request $request)
    {
        $period = (int) $request->get('period', 30); // Cast to int
        $type = $request->get('type', 'all');

        // This will be implemented with Excel export class
        // For now, return JSON for testing
        $alerts = $this->retentionAlerts($request)->getData();

        $fileName = 'laporan-retensi-' . $period . 'hari-' . date('Y-m-d') . '.json';

        return response()->json($alerts)
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Get priority level based on days remaining
     */
    private function getPriority($daysRemaining): string
    {
        if ($daysRemaining <= 7) {
            return 'critical'; // Red - 1 week or less
        } elseif ($daysRemaining <= 30) {
            return 'high'; // Orange - 1 month or less
        } elseif ($daysRemaining <= 60) {
            return 'medium'; // Yellow - 2 months or less
        } else {
            return 'low'; // Green - More than 2 months
        }
    }

    /**
     * Get retention summary for dashboard widgets
     */
    public function retentionSummary()
    {
        $today = today();

        $summary = [
            'overdue' => Archive::where('transition_active_due', '<', $today)
                ->where('status', 'Aktif')
                ->count() +
                Archive::where('transition_inactive_due', '<', $today)
                ->where('status', 'Inaktif')
                ->count(),

            'due_this_week' => Archive::whereBetween('transition_active_due', [$today, $today->copy()->addDays(7)])
                ->where('status', 'Aktif')
                ->count() +
                Archive::whereBetween('transition_inactive_due', [$today, $today->copy()->addDays(7)])
                ->where('status', 'Inaktif')
                ->count(),

            'due_this_month' => Archive::whereBetween('transition_active_due', [$today, $today->copy()->addDays(30)])
                ->where('status', 'Aktif')
                ->count() +
                Archive::whereBetween('transition_inactive_due', [$today, $today->copy()->addDays(30)])
                ->where('status', 'Inaktif')
                ->count(),

            'manual_overrides' => Archive::where('manual_status_override', true)->count(),
        ];

        return response()->json($summary);
    }
}
