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

        if (!$user->isAdmin()) {
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

        if (!$user->isAdmin()) {
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
            'new_status' => 'required|in:Aktif,Inaktif,Permanen,Musnah',
            'remarks' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();

        $query = Archive::query();

        if (!$user->isAdmin()) {
            $query->where('created_by', $user->id);
        }

        $archive = $query->findOrFail($id);

        DB::transaction(function() use ($archive, $request, $user) {
            $oldStatus = $archive->status;

            $archive->update([
                'status' => $request->new_status,
                'manual_status_override' => true,
                'manual_override_at' => now(),
                'manual_override_by' => $user->id,
                'updated_by' => $user->id,
                'ket' => $request->remarks ?
                    ($archive->ket ? $archive->ket . "\n\nRe-evaluation: " . $request->remarks : "Re-evaluation: " . $request->remarks) :
                    $archive->ket
            ]);
        });

        return redirect()->route('re-evaluation.index')
            ->with('success', "Status arsip {$archive->index_number} berhasil diubah ke {$request->new_status}");
    }

    /**
     * Bulk status update for multiple archives
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array',
            'archive_ids.*' => 'exists:archives,id',
            'new_status' => 'required|in:Aktif,Inaktif,Permanen,Musnah',
            'remarks' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();

        $query = Archive::whereIn('id', $request->archive_ids)
            ->dinilaiKembali();

        if (!$user->isAdmin()) {
            $query->where('created_by', $user->id);
        }

        $archives = $query->get();

        DB::transaction(function() use ($archives, $request, $user) {
            foreach ($archives as $archive) {
                $oldStatus = $archive->status;

                $archive->update([
                    'status' => $request->new_status,
                    'manual_status_override' => true,
                    'manual_override_at' => now(),
                    'manual_override_by' => $user->id,
                    'updated_by' => $user->id,
                    'ket' => $request->remarks ?
                        ($archive->ket ? $archive->ket . "\n\nBulk Re-evaluation: " . $request->remarks : "Bulk Re-evaluation: " . $request->remarks) :
                        $archive->ket
                ]);
            }
        });

        return redirect()->route('re-evaluation.index')
            ->with('success', "Status untuk " . count($archives) . " arsip berhasil diubah ke {$request->new_status}");
    }
}
