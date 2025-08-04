<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ArchiveMusnahExport implements WithEvents
{
    protected $yearFrom;
    protected $yearTo;
    protected $createdBy;

    public function __construct($yearFrom = null, $yearTo = null, $createdBy = null)
    {
        $this->yearFrom = $yearFrom;
        $this->yearTo = $yearTo;
        $this->createdBy = $createdBy;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Query data arsip musnah
                $query = Archive::with(['classification'])
                    ->where('status', 'Musnah');

                if ($this->yearFrom) {
                    $query->whereYear('kurun_waktu_start', '>=', $this->yearFrom);
                }
                if ($this->yearTo) {
                    $query->whereYear('kurun_waktu_start', '<=', $this->yearTo);
                }
                if ($this->createdBy) {
                    $query->where('created_by', $this->createdBy);
                }

                $data = $query->orderBy('created_at', 'desc')->get();

                // Header Utama
                $sheet->setCellValue('A1', 'DAFTAR ARSIP USUL MUSNAH');
                $sheet->mergeCells('A1:H1');
                
                // Style Header Utama
                $headerStyle = [
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
                ];
                $sheet->getStyle('A1')->applyFromArray($headerStyle);

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
                $columnHeaderStyle = [
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
                            'borderStyle' => Border::BORDER_THIN
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ];
                $sheet->getStyle('A2:H2')->applyFromArray($columnHeaderStyle);

                // Isi Data (mulai baris 3)
                $row = 3;
                foreach ($data as $index => $archive) {
                    $sheet->setCellValue('A'.$row, $index + 1);
                    $sheet->setCellValue('B'.$row, $archive->classification->code ?? '-');
                    $sheet->setCellValue('C'.$row, $archive->index_number ?? '-'); // Menggunakan index_number sebagai nomor surat
                    $sheet->setCellValue('D'.$row, $archive->description ?? '-');
                    $sheet->setCellValue('E'.$row, $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('Y') : '-');
                    $sheet->setCellValue('F'.$row, $archive->jumlah_berkas ?? '-');
                    $sheet->setCellValue('G'.$row, $archive->skkad ?? '-');
                    $sheet->setCellValue('H'.$row, $archive->classification->nasib_akhir ?? 'Musnah');
                    
                    $row++;
                }

                // Style Data
                $dataStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ]
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ];
                $lastRow = max($row - 1, 3); // Pastikan minimal sampai baris 3
                $sheet->getStyle('A3:H'.$lastRow)->applyFromArray($dataStyle);

                // Alignment khusus
                $sheet->getStyle('A3:A'.$lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F3:H'.$lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Auto size kolom
                foreach (range('A', 'H') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Set tinggi baris
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
            }
        ];
    }
}