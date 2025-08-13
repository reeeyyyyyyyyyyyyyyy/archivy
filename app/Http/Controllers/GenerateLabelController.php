<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StorageRack;
use App\Models\StorageBox;
use App\Models\Archive;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class GenerateLabelController extends Controller
{
    /**
     * Display the label generation form
     */
    public function index()
    {
        $user = Auth::user();
        $racks = StorageRack::where('status', 'active')->get();

        // Determine view path based on user role
        $viewPath = $user->role_type === 'admin' ? 'admin.storage.generate-box-labels' :
                   ($user->role_type === 'staff' ? 'staff.storage.generate-box-labels' : 'intern.storage.generate-box-labels');

        return view($viewPath, compact('racks'));
    }

    /**
     * Generate labels
     */
    public function generate(Request $request)
    {
        $request->validate([
            'rack_id' => 'required|exists:storage_racks,id',
            'box_start' => 'required|integer|min:1',
            'box_end' => 'required|integer|gte:box_start'
        ]);

        $rack = StorageRack::findOrFail($request->rack_id);
        $boxStart = $request->box_start;
        $boxEnd = $request->box_end;

        // Get boxes in the specified range
        $boxes = StorageBox::where('rack_id', $rack->id)
            ->whereBetween('box_number', [$boxStart, $boxEnd])
            ->orderBy('box_number')
            ->get();

        if ($boxes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada box yang ditemukan dalam range yang ditentukan.'
            ], 400);
        }

        try {
            // Generate labels data
            $labels = $this->generateLabelsData($boxes, $rack);

            // Generate PDF only
            return $this->generatePDF($labels, $rack);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate label: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF labels (direct method for routes)
     */
    public function generatePDFDirect(Request $request)
    {
        $request->validate([
            'rack_id' => 'required|exists:storage_racks,id',
            'box_start' => 'required|integer|min:1',
            'box_end' => 'required|integer|gte:box_start'
        ]);

        $rack = StorageRack::findOrFail($request->rack_id);
        $boxStart = $request->box_start;
        $boxEnd = $request->box_end;

        // Get boxes in the specified range
        $boxes = StorageBox::where('rack_id', $rack->id)
            ->whereBetween('box_number', [$boxStart, $boxEnd])
            ->orderBy('box_number')
            ->get();

        if ($boxes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada box yang ditemukan dalam range yang ditentukan.'
            ], 400);
        }

        try {
            // Generate labels data
            $labels = $this->generateLabelsData($boxes, $rack);

            // Generate PDF only
            return $this->generatePDF($labels, $rack);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate label: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get boxes for a specific rack
     */
    public function getBoxes($rackId)
    {
        $user = Auth::user();
        $boxes = StorageBox::where('rack_id', $rackId)
            ->orderBy('box_number')
            ->get(['box_number', 'archive_count', 'capacity']);

        return response()->json([
            'success' => true,
            'boxes' => $boxes
        ]);
    }

    /**
     * Preview labels for a specific rack and box range
     */
    public function preview($rackId, $boxStart, $boxEnd)
    {
        $user = Auth::user();
        $rack = StorageRack::findOrFail($rackId);

        // Get boxes in the specified range
        $boxes = StorageBox::where('rack_id', $rack->id)
            ->whereBetween('box_number', [$boxStart, $boxEnd])
            ->orderBy('box_number')
            ->get();

        if ($boxes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada box yang ditemukan dalam range yang ditentukan.'
            ], 400);
        }

        // Generate labels data
        $labels = $this->generateLabelsData($boxes, $rack);

        // Generate HTML for preview with separate tables for each box
        $html = '';
        foreach ($labels as $label) {
            $html .= '<div class="mb-8 p-4 bg-white rounded-lg border-2 border-gray-300 shadow-md">';
            $html .= '<div class="text-center mb-4">';
            $html .= '<h4 class="font-bold text-lg mb-1 text-gray-800">DINAS PENANAMAN MODAL DAN PTSP</h4>';
            $html .= '<h5 class="font-semibold text-base mb-1 text-gray-700">PROVINSI JAWA TIMUR</h5>';
            $html .= '<div class="border-b-2 border-gray-400 w-full"></div>';
            $html .= '</div>';

            $html .= '<table class="w-full border-2 border-gray-400">';
            $html .= '<tr class="border-b border-gray-400">';
            $html .= '<td class="px-4 py-2 text-sm font-bold text-center border-r border-gray-400" style="width: 70%;">NOMOR BERKAS</td>';
            $html .= '<td class="px-4 py-2 text-sm font-bold text-center" style="width: 30%;">NO. BOKS</td>';
            $html .= '</tr>';

            foreach ($label['ranges'] as $range) {
                $html .= '<tr class="border-b border-gray-400">';
                $html .= '<td class="px-4 py-2 text-sm font-medium border-r border-gray-400">' . $range . '</td>';
                $html .= '<td class="px-4 py-2 text-center font-bold text-sm">' . $label['box_number'] . '</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';
            $html .= '</div>';
        }

        return response()->json([
            'success' => true,
            'labels' => $labels
        ]);
    }

    /**
     * Generate labels data with separate table for each box
     */
    public function generateLabelsData($boxes, $rack)
    {
        $labels = [];

        foreach ($boxes as $box) {
            // Get archives in this specific rack and box
            $archives = Archive::where('rack_number', $rack->id)
                ->where('box_number', $box->box_number)
                ->get();

            if ($archives->isEmpty()) {
                // Empty box - use placeholders
                $labels[] = [
                    'box_number' => $box->box_number,
                    'ranges' => [
                        'TAHUN X No. X-X',
                        'TAHUN X No. X'
                    ]
                ];
                continue;
            }

            // Group archives by year
            $archivesByYear = $archives->groupBy(function($archive) {
                return $archive->kurun_waktu_start ? $archive->kurun_waktu_start->year : 'Unknown';
            });

            $ranges = [];

            // Sort years to ensure consistent order
            $years = $archivesByYear->keys()->sort();

            foreach ($years as $year) {
                $yearArchives = $archivesByYear[$year];
                $totalArchives = $yearArchives->count();

                if ($totalArchives === 1) {
                    // Single archive in this year - use actual file_number
                    $firstArchive = $yearArchives->first();
                    $fileNumber = $firstArchive->file_number ?? 1;
                    $ranges[] = "TAHUN {$year} No. {$fileNumber}";
                } else {
                    // Multiple archives in this year - use actual file numbers
                    $minFileNumber = $yearArchives->min('file_number') ?? 1;
                    $maxFileNumber = $yearArchives->max('file_number') ?? $totalArchives;
                    $ranges[] = "TAHUN {$year} No. {$minFileNumber}-{$maxFileNumber}";
                }
            }

            // Ensure we have at least 1 range for the table
            if (count($ranges) === 0) {
                $ranges = ['TAHUN X No. X-X'];
            }

            $labels[] = [
                'box_number' => $box->box_number,
                'ranges' => $ranges
            ];
        }
        return $labels;
    }

    /**
     * Generate PDF labels
     */
    public function generatePDF($labels, $rack)
    {
        $filename = 'rack_labels_' . $rack->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $filepath = storage_path('app/public/' . $filename);

        // Process labels with pagination (4 labels per page)
        $labelsPerPage = 4;
        $paginatedLabels = [];

        for ($i = 0; $i < count($labels); $i += $labelsPerPage) {
            $paginatedLabels[] = array_slice($labels, $i, $labelsPerPage);
        }

        $html = view('admin.storage.label-template', compact('paginatedLabels', 'rack'))->render();

        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->save($filepath);

        $downloadUrl = url('storage/' . $filename);

        return response()->json([
            'success' => true,
            'message' => 'Label berhasil di-generate!',
            'download_url' => $downloadUrl
        ]);
    }
}
