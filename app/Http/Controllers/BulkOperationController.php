<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArchiveExportWithHeader;
use App\Services\StorageUpdateService;


class BulkOperationController extends Controller
{
    protected $storageUpdateService;

    public function __construct(StorageUpdateService $storageUpdateService)
    {
        $this->storageUpdateService = $storageUpdateService;
    }

    /**
     * Show bulk operations form
     */
    public function index()
    {
        $user = Auth::user();

        // Filter archives based on user role
        $query = Archive::with(['category', 'classification', 'createdByUser']);

        if ($user->role_type === 'staff' || $user->role_type === 'intern') {
            // Staff and intern can only see archives created by staff and intern users
            $query->whereHas('createdByUser', function($q) {
                $q->whereIn('role_type', ['admin', 'staff', 'intern']);
            });
        }

        $archives = $query->orderBy('created_at', 'desc')->paginate(25);

        $categories = Category::orderBy('nama_kategori')->get();
        $classifications = Classification::with('category')->orderBy('code')->get();

        // Filter users based on role
        if ($user->role_type === 'admin') {
            $users = User::orderBy('name')->get();
        } else {
            $users = User::whereIn('role_type', ['staff', 'intern'])->orderBy('name')->get();
        }

        $statuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah'];

        // Get storage racks for bulk move storage
        $racks = \App\Models\StorageRack::where('status', 'active')->orderBy('name')->get();

        // Determine view path based on user role
        $viewPath = $user->role_type === 'admin' ? 'admin.bulk.index' :
                   ($user->role_type === 'staff' ? 'staff.bulk.index' : 'intern.bulk.index');

        return view($viewPath, compact(
            'archives',
            'categories',
            'classifications',
            'users',
            'statuses',
            'racks'
        ));
    }

