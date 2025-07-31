<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReEvaluationController extends Controller
{
    /**
     * Display archives that are marked for re-evaluation
     */
    public function index()
    {
        $user = Auth::user();

        // Admin can see all re-evaluation archives, others see only their own
        $query = Archive::with(['category', 'classification', 'createdByUser'])
            ->dinilaiKembali();

        if (!$user->hasRole('admin')) {
            $query->where('created_by', $user->id);
        }

        $archives = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.re-evaluation.index', compact('archives'));
    }

    /**
     * Show detailed view of re-evaluation archive
     */
    public function show($id)
    {
        $user = Auth::user();

        $query = Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser']);

        if (!$user->hasRole('admin')) {
            $query->where('created_by', $user->id);
        }

        $archive = $query->findOrFail($id);

        return view('admin.re-evaluation.show', compact('archive'));
    }

    /**
     * Update status of re-evaluation archive
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aktif,permanen,musnah',
        ]);

        $user = Auth::user();

        $query = Archive::query();

        if (!$user->hasRole('admin')) {
            $query->where('created_by', $user->id);
        }

        $archive = $query->findOrFail($id);

        try {
            $newStatus = ucfirst($request->status); // Capitalize first letter

            DB::transaction(function() use ($archive, $newStatus, $user) {
                $archive->update([
                    'status' => $newStatus,
                    'manual_status_override' => true,
                    'manual_override_at' => now(),
                    'manual_override_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => "Status arsip berhasil diubah menjadi {$newStatus}",
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk status update for multiple archives
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array',
            'archive_ids.*' => 'exists:archives,id',
            'status' => 'required|in:aktif,permanen,musnah',
        ]);

        $user = Auth::user();

        $query = Archive::whereIn('id', $request->archive_ids)
            ->dinilaiKembali();

        if (!$user->hasRole('admin')) {
            $query->where('created_by', $user->id);
        }

        $archives = $query->get();

        if ($archives->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada arsip yang dapat diubah statusnya'
            ], 400);
        }

        try {
            $newStatus = ucfirst($request->status);
            $updatedCount = 0;

            DB::transaction(function() use ($archives, $newStatus, $user, &$updatedCount) {
                foreach ($archives as $archive) {
                    $archive->update([
                        'status' => $newStatus,
                        'manual_status_override' => true,
                        'manual_override_at' => now(),
                        'manual_override_by' => $user->id,
                        'updated_by' => $user->id,
                    ]);
                    $updatedCount++;
                }
            });

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengubah status {$updatedCount} arsip menjadi {$newStatus}",
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}
