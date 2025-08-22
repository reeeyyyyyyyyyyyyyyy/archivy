<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArchiveMusnahExport implements WithMultipleSheets
{
    protected $yearFrom;
    protected $yearTo;
    protected $createdBy;
    protected $categoryId;
    protected $classificationId;

    public function __construct($yearFrom = null, $yearTo = null, $createdBy = null, $categoryId = null, $classificationId = null)
    {
        $this->yearFrom = $yearFrom;
        $this->yearTo = $yearTo;
        $this->createdBy = $createdBy;
        $this->categoryId = $categoryId;
        $this->classificationId = $classificationId;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        if (!$this->yearFrom && !$this->yearTo) {
            $sheets[] = new ArchiveMusnahSheet(null, $this->createdBy, $this->categoryId, $this->classificationId);
            return $sheets;
        }
        
        $startYear = $this->yearFrom ?: Archive::min('kurun_waktu_start');
        $endYear = $this->yearTo ?: Archive::max('kurun_waktu_start');
        
        if ($startYear instanceof \Carbon\Carbon) {
            $startYear = $startYear->year;
        }
        
        if ($endYear instanceof \Carbon\Carbon) {
            $endYear = $endYear->year;
        }
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $sheets[] = new ArchiveMusnahSheet($year, $this->createdBy, $this->categoryId, $this->classificationId);
        }
        
        return $sheets;
    }
}

class ArchiveMusnahSheet implements FromCollection, WithTitle, WithEvents
{
    protected $year;
    protected $createdBy;
    protected $categoryId;
    protected $classificationId;

    public function __construct($year = null, $createdBy = null, $categoryId = null, $classificationId = null)
    {
        $this->year = $year;
        $this->createdBy = $createdBy;
        $this->categoryId = $categoryId;
        $this->classificationId = $classificationId;
    }

    public function collection()
    {
        $query = Archive::with(['classification'])
            ->where('status', 'Musnah');

        if ($this->year) {
            $query->whereYear('kurun_waktu_start', $this->year);
        }
        
        if ($this->createdBy) {
            $query->where('created_by', $this->createdBy);
        }
        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }
        if ($this->classificationId) {
            $query->where('classification_id', $this->classificationId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function title(): string
    {
        return $this->year ? "TAHUN {$this->year}" : "SEMUA TAHUN";
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $data = $this->collection();
                $year = $this->year;

                // Header Utama - hanya DAFTAR ARSIP USUL MUSNAH
                $sheet->setCellValue('A1', 'DAFTAR ARSIP USUL MUSNAH');
                $sheet->mergeCells('A1:H1');

                // Style Header Utama
                $event->sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'D9D9D9']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM
                        ]
                    ]
                ]);

                // Header Kolom (baris 2)
                $headers = [
                    'NO', 
                    'KODE KLASIFIKASI',
                    'NOMOR SURAT',
                    'URAIAN INFORMASI ARSIP',
                    'KURUN WAKTU',
                    'JUMLAH',
                    'SKKAD',
                    'NASIB AKHIR'
                ];

                foreach ($headers as $key => $header) {
                    $cell = chr(65 + $key) . '2';
                    $sheet->setCellValue($cell, $header);
                }

                // Style Header Kolom
                $event->sheet->getStyle('A2:H2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '4472C4']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);

                // Isi Data (mulai baris 3)
                $row = 3;
                foreach ($data as $index => $archive) {
                    $sheet->setCellValue('A'.$row, $index + 1);
                    $sheet->setCellValue('B'.$row, $archive->classification->code ?? '-');
                    $sheet->setCellValue('C'.$row, $archive->index_number ?? '-');
                    $sheet->setCellValue('D'.$row, $archive->description ?? '-');
                    $sheet->setCellValue('E'.$row, $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('Y') : '-');
                    $sheet->setCellValue('F'.$row, $archive->jumlah_berkas ?? '-');
                    $sheet->setCellValue('G'.$row, $archive->skkad ?? '-');
                    $sheet->setCellValue('H'.$row, $archive->classification->nasib_akhir ?? 'Musnah');
                    
                    $row++;
                }

                // Style Data
                $lastRow = max($row - 1, 3);
                $event->sheet->getStyle('A3:H'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);

                // Alignment khusus
                $event->sheet->getStyle('A3:A'.$lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('E3:H'.$lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Set column widths
                $columnWidths = [
                    'A' => 5,   'B' => 15,  'C' => 20,
                    'D' => 40,  'E' => 15,  'F' => 10,
                    'G' => 15,  'H' => 15
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Add right border to column H
                $event->sheet->getStyle('H1:H'.$lastRow)->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // Hide columns after H
                foreach (range('I', 'Z') as $col) {
                    $sheet->getColumnDimension($col)->setWidth(0);
                    $sheet->getColumnDimension($col)->setVisible(false);
                }

                // Clear all cells after column H
                $highestRow = $sheet->getHighestRow();
                for ($row = 1; $row <= $highestRow; $row++) {
                    for ($col = 'I'; $col <= 'Z'; $col++) {
                        $sheet->setCellValue($col.$row, null);
                    }
                }
                
                // Set white background for area after H
                $sheet->getStyle('I1:Z'.$highestRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFFF']
                    ]
                ]);
            }
        ];
    }
}