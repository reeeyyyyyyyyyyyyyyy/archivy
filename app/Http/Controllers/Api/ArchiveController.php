<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('index_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Role-based filtering
        $user = Auth::user();
        if ($user->role_type === 'intern') {
            $query->where('created_by', $user->id);
        } elseif ($user->role_type === 'staff') {
            $query->whereIn('created_by', [$user->id] + User::role('intern')->pluck('id')->toArray());
        }

        $archives = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $archives,
            'message' => 'Arsip berhasil diambil'
        ]);
    }

    public function show(Archive $archive)
    {
        $archive->load(['category', 'classification', 'createdByUser', 'updatedByUser']);

        return response()->json([
            'success' => true,
            'data' => $archive,
            'message' => 'Detail arsip berhasil diambil'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'classification_id' => 'required|exists:classifications,id',
            'description' => 'required|string|max:255',
            'index_number' => 'required|string',
            'kurun_waktu_start' => 'required|date',
            'jumlah_berkas' => 'required|integer|min:1',
            'is_manual_input' => 'boolean',
            'manual_retention_aktif' => 'integer|min:0',
            'manual_retention_inaktif' => 'integer|min:0',
            'manual_nasib_akhir' => 'string',
        ]);

        try {
            $classification = Classification::with('category')->findOrFail($request->classification_id);
            $category = $classification->category;

            $isManualInput = $request->is_manual_input ?? false;

            // Handle index number
            if ($isManualInput || $classification->code === 'LAINNYA') {
                $indexNumber = $request->index_number;
            } else {
                $userInput = $request->index_number;
                $indexNumber = $this->generateAutoIndexNumber($classification, $userInput, $request->kurun_waktu_start);
            }

            // Handle retention values
            $retentionAktif = $isManualInput ?
                (int)($request->manual_retention_aktif ?? 0) :
                (int)$classification->retention_aktif;

            $retentionInaktif = $isManualInput ?
                (int)($request->manual_retention_inaktif ?? 0) :
                (int)$classification->retention_inaktif;

            // Calculate transition dates
            $kurunWaktuStart = \Carbon\Carbon::parse($request->kurun_waktu_start);
            $transitionActiveDue = $kurunWaktuStart->copy()->addYears($retentionAktif);
            $transitionInactiveDue = $transitionActiveDue->copy()->addYears($retentionInaktif);

            $archive = Archive::create([
                'category_id' => $category->id,
                'classification_id' => $classification->id,
                'index_number' => $indexNumber,
                'description' => $request->description,
                'kurun_waktu_start' => $request->kurun_waktu_start,
                'jumlah_berkas' => $request->jumlah_berkas,
                'is_manual_input' => $isManualInput,
                'manual_retention_aktif' => $request->manual_retention_aktif,
                'manual_retention_inaktif' => $request->manual_retention_inaktif,
                'manual_nasib_akhir' => $request->manual_nasib_akhir,
                'retention_aktif' => $retentionAktif,
                'retention_inaktif' => $retentionInaktif,
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'status' => 'Aktif',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $archive->load(['category', 'classification', 'createdByUser']);

            return response()->json([
                'success' => true,
                'data' => $archive,
                'message' => 'Arsip berhasil dibuat'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat arsip: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Archive $archive)
    {
        $request->validate([
            'classification_id' => 'required|exists:classifications,id',
            'description' => 'required|string|max:255',
            'index_number' => 'required|string',
            'kurun_waktu_start' => 'required|date',
            'jumlah_berkas' => 'required|integer|min:1',
        ]);

        try {
            $classification = Classification::with('category')->findOrFail($request->classification_id);
            $category = $classification->category;

            $isManualInput = $request->is_manual_input ?? false;

            // Handle index number
            if ($isManualInput || $classification->code === 'LAINNYA') {
                $indexNumber = $request->index_number;
            } else {
                $userInput = $request->index_number;
                $indexNumber = $this->generateAutoIndexNumber($classification, $userInput, $request->kurun_waktu_start);
            }

            // Handle retention values
            $retentionAktif = $isManualInput ?
                (int)($request->manual_retention_aktif ?? 0) :
                (int)$classification->retention_aktif;

            $retentionInaktif = $isManualInput ?
                (int)($request->manual_retention_inaktif ?? 0) :
                (int)$classification->retention_inaktif;

            // Calculate transition dates
            $kurunWaktuStart = \Carbon\Carbon::parse($request->kurun_waktu_start);
            $transitionActiveDue = $kurunWaktuStart->copy()->addYears($retentionAktif);
            $transitionInactiveDue = $transitionActiveDue->copy()->addYears($retentionInaktif);

            $archive->update([
                'category_id' => $category->id,
                'classification_id' => $classification->id,
                'index_number' => $indexNumber,
                'description' => $request->description,
                'kurun_waktu_start' => $request->kurun_waktu_start,
                'jumlah_berkas' => $request->jumlah_berkas,
                'retention_aktif' => $retentionAktif,
                'retention_inaktif' => $retentionInaktif,
                'transition_active_due' => $transitionActiveDue,
                'transition_inactive_due' => $transitionInactiveDue,
                'updated_by' => Auth::id(),
            ]);

            $archive->load(['category', 'classification', 'createdByUser']);

            return response()->json([
                'success' => true,
                'data' => $archive,
                'message' => 'Arsip berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui arsip: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Archive $archive)
    {
        try {
            $archive->delete();

            return response()->json([
                'success' => true,
                'message' => 'Arsip berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus arsip: ' . $e->getMessage()
            ], 500);
        }
    }

    public function categories()
    {
        $categories = Category::orderBy('nama_kategori')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Kategori berhasil diambil'
        ]);
    }

    public function classifications(Request $request)
    {
        $query = Classification::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $classifications = $query->orderBy('nama_klasifikasi')->get();

        return response()->json([
            'success' => true,
            'data' => $classifications,
            'message' => 'Klasifikasi berhasil diambil'
        ]);
    }

    private function generateAutoIndexNumber($classification, $userInput, $kurunWaktuStart)
    {
        $year = \Carbon\Carbon::parse($kurunWaktuStart)->format('Y');
        return $classification->code . '/' . $userInput . '/' . $year;
    }
}
