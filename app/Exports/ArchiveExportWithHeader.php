<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ArchiveExportWithHeader implements WithEvents
{
    protected $status;
    protected $yearFrom;
    protected $yearTo;
    protected $createdBy;

    public function __construct($status, $yearFrom = null, $yearTo = null, $createdBy = null)
    {
        $this->status = $status;
        $this->yearFrom = $yearFrom;
        $this->yearTo = $yearTo;
        $this->createdBy = $createdBy;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Get data first
                $query = Archive::with(['category', 'classification', 'createdByUser']);

                // Apply status filter
                if ($this->status && $this->status !== 'all') {
                    $query->where('status', $this->status);
                }

                // Apply year filters
                if ($this->yearFrom) {
                    $query->whereYear('kurun_waktu_start', '>=', $this->yearFrom);
                }

                if ($this->yearTo) {
                    $query->whereYear('kurun_waktu_start', '<=', $this->yearTo);
                }

                // Apply created by filter
                if ($this->createdBy) {
                    $query->where('created_by', $this->createdBy);
                }

                $data = $query->orderBy('created_at', 'desc')->get();

                // Set column widths
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

                // Note: Data will be written properly starting from row 10 to avoid header conflicts

                // Add header content
                $statusTitle = $this->getStatusTitle();

                // Generate year text based on filter
                if ($this->yearFrom && $this->yearTo) {
                    $yearText = "TAHUN {$this->yearFrom} - {$this->yearTo}";
                } elseif ($this->yearFrom) {
                    $yearText = "TAHUN {$this->yearFrom}";
                } elseif ($this->yearTo) {
                    $yearText = "TAHUN {$this->yearTo}";
                } else {
                    $yearText = "SEMUA TAHUN";
                }

                // Row 1: Main title
                $sheet->setCellValue('A1', "DAFTAR ARSIP " . strtoupper($statusTitle));
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

                // Row 5: Year (dynamic based on filter)
                $sheet->setCellValue('A5', $yearText);
                $sheet->mergeCells('A5:M5');

                // Row 6: Status
                $sheet->setCellValue('A6', strtoupper($statusTitle));
                $sheet->mergeCells('A6:M6');

                // Row 7: Empty row for spacing
                $sheet->setCellValue('A7', '');

                // Header rows (row 8-9) - Complex headers with merged cells
                // Row 8: Main headers
                $sheet->setCellValue('A8', 'No.');
                $sheet->mergeCells('A8:A9'); // Merge No. vertically

                $sheet->setCellValue('B8', 'Kode Klasifikasi');
                $sheet->mergeCells('B8:B9'); // Merge Kode Klasifikasi vertically

                $sheet->setCellValue('C8', 'Indeks');
                $sheet->mergeCells('C8:C9'); // Merge Indeks vertically

                $sheet->setCellValue('D8', 'Uraian');
                $sheet->mergeCells('D8:D9'); // Merge Uraian vertically

                $sheet->setCellValue('E8', 'Kurun Waktu');
                $sheet->mergeCells('E8:E9'); // Merge Kurun Waktu vertically

                $sheet->setCellValue('F8', 'Tingkat Perkembangan');
                $sheet->mergeCells('F8:F9'); // Merge Tingkat Perkembangan vertically

                $sheet->setCellValue('G8', 'Jumlah');
                $sheet->mergeCells('G8:G9'); // Merge Jumlah vertically

                $sheet->setCellValue('H8', 'Ket.');
                $sheet->mergeCells('H8:H9'); // Merge Ket vertically

                // Complex header for Nomor Definitif dan Boks
                $sheet->setCellValue('I8', 'Nomor Definitif dan Boks');
                $sheet->mergeCells('I8:J8'); // Merge horizontally
                $sheet->setCellValue('I9', 'Nomor Definitif');
                $sheet->setCellValue('J9', 'Nomor Boks');

                // Complex header for Lokasi Simpan
                $sheet->setCellValue('K8', 'Lokasi Simpan');
                $sheet->mergeCells('K8:L8'); // Merge horizontally
                $sheet->setCellValue('K9', 'Rak');
                $sheet->setCellValue('L9', 'Baris');

                $sheet->setCellValue('M8', 'Jangka Simpan dan Nasib Akhir');
                $sheet->mergeCells('M8:M9'); // Merge vertically

                // Add data starting from row 10
                $row = 10;
                $counter = 1;
                foreach ($data as $archive) {
                    // Format kurun waktu to only show year
                    $kurunWaktu = $archive->kurun_waktu_start ?
                        $archive->kurun_waktu_start->format('Y') : '';

                    // Format jangka simpan
                    $jangkaSimpan = ($archive->classification->retention_aktif ?? '0') . ' Tahun (' .
                                   ($archive->classification->nasib_akhir ?? 'Permanen') . ')';

                    // Set values as text to avoid timestamp issues
                    $sheet->setCellValueExplicit("A{$row}", $counter, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("B{$row}", $archive->classification->code ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("C{$row}", $archive->index_number ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("D{$row}", $archive->description ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("E{$row}", $kurunWaktu, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("F{$row}", $archive->tingkat_perkembangan ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("G{$row}", $archive->jumlah_berkas ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("H{$row}", $archive->description ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("I{$row}", '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); // Empty for manual input
                    $sheet->setCellValueExplicit("J{$row}", '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); // Empty for manual input
                    $sheet->setCellValueExplicit("K{$row}", '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); // Empty for manual input
                    $sheet->setCellValueExplicit("L{$row}", '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); // Empty for manual input
                    $sheet->setCellValueExplicit("M{$row}", $jangkaSimpan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    $row++;
                    $counter++;
                }

                // Apply styles
                // Title section styling (rows 1-6)
                $sheet->getStyle('A1:M6')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'B3E5FC']]
                ]);

                // Header rows styling (row 8-9)
                $sheet->getStyle('A8:M9')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '81C784']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]
                    ]
                ]);

                // Data section styling
                $lastRow = $row - 1;
                if ($lastRow >= 10) {
                    $sheet->getStyle("A10:M{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
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

    private function getStatusTitle(): string
    {
        return match($this->status) {
            'aktif', 'Aktif' => 'Aktif',
            'inaktif', 'Inaktif' => 'Inaktif',
            'permanen', 'Permanen' => 'Permanen',
            'musnah', 'Musnah' => 'Usul Musnah',
            'all', null, '' => 'Semua Status',
            default => 'Semua Status'
        };
    }
}
