<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ArchiveAktifExport implements WithEvents
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

                // Query data arsip aktif
                $query = Archive::with(['classification' => function($q) {
                    $q->select('id', 'code', 'nama_klasifikasi');
                }])->where('status', 'Aktif');

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
                $sheet->setCellValue('A1', 'DAFTAR BERKAS AKTIF');
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'B3E5FC']
                    ],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_MEDIUM]
                    ]
                ]);

                // Header Baris 2
                $headersRow2 = [
                    'NO', 
                    'NO. BERKAS',
                    'KODE KLASIFIKASI DAN INDEKS',
                    'URAIAN INFORMASI',
                    'KURUN WAKTU',
                    'JUMLAH',
                    'SKKAD',
                    'PENYIMPANAN', '', ''
                ];

                foreach ($headersRow2 as $key => $header) {
                    $cell = chr(65 + $key) . '2';
                    $sheet->setCellValue($cell, $header);
                }

                // Merge kolom PENYIMPANAN (H2:J2)
                $sheet->mergeCells('H2:J2');

                // Header Baris 3 (sub header dari PENYIMPANAN)
                $sheet->setCellValue('H3', 'RAK');
                $sheet->setCellValue('I3', 'BARIS');
                $sheet->setCellValue('J3', 'BOX');

                // Merge vertikal kolom A–G
                foreach (range('A', 'G') as $col) {
                    $sheet->mergeCells("{$col}2:{$col}3");
                }

                // Style untuk Header (A2:J3)
                $sheet->getStyle('A2:J3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '4472C4']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);

                // Data dimulai dari baris 4
                $row = 4;
                foreach ($data as $index => $archive) {
                    $sheet->setCellValue('A'.$row, $index + 1);
                    $sheet->setCellValue('B'.$row, $archive->index_number ?? '-');

                    $kodeIndeks = ($archive->classification->code ?? '-') . ' - ' .
                                 ($archive->classification->keterangan ?? $archive->classification->nama_klasifikasi ?? '-');
                    $sheet->setCellValue('C'.$row, $kodeIndeks);

                    $sheet->setCellValue('D'.$row, $archive->description ?? '-');
                    $sheet->setCellValue('E'.$row, $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('Y') : '-');
                    $sheet->setCellValue('F'.$row, $archive->jumlah_berkas ?? '-');
                    $sheet->setCellValue('G'.$row, $archive->skkad ?? '-');

                    $sheet->setCellValue('H'.$row, $archive->rak ?? '-');
                    $sheet->setCellValue('I'.$row, $archive->baris ?? '-');
                    $sheet->setCellValue('J'.$row, $archive->box ?? '-');
                    $row++;
                }

                // Styling data
                $lastRow = $row - 1;
                $sheet->getStyle("A4:J{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);

                // Horizontal alignment
                $sheet->getStyle("A4:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F4:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(10);
                $sheet->getColumnDimension('I')->setWidth(10);
                $sheet->getColumnDimension('J')->setWidth(10);

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(25);
            }
        ];
    }
}