    /**
     * Handle bulk status change
     */
    public function bulkStatusChange(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array|min:1',
            'archive_ids.*' => 'exists:archives,id',
            'new_status' => 'required|in:Aktif,Inaktif,Permanen,Musnah'
        ]);

        $archiveIds = $request->archive_ids;
        $newStatus = $request->new_status;
        $successCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($archiveIds as $archiveId) {
                $archive = Archive::find($archiveId);
                if ($archive) {
                    $oldStatus = $archive->status;

                    // Update status
                    $archive->update([
                        'status' => $newStatus,
                        'manual_status_override' => true,
                        'manual_override_at' => now(),
                        'manual_override_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ]);

                    $successCount++;

                    // Log the change
                    Log::info("Bulk status change: Archive ID {$archiveId} changed from {$oldStatus} to {$newStatus} by user " . Auth::id());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengubah status {$successCount} arsip menjadi {$newStatus}",
                'count' => $successCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk status change error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status arsip: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk delete
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array|min:1',
            'archive_ids.*' => 'exists:archives,id'
        ]);

        $archiveIds = $request->archive_ids;
        $successCount = 0;

        DB::beginTransaction();
        try {
            $successCount = Archive::whereIn('id', $archiveIds)->delete();

            DB::commit();

            // Log the deletion
            Log::info("Bulk delete: {$successCount} archives deleted by user " . Auth::id() . ". IDs: " . implode(',', $archiveIds));

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$successCount} arsip",
                'count' => $successCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus arsip: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk category assignment
     */
    public function bulkAssignCategory(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array|min:1',
            'archive_ids.*' => 'exists:archives,id',
            'category_id' => 'required|exists:categories,id'
        ]);

        $archiveIds = $request->archive_ids;
        $categoryId = $request->category_id;
        $category = Category::find($categoryId);
        $successCount = 0;

        DB::beginTransaction();
        try {
            $successCount = Archive::whereIn('id', $archiveIds)->update([
                'category_id' => $categoryId,
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

            DB::commit();

            // Log the assignment
            Log::info("Bulk category assignment: {$successCount} archives assigned to category '{$category->name}' by user " . Auth::id());

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengassign {$successCount} arsip ke kategori '{$category->name}'",
                'count' => $successCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk category assignment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat assign kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk classification assignment
     */
    public function bulkAssignClassification(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array|min:1',
            'archive_ids.*' => 'exists:archives,id',
            'classification_id' => 'required|exists:classifications,id'
        ]);

        $archiveIds = $request->archive_ids;
        $classificationId = $request->classification_id;
        $classification = Classification::with('category')->find($classificationId);
        $successCount = 0;

        DB::beginTransaction();
        try {
            $successCount = Archive::whereIn('id', $archiveIds)->update([
                'classification_id' => $classificationId,
                'category_id' => $classification->category_id, // Auto-assign category too
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

            DB::commit();

            // Log the assignment
            Log::info("Bulk classification assignment: {$successCount} archives assigned to classification '{$classification->code} - {$classification->name}' by user " . Auth::id());

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengassign {$successCount} arsip ke klasifikasi '{$classification->code} - {$classification->name}'",
                'count' => $successCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk classification assignment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat assign klasifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk export of selected archives
     */
    public function bulkExport(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array|min:1',
            'archive_ids.*' => 'exists:archives,id'
        ]);

        $archiveIds = $request->archive_ids;
        $fileName = 'export-arsip-terpilih-' . date('Y-m-d-H-i-s') . '.xlsx';

        // Log the export
        Log::info("Bulk export: " . count($archiveIds) . " archives exported by user " . Auth::id());

        // Create a custom export using the same format as ArchiveExportWithHeader
        // but filtered to selected archive IDs
        return Excel::download(new class($archiveIds) implements \Maatwebsite\Excel\Concerns\WithEvents {
            protected $archiveIds;

            public function __construct($archiveIds) {
                $this->archiveIds = $archiveIds;
            }

            public function registerEvents(): array
            {
                return [
                    \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                        $sheet = $event->sheet->getDelegate();

                        // Get selected archives data
                        $data = \App\Models\Archive::with(['category', 'classification'])
                            ->whereIn('id', $this->archiveIds)
                            ->orderBy('created_at', 'desc')
                            ->get();

                        // Set column widths - same as ArchiveExportWithHeader
                        $sheet->getColumnDimension('A')->setWidth(5);   // No
                        $sheet->getColumnDimension('B')->setWidth(12);  // Kode Klasifikasi
                        $sheet->getColumnDimension('C')->setWidth(25);  // Indeks
                        $sheet->getColumnDimension('D')->setWidth(35);  // Uraian
                        $sheet->getColumnDimension('E')->setWidth(15);  // Kurun Waktu
                        $sheet->getColumnDimension('F')->setWidth(15);  // Tingkat Perkembangan
                        $sheet->getColumnDimension('G')->setWidth(10);  // Jumlah
                        $sheet->getColumnDimension('H')->setWidth(15);  // Ket
                        $sheet->getColumnDimension('I')->setWidth(12);  // Nomor Definitif
                        $sheet->getColumnDimension('J')->setWidth(12);  // Nomor Boks
                        $sheet->getColumnDimension('K')->setWidth(8);   // Rak
                        $sheet->getColumnDimension('L')->setWidth(8);   // Baris
                        $sheet->getColumnDimension('M')->setWidth(25);  // Jangka Simpan

                        // Add header content
                        $statusTitle = "ARSIP TERPILIH";

                        // Row 1: Main title
                        $sheet->setCellValue('A1', "DAFTAR " . $statusTitle);
                        $sheet->mergeCells('A1:M1');

                        // Row 2: Department
                        $sheet->setCellValue('A2', 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU');
                        $sheet->mergeCells('A2:M2');

                        // Row 3: Province
                        $sheet->setCellValue('A3', 'PROVINSI JAWA TIMUR');
                        $sheet->mergeCells('A3:M3');

                        // Row 4: Sub department
                        $sheet->setCellValue('A4', 'SUB BAGIAN PTSP');
                        $sheet->mergeCells('A4:M4');

                        // Row 5: Export info
                        $sheet->setCellValue('A5', 'EXPORT ' . count($this->archiveIds) . ' ARSIP TERPILIH - ' . date('d F Y'));
                        $sheet->mergeCells('A5:M5');

                        // Row 6: Status
                        $sheet->setCellValue('A6', 'OPERASI MASSAL');
                        $sheet->mergeCells('A6:M6');

                        // Row 7: Empty row for spacing
                        $sheet->setCellValue('A7', '');

                        // Header rows (row 8-9) - Complex headers with merged cells
                        // Row 8: Main headers
                        $sheet->setCellValue('A8', 'No.');
                        $sheet->mergeCells('A8:A9');

                        $sheet->setCellValue('B8', 'Kode Klasifikasi');
                        $sheet->mergeCells('B8:B9');

                        $sheet->setCellValue('C8', 'Indeks');
                        $sheet->mergeCells('C8:C9');

                        $sheet->setCellValue('D8', 'Uraian');
                        $sheet->mergeCells('D8:D9');

                        $sheet->setCellValue('E8', 'Kurun Waktu');
                        $sheet->mergeCells('E8:E9');

                        $sheet->setCellValue('F8', 'Tingkat Perkembangan');
                        $sheet->mergeCells('F8:F9');

                        $sheet->setCellValue('G8', 'Jumlah');
                        $sheet->mergeCells('G8:G9');

                        $sheet->setCellValue('H8', 'Ket.');
                        $sheet->mergeCells('H8:H9');

                        // Complex header for Nomor Definitif dan Boks
                        $sheet->setCellValue('I8', 'Nomor Definitif dan Boks');
                        $sheet->mergeCells('I8:J8');
                        $sheet->setCellValue('I9', 'Nomor Definitif');
                        $sheet->setCellValue('J9', 'Nomor Boks');

                        // Complex header for Lokasi Simpan
                        $sheet->setCellValue('K8', 'Lokasi Simpan');
                        $sheet->mergeCells('K8:L8');
                        $sheet->setCellValue('K9', 'Rak');
                        $sheet->setCellValue('L9', 'Baris');

                        $sheet->setCellValue('M8', 'Jangka Simpan dan Nasib Akhir');
                        $sheet->mergeCells('M8:M9');

                        // Add data starting from row 10
                        $row = 10;
                        $counter = 1;
                        foreach ($data as $archive) {
                            // Format kurun waktu to only show month name
                            $kurunWaktu = $archive->kurun_waktu_start ?
                                $archive->kurun_waktu_start->format('F') : '';

                            // Format jangka simpan
                            $jangkaSimpan = $archive->retention_aktif . ' Tahun (' .
                                           ($archive->category->nasib_akhir ?? 'Permanen') . ')';

                            // Set values
                            $sheet->setCellValueExplicit("A{$row}", $counter, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                            $sheet->setCellValueExplicit("B{$row}", $archive->classification->code ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("C{$row}", $archive->index_number ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("D{$row}", $archive->uraian ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("E{$row}", $kurunWaktu, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("F{$row}", $archive->tingkat_perkembangan ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("G{$row}", $archive->jumlah ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("H{$row}", $archive->ket ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("I{$row}", '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("J{$row}", '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("K{$row}", '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("L{$row}", '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValueExplicit("M{$row}", $jangkaSimpan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                            $row++;
                            $counter++;
                        }

                        // Apply styles - same as ArchiveExportWithHeader
                        // Title section styling (rows 1-6)
                        $sheet->getStyle('A1:M6')->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12],
                            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'B3E5FC']]
                        ]);

                        // Header rows styling (row 8-9)
                        $sheet->getStyle('A8:M9')->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '81C784']],
                            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                            'borders' => [
                                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]
                            ]
                        ]);

                        // Data section styling
                        $lastRow = $row - 1;
                        if ($lastRow >= 10) {
                            $sheet->getStyle("A10:M{$lastRow}")->applyFromArray([
                                'borders' => [
                                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                                ],
                                'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
                            ]);
                        }

                        // Set row heights
                        for ($i = 1; $i <= 6; $i++) {
                            $sheet->getRowDimension($i)->setRowHeight(20);
                        }
                        $sheet->getRowDimension(8)->setRowHeight(25);
                        $sheet->getRowDimension(9)->setRowHeight(25);
                    }
                ];
            }
        }, $fileName);
    }

    /**
     * Handle bulk move storage
     */
    public function bulkMoveStorage(Request $request)
    {
        $request->validate([
            'archive_ids' => 'required|array|min:1',
            'archive_ids.*' => 'exists:archives,id',
            'rack_id' => 'required|exists:storage_racks,id'
        ]);

        $archiveIds = $request->archive_ids;
        $rackId = $request->rack_id;

        try {
            $result = $this->storageUpdateService->bulkUpdateStorageLocation($archiveIds, $rackId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'count' => $result['success_count'],
                    'errors' => $result['errors']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada arsip yang berhasil dipindahkan',
                    'errors' => $result['errors']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Bulk storage move error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memindahkan arsip: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get archives with filtering for bulk operations
     */
    public function getArchives(Request $request)
    {
        $user = Auth::user();
        $query = Archive::with(['category', 'classification', 'createdByUser']);

        // Filter archives based on user role
        if ($user->role_type === 'staff' || $user->role_type === 'intern') {
            // Staff and intern can only see archives created by staff and intern users
            $query->whereHas('createdByUser', function($q) {
                $q->whereIn('role_type', ['staff', 'intern']);
            });
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('classification_id')) {
            $query->where('classification_id', $request->classification_id);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('kurun_waktu_start', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('kurun_waktu_start', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('index_number', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $archives = $query->orderBy('created_at', 'desc')->paginate(25);

        // Map data agar classification dan category selalu ada field code, nama_klasifikasi, nama_kategori
        $archivesData = collect($archives->items())->map(function($archive) {
            return [
                'id' => $archive->id,
                'index_number' => $archive->index_number,
                'description' => $archive->description,
                'status' => $archive->status,
                'created_at' => $archive->created_at,
                'category' => $archive->category ? [
                    'nama_kategori' => $archive->category->nama_kategori
                ] : null,
                'classification' => $archive->classification ? [
                    'code' => $archive->classification->code,
                    'nama_klasifikasi' => $archive->classification->nama_klasifikasi
                ] : null,
                'created_by_user' => $archive->createdByUser ? [
                    'name' => $archive->createdByUser->name
                ] : null,
            ];
        });

        return response()->json([
            'archives' => $archivesData,
            'pagination' => [
                'current_page' => $archives->currentPage(),
                'last_page' => $archives->lastPage(),
                'per_page' => $archives->perPage(),
                'total' => $archives->total(),
                'from' => $archives->firstItem(),
                'to' => $archives->lastItem()
            ]
        ]);
    }
}
