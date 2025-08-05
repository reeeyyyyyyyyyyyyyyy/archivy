<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StorageBox;
use App\Models\StorageRack;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateBoxLabels extends Command
{
    protected $signature = 'storage:generate-box-labels {--rack-id=} {--box-start=} {--box-end=} {--format=pdf}';
    protected $description = 'Generate printable box labels in PDF format for a specific rack and box range';

    public function handle()
    {
        $rackId = $this->option('rack-id');
        $boxStart = $this->option('box-start');
        $boxEnd = $this->option('box-end');
        $format = $this->option('format');

        if (!$rackId) {
            $this->error('Please specify rack ID using --rack-id option');
            return 1;
        }

        if (!$boxStart || !$boxEnd) {
            $this->error('Please specify box range using --box-start and --box-end options');
            return 1;
        }

        $rack = StorageRack::find($rackId);
        if (!$rack) {
            $this->error('Rack not found with ID: ' . $rackId);
            return 1;
        }

        // Get boxes in the specified range
        $boxes = StorageBox::where('rack_id', $rackId)
            ->whereBetween('box_number', [$boxStart, $boxEnd])
            ->orderBy('box_number')
            ->get();

        if ($boxes->isEmpty()) {
            $this->error('No boxes found in rack: ' . $rack->name . ' with range ' . $boxStart . '-' . $boxEnd);
            return 1;
        }

        $this->info("Generating labels for rack: {$rack->name} (ID: {$rackId})");
        $this->info("Box range: {$boxStart} - {$boxEnd}");
        $this->info("Found {$boxes->count()} boxes in this range");

        $labels = [];
        foreach ($boxes as $box) {
            $capacity = $box->capacity ?? 20; // Default capacity
            $actualCount = $box->archive_count;

            // Get archives in this box to get year information
            $archives = \App\Models\Archive::where('box_number', $box->box_number)
                ->orderBy('file_number')
                ->get();

            if ($archives->isEmpty()) {
                // Empty box - show dashes for file numbers
                $labels[] = [
                    'box_number' => $box->box_number,
                    'first_range' => 'TAHUN - NO.ARSIP -',
                    'second_range' => 'TAHUN - NO.ARSIP -',
                    'capacity' => $capacity,
                    'actual_count' => 0
                ];
            } else {
                // Get year from first archive
                $year = $archives->first()->kurun_waktu_start ? $archives->first()->kurun_waktu_start->year : 'N/A';

                // Calculate file number ranges based on actual count
                $halfCount = (int)($actualCount / 2);
                $remainder = $actualCount % 2;

                if ($remainder == 0) {
                    // Even number
                    $firstRange = "TAHUN {$year} - NO.ARSIP 1-" . $halfCount;
                    $secondRange = "TAHUN {$year} - NO.ARSIP " . ($halfCount + 1) . "-" . $actualCount;
                } else {
                    // Odd number - give extra to first range
                    $firstRange = "TAHUN {$year} - NO.ARSIP 1-" . ($halfCount + 1);
                    $secondRange = "TAHUN {$year} - NO.ARSIP " . ($halfCount + 2) . "-" . $actualCount;
                }

                // Special case for count = 1
                if ($actualCount == 1) {
                    $firstRange = "TAHUN {$year} - NO.ARSIP 1-1";
                    $secondRange = "TAHUN {$year} - NO.ARSIP 1-1";
                }

                $labels[] = [
                    'box_number' => $box->box_number,
                    'first_range' => $firstRange,
                    'second_range' => $secondRange,
                    'capacity' => $capacity,
                    'actual_count' => $actualCount
                ];
            }
        }

        if ($format === 'pdf') {
            $this->generatePDF($labels, $rack);
        } elseif ($format === 'excel') {
            $this->generateExcel($labels, $rack);
        } elseif ($format === 'word') {
            $this->generateWord($labels, $rack);
        } else {
            $this->error('Only PDF, Excel, and Word formats are supported');
            return 1;
        }

        $this->info('Labels generated successfully!');
        return 0;
    }

    private function generatePDF($labels, $rack)
    {
        $html = $this->generateHTML($labels, $rack);

        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'rack_labels_' . $rack->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $filepath = storage_path('app/public/' . $filename);

        $pdf->save($filepath);

        $this->info("PDF saved to: " . $filepath);
        $this->info("Download URL: " . asset('storage/' . $filename));

        return asset('storage/' . $filename);
    }

    private function generateHTML($labels, $rack)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Box Labels - ' . $rack->name . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 15px;
                    background-color: white;
                }
                .label-container {
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                }
                .label {
                    width: 100%;
                    height: 120px;
                    border: 2px solid #000000;
                    position: relative;
                    margin-bottom: 120px;
                    page-break-inside: avoid;
                    background-color: white;
                }
                .header {
                    height: 50px;
                    background-color: white;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    border-bottom: 2px solid #000000;
                    font-weight: bold;
                    font-size: 14px;
                    text-align: center;
                    padding: 8px 0;
                }
                .header-text {
                    color: #000000;
                    font-weight: bold;
                    font-size: 14px;
                    text-align: center;
                    margin: 0;
                    line-height: 1.2;
                }
                .content {
                    height: 70px;
                    display: flex;
                }
                .file-numbers {
                    flex: 0 0 72.7%;
                    background-color: white;
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-start;
                    align-items: flex-start;
                    border-right: 2px solid #000000;
                    padding: 12px 8px;
                }
                .box-number {
                    flex: 0 0 27.3%;
                    background-color: white;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    padding: 12px 8px;
                }
                .content-text {
                    color: #000000;
                    font-weight: bold;
                    font-size: 18px;
                    text-align: center;
                    margin: 0;
                    line-height: 1.3;
                }
                .file-range {
                    margin: 8px 0;
                    text-align: left;
                    padding-left: 25px;
                    font-weight: bold;
                    font-size: 14px;
                }
                .label-title {
                    font-weight: bold;
                    font-size: 16px;
                    margin-bottom: 15px;
                    color: #000000;
                    text-align: center;
                }
                .cut-line {
                    border-top: 2px dashed #000000;
                    margin: 20px 0;
                    page-break-after: always;
                }
                @media print {
                    body {
                        background-color: white;
                    }
                    .label {
                        page-break-inside: avoid;
                        margin-bottom: 15px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="label-container">';

        foreach ($labels as $label) {
            $html .= '
                <div class="label">
                    <div class="header">
                        <p class="header-text">DINAS PENANAMAN MODAL DAN PTSP</p>
                        <p class="header-text">PROVINSI JAWA TIMUR</p>
                    </div>
                    <div class="content">
                        <div class="file-numbers">
                            <div class="label-title">NOMOR BERKAS</div>
                            <div class="file-range">
                                <p class="content-text">' . $label['first_range'] . '</p>
                            </div>
                            <div class="file-range">
                                <p class="content-text">' . $label['second_range'] . '</p>
                            </div>
                        </div>
                        <div class="box-number">
                            <div class="label-title">NO. BOKS</div>
                            <p class="content-text">' . $label['box_number'] . '</p>
                        </div>
                    </div>
                </div>';
        }

        $html .= '
            </div>
        </body>
        </html>';

        return $html;
    }

    private function generateExcel($labels, $rack)
    {
        $filename = 'rack_labels_' . $rack->id . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filepath = storage_path('app/public/' . $filename);

        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'LABEL BOX ARSIP - ' . strtoupper($rack->name));
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        // Set headers
        $sheet->setCellValue('A3', 'NO. BOKS');
        $sheet->setCellValue('B3', 'NOMOR BERKAS (KOLOM 1)');
        $sheet->setCellValue('C3', 'NOMOR BERKAS (KOLOM 2)');
        $sheet->setCellValue('D3', 'KAPASITAS');
        $sheet->getStyle('A3:D3')->getFont()->setBold(true);

        // Add data
        $row = 4;
        foreach ($labels as $label) {
            $sheet->setCellValue('A' . $row, $label['box_number']);
            $sheet->setCellValue('B' . $row, $label['first_range']);
            $sheet->setCellValue('C' . $row, $label['second_range']);
            $sheet->setCellValue('D' . $row, $label['actual_count'] . '/' . $label['capacity']);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);

        $downloadUrl = url('storage/' . $filename);

        $this->info("Excel file saved to: {$filepath}");
        $this->info("Download URL: {$downloadUrl}");
        $this->info("Labels generated successfully!");
    }

    private function generateWord($labels, $rack)
    {
        $filename = 'rack_labels_' . $rack->id . '_' . date('Y-m-d_H-i-s') . '.docx';
        $filepath = storage_path('app/public/' . $filename);

        // Create new Word document using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title with formatting
        $sheet->setCellValue('A1', 'DINAS PENANAMAN MODAL DAN PTSP');
        $sheet->setCellValue('A2', 'PROVINSI JAWA TIMUR');
        $sheet->setCellValue('A3', 'LABEL BOX ARSIP - ' . strtoupper($rack->name));
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');

        // Style the header
        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Add space
        $sheet->setCellValue('A4', '');

        // Set table headers
        $sheet->setCellValue('A5', 'NOMOR BERKAS');
        $sheet->setCellValue('D5', 'NO. BOKS');
        $sheet->mergeCells('A5:C5');
        $sheet->mergeCells('D5:D5');

        // Style table headers
        $sheet->getStyle('A5:D5')->getFont()->setBold(true);
        $sheet->getStyle('A5:D5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:D5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');

        // Add borders
        $sheet->getStyle('A5:D5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Add data rows
        $row = 6;
        foreach ($labels as $label) {
            // First range
            $sheet->setCellValue('A' . $row, $label['first_range']);
            $sheet->setCellValue('D' . $row, $label['box_number']);
            $sheet->mergeCells('A' . $row . ':C' . $row);

            // Style the row
            $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $row++;

            // Second range
            $sheet->setCellValue('A' . $row, $label['second_range']);
            $sheet->setCellValue('D' . $row, $label['box_number']);
            $sheet->mergeCells('A' . $row . ':C' . $row);

            // Style the row
            $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save as Word document using the correct writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);

        $downloadUrl = url('storage/' . $filename);

        $this->info("Word document saved to: {$filepath}");
        $this->info("Download URL: {$downloadUrl}");
        $this->info("Labels generated successfully!");
    }
}
