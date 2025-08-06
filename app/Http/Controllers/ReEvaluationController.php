<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReEvaluationController extends Controller
{
    /**
     * Display a listing of re-evaluation archives
     */
    public function index()
    {
        $user = Auth::user();

        // Get archives with status "Dinilai Kembali" that haven't been evaluated yet
        $query = Archive::where('status', 'Dinilai Kembali')
            ->whereNull('evaluation_notes') // Exclude archives that have been evaluated
            ->with(['category', 'classification', 'createdByUser']);

        // Filter based on user role - Staff can see all evaluated archives from admin and staff
        if ($user->role_type === 'intern') {
            $query->whereHas('createdByUser', function($q) {
                $q->where('role_type', 'intern');
            });
        }

        $archives = $query->orderBy('created_at', 'desc')->paginate(25);

        // Get categories and classifications for filtering
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('code')->get();

        // Determine view path based on user role
        $viewPath = $user->role_type === 'admin' ? 'admin.re-evaluation.index' :
                   ($user->role_type === 'staff' ? 'staff.re-evaluation.index' : 'intern.re-evaluation.index');

        return view($viewPath, compact('archives', 'categories', 'classifications'));
    }

    /**
     * Display already evaluated archives
     */
    public function evaluated()
    {
        $user = Auth::user();

        // Get archives that have evaluation notes (have been evaluated)
        $query = Archive::whereNotNull('evaluation_notes')
            ->with(['category', 'classification', 'createdByUser']);

        // Filter based on user role - Staff can see all evaluated archives from admin and staff
        if ($user->role_type === 'intern') {
            $query->whereHas('createdByUser', function($q) {
                $q->where('role_type', 'intern');
            });
        }

        $archives = $query->orderBy('updated_at', 'desc')->paginate(25);

        // Get categories and classifications for filtering
        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('code')->get();

        // Determine view path based on user role
        $viewPath = $user->role_type === 'admin' ? 'admin.re-evaluation.evaluated' :
                   ($user->role_type === 'staff' ? 'staff.re-evaluation.evaluated' : 'intern.re-evaluation.evaluated');

        return view($viewPath, compact('archives', 'categories', 'classifications'));
    }

    /**
     * Display the specified re-evaluation archive
     */
    public function show(Archive $archive)
    {
        // Check if archive is in re-evaluation status
        if ($archive->status !== 'Dinilai Kembali') {
            return redirect()->back()->with('error', 'Arsip ini tidak dalam status Dinilai Kembali');
        }

        $user = Auth::user();

        // Check permissions - Staff can view all re-evaluation archives, intern can only view their own
        if ($user->role_type === 'intern' && $archive->created_by !== $user->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke arsip ini');
        }

        // Get related archives for comparison
        $relatedArchives = Archive::where('classification_id', $archive->classification_id)
            ->where('id', '!=', $archive->id)
            ->where('status', '!=', 'Dinilai Kembali')
            ->limit(5)
            ->get();

        // Determine view path based on user role
        $viewPath = $user->role_type === 'admin' ? 'admin.re-evaluation.show' :
                   ($user->role_type === 'staff' ? 'staff.re-evaluation.show' : 'intern.re-evaluation.show');

        return view($viewPath, compact('archive', 'relatedArchives'));
    }

    /**
     * Update status of re-evaluation archive
     */
    public function updateStatus(Request $request, Archive $archive)
    {
        $request->validate([
            'new_status' => 'required|in:Aktif,Inaktif,Permanen,Musnah',
            'evaluation_notes' => 'nullable|string|max:1000'
        ]);

        // Check if archive is in re-evaluation status
        if ($archive->status !== 'Dinilai Kembali') {
            return response()->json([
                'success' => false,
                'message' => 'Arsip ini tidak dalam status Dinilai Kembali'
            ], 400);
        }

        $user = Auth::user();

        // Check permissions
        if (($user->role_type === 'staff' || $user->role_type === 'intern') &&
            $archive->created_by !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengubah status arsip ini'
            ], 403);
        }

        DB::transaction(function() use ($request, $archive) {
            $oldStatus = $archive->status;

            // Update archive status
                $archive->update([
                'status' => $request->new_status,
                    'manual_status_override' => true,
                    'manual_override_at' => now(),
                'manual_override_by' => Auth::id(),
                'evaluation_notes' => $request->evaluation_notes,
                'updated_by' => Auth::id()
                ]);

            // Log the status change
            Log::info("Re-evaluation archive status updated: Archive ID {$archive->id} changed from {$oldStatus} to {$request->new_status} by user " . Auth::id());
            });

            $user = Auth::user();
            $route = $user->role_type === 'staff' ? 'staff.re-evaluation.evaluated' : 'admin.re-evaluation.evaluated';

            return redirect()->route($route)
                ->with('success', "Status arsip berhasil diubah dari 'Dinilai Kembali' menjadi '{$request->new_status}'");
    }

    /**
     * Bulk update status of re-evaluation archives
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array|min:1',
            'archive_ids.*' => 'exists:archives,id',
            'new_status' => 'required|in:Aktif,Inaktif,Permanen,Musnah',
            'evaluation_notes' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $archiveIds = $request->archive_ids;
        $newStatus = $request->new_status;
        $successCount = 0;
        $errors = [];

        DB::transaction(function() use ($archiveIds, $newStatus, $request, $user, &$successCount, &$errors) {
            foreach ($archiveIds as $archiveId) {
                try {
                    $archive = Archive::find($archiveId);

                    if (!$archive) {
                        $errors[] = "Arsip ID {$archiveId} tidak ditemukan";
                        continue;
                    }

                    // Check if archive is in re-evaluation status
                    if ($archive->status !== 'Dinilai Kembali') {
                        $errors[] = "Arsip ID {$archiveId} tidak dalam status Dinilai Kembali";
                        continue;
                    }

                    // Check permissions
                    if (($user->role_type === 'staff' || $user->role_type === 'intern') &&
                        $archive->created_by !== $user->id) {
                        $errors[] = "Anda tidak memiliki akses untuk mengubah arsip ID {$archiveId}";
                        continue;
                    }

                    $oldStatus = $archive->status;

                    // Update archive status
                    $archive->update([
                        'status' => $newStatus,
                        'manual_status_override' => true,
                        'manual_override_at' => now(),
                        'manual_override_by' => Auth::id(),
                        'evaluation_notes' => $request->evaluation_notes,
                        'updated_by' => Auth::id()
                    ]);

                    $successCount++;

                    // Log the status change
                    Log::info("Bulk re-evaluation status update: Archive ID {$archiveId} changed from {$oldStatus} to {$newStatus} by user " . Auth::id());

                } catch (\Exception $e) {
                    $errors[] = "Error untuk arsip ID {$archiveId}: " . $e->getMessage();
                }
                }
            });

            return response()->json([
            'success' => $successCount > 0,
            'message' => "Berhasil mengubah status {$successCount} arsip menjadi '{$newStatus}'",
            'count' => $successCount,
            'errors' => $errors
        ]);
    }

    /**
     * Export re-evaluation archives
     */
    public function export(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array|min:1',
            'archive_ids.*' => 'exists:archives,id',
            'format' => 'required|in:xlsx,pdf'
        ]);

        $user = Auth::user();
        $archiveIds = $request->archive_ids;
        $format = $request->format;
        $fileName = 're-evaluation-arsip-' . date('Y-m-d-H-i-s') . '.' . $format;

        // Get archives
        $archives = Archive::whereIn('id', $archiveIds)
            ->where('status', 'Dinilai Kembali')
            ->with(['category', 'classification', 'createdByUser'])
            ->get();

        // Check permissions
        if ($user->role_type === 'staff' || $user->role_type === 'intern') {
            $archives = $archives->filter(function($archive) use ($user) {
                return $archive->created_by === $user->id;
            });
        }

        if ($archives->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada arsip yang dapat diexport'
            ], 400);
        }

        // Log the export
        Log::info("Re-evaluation export: " . $archives->count() . " archives exported by user " . Auth::id());

        if ($format === 'xlsx') {
            return Excel::download(new class($archives) implements \Maatwebsite\Excel\Concerns\WithEvents {
                protected $archives;

                public function __construct($archives) {
                    $this->archives = $archives;
                }

                public function registerEvents(): array
                {
                    return [
                        \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                            $sheet = $event->sheet->getDelegate();

                            // Set column widths
                            $sheet->getColumnDimension('A')->setWidth(5);   // No
                            $sheet->getColumnDimension('B')->setWidth(15);  // Nomor Arsip
                            $sheet->getColumnDimension('C')->setWidth(35);  // Uraian
                            $sheet->getColumnDimension('D')->setWidth(20);  // Kategori
                            $sheet->getColumnDimension('E')->setWidth(25);  // Klasifikasi
                            $sheet->getColumnDimension('F')->setWidth(15);  // Tahun
                            $sheet->getColumnDimension('G')->setWidth(15);  // Status
                            $sheet->getColumnDimension('H')->setWidth(20);  // Lokasi
                            $sheet->getColumnDimension('I')->setWidth(30);  // Catatan Evaluasi

                            // Add header content
                            $sheet->setCellValue('A1', "DAFTAR ARSIP DINILAI KEMBALI");
                            $sheet->mergeCells('A1:I1');

                            $sheet->setCellValue('A2', 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU');
                            $sheet->mergeCells('A2:I2');

                            $sheet->setCellValue('A3', 'PROVINSI JAWA TIMUR');
                            $sheet->mergeCells('A3:I3');

                            $sheet->setCellValue('A4', 'SUB BAGIAN PTSP');
                            $sheet->mergeCells('A4:I4');

                            $sheet->setCellValue('A5', 'EXPORT ' . $this->archives->count() . ' ARSIP DINILAI KEMBALI - ' . date('d F Y'));
                            $sheet->mergeCells('A5:I5');

                            // Header row
                            $sheet->setCellValue('A7', 'No.');
                            $sheet->setCellValue('B7', 'Nomor Arsip');
                            $sheet->setCellValue('C7', 'Uraian');
                            $sheet->setCellValue('D7', 'Kategori');
                            $sheet->setCellValue('E7', 'Klasifikasi');
                            $sheet->setCellValue('F7', 'Tahun');
                            $sheet->setCellValue('G7', 'Status');
                            $sheet->setCellValue('H7', 'Lokasi');
                            $sheet->setCellValue('I7', 'Catatan Evaluasi');

                            // Add data
                            $row = 8;
                            $counter = 1;
                            foreach ($this->archives as $archive) {
                                $sheet->setCellValue("A{$row}", $counter);
                                $sheet->setCellValue("B{$row}", $archive->index_number ?? 'N/A');
                                $sheet->setCellValue("C{$row}", $archive->description ?? 'N/A');
                                $sheet->setCellValue("D{$row}", $archive->category->nama_kategori ?? 'N/A');
                                $sheet->setCellValue("E{$row}", $archive->classification ? ($archive->classification->code . ' - ' . $archive->classification->nama_klasifikasi) : 'N/A');
                                $sheet->setCellValue("F{$row}", $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('Y') : 'N/A');
                                $sheet->setCellValue("G{$row}", $archive->status);
                                $sheet->setCellValue("H{$row}", $archive->hasStorageLocation() ? 'Lokasi Set' : 'Belum Set');
                                $sheet->setCellValue("I{$row}", $archive->evaluation_notes ?? 'Belum ada catatan');

                                $row++;
                                $counter++;
                            }

                            // Apply styles
                            $sheet->getStyle('A1:I5')->applyFromArray([
                                'font' => ['bold' => true, 'size' => 12],
                                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'E3F2FD']]
                            ]);

                            $sheet->getStyle('A7:I7')->applyFromArray([
                                'font' => ['bold' => true],
                                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '81C784']],
                                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                            ]);

                            $lastRow = $row - 1;
                            if ($lastRow >= 8) {
                                $sheet->getStyle("A8:I{$lastRow}")->applyFromArray([
                                    'borders' => [
                                        'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
                                    ]
                                ]);
                            }
                        }
                    ];
                }
            }, $fileName);
        } else {
            // PDF export
            $pdf = \PDF::loadView('admin.re-evaluation.pdf', compact('archives'));
            return $pdf->download($fileName);
        }
    }

    /**
     * Get re-evaluation archives with filtering
     */
    public function getReEvaluationArchives(Request $request)
    {
        $user = Auth::user();

        $query = Archive::where('status', 'Dinilai Kembali')
            ->whereNull('evaluation_notes') // Exclude archives that have been evaluated
            ->with(['category', 'classification', 'createdByUser']);

        // Filter based on user role
        if ($user->role_type === 'intern') {
            $query->whereHas('createdByUser', function($q) {
                $q->where('role_type', 'intern');
            });
        }

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('classification_id')) {
            $query->where('classification_id', $request->classification_id);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('index_number', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $archives = $query->orderBy('created_at', 'desc')->paginate(25);

        return response()->json([
            'success' => true,
            'archives' => $archives->items(),
            'pagination' => [
                'current_page' => $archives->currentPage(),
                'last_page' => $archives->lastPage(),
                'per_page' => $archives->perPage(),
                'total' => $archives->total()
            ]
        ]);
    }
}
