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
                $query = Archive::with(['category', 'classification', 'createdByUser'])
                    ->whereIn('status', ['aktif', 'inaktif', 'permanen']);

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

                // Determine if SKKD column should be shown
                $showSkkd = strtolower($this->status) === 'aktif';

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(5);   // No
                $sheet->getColumnDimension('B')->setWidth(12);  // Kode Klasifikasi
                $sheet->getColumnDimension('C')->setWidth(25);  // Indeks
                $sheet->getColumnDimension('D')->setWidth(35);  // Uraian
                $sheet->getColumnDimension('E')->setWidth(15);  // Kurun Waktu
                $sheet->getColumnDimension('F')->setWidth(15);  // Tingkat Perkembangan
                
                if ($showSkkd) {
                    $sheet->getColumnDimension('G')->setWidth(15);  // SKKD
                    $sheet->getColumnDimension('H')->setWidth(10);  // Jumlah
                    $sheet->getColumnDimension('I')->setWidth(15);  // Ket
                    $sheet->getColumnDimension('J')->setWidth(12);  // Nomor Definitif
                    $sheet->getColumnDimension('K')->setWidth(12);  // Nomor Boks
                    $sheet->getColumnDimension('L')->setWidth(8);   // Rak
                    $sheet->getColumnDimension('M')->setWidth(8);   // Baris
                    $sheet->getColumnDimension('N')->setWidth(25);  // Jangka Simpan
                    $lastColumn = 'N';
                } else {
                    $sheet->getColumnDimension('G')->setWidth(10);  // Jumlah
                    $sheet->getColumnDimension('H')->setWidth(15);  // Ket
                    $sheet->getColumnDimension('I')->setWidth(12);  // Nomor Definitif
                    $sheet->getColumnDimension('J')->setWidth(12);  // Nomor Boks
                    $sheet->getColumnDimension('K')->setWidth(8);   // Rak
                    $sheet->getColumnDimension('L')->setWidth(8);   // Baris
                    $sheet->getColumnDimension('M')->setWidth(25);  // Jangka Simpan
                    $lastColumn = 'M';
                }

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
                $sheet->setCellValue('A1', "DAFTAR BERKAS " . strtoupper($statusTitle));
                $sheet->mergeCells("A1:{$lastColumn}1");

                // Row 2: Department
                $sheet->setCellValue('A2', 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU');
                $sheet->mergeCells("A2:{$lastColumn}2");

                // Row 3: Province
                $sheet->setCellValue('A3', 'PROVINSI JAWA TIMUR');
                $sheet->mergeCells("A3:{$lastColumn}3");

                // Row 4: Sub department
                $sheet->setCellValue('A4', 'SUB BAGIAN PTSP');
                $sheet->mergeCells("A4:{$lastColumn}4");

                // Row 5: Year (dynamic based on filter)
                $sheet->setCellValue('A5', $yearText);
                $sheet->mergeCells("A5:{$lastColumn}5");

                // Row 6: Status
                $sheet->setCellValue('A6', strtoupper($statusTitle));
                $sheet->mergeCells("A6:{$lastColumn}6");

                // Header rows (row 7-8)
                // Row 7: Main headers
                $sheet->setCellValue('A7', 'No.');
                $sheet->mergeCells('A7:A8');

                $sheet->setCellValue('B7', 'NO. BERKAS');
                $sheet->mergeCells('B7:B8');

                $sheet->setCellValue('C7', 'KODE KLASIFIKASI DAN INDEKS');
                $sheet->mergeCells('C7:C8');

                $sheet->setCellValue('D7', 'URAIAN INFORMASI');
                $sheet->mergeCells('D7:D8');

                $sheet->setCellValue('E7', 'KURUN WAKTU');
                $sheet->mergeCells('E7:E8');

                $sheet->setCellValue('F7', 'JUMLAH');
                $sheet->mergeCells('F7:F8');

                if ($showSkkd) {
                    $sheet->setCellValue('G7', 'KETERANGAN (SKKAD)');
                    $sheet->mergeCells('G7:G8');

                    $sheet->setCellValue('H7', 'PENYIMPANAN');
                    $sheet->mergeCells('H7:H8');
                    $lastColumn = 'H';
                } else {
                    $sheet->setCellValue('G7', 'KETERANGAN');
                    $sheet->mergeCells('G7:G8');

                    $sheet->setCellValue('H7', 'PENYIMPANAN');
                    $sheet->mergeCells('H7:H8');
                    $lastColumn = 'H';
                }

                // Add data starting from row 9
                $row = 9;
                $counter = 1;
                foreach ($data as $archive) {
                    $kurunWaktu = $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('Y') : '';
                    
                    $sheet->setCellValueExplicit("A{$row}", $counter, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("B{$row}", $archive->nomor_definitif ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("C{$row}", ($archive->classification->code ?? '') . ' - ' . ($archive->index_number ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("D{$row}", $archive->description ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("E{$row}", $kurunWaktu, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("F{$row}", $archive->jumlah_berkas ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($showSkkd) {
                        $sheet->setCellValueExplicit("G{$row}", $archive->skkd ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("H{$row}", 'Rak ' . $archive->rak . ' Baris ' . $archive->baris, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    } else {
                        $sheet->setCellValueExplicit("G{$row}", $archive->keterangan ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("H{$row}", 'Rak ' . $archive->rak . ' Baris ' . $archive->baris, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }

                    $row++;
                    $counter++;
                }

                // Apply styles
                // Title section styling (rows 1-6)
                $sheet->getStyle("A1:{$lastColumn}6")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'B3E5FC']],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_MEDIUM],
                        'inside' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                
                // Header rows styling (row 7-8)
                $sheet->getStyle("A7:{$lastColumn}8")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '81C784']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]
                    ]
                ]);

                // Data section styling
                $lastRow = $row - 1;
                if ($lastRow >= 9) {
                    $sheet->getStyle("A9:{$lastColumn}{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
                    ]);

                    // Add border for the entire table
                    $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_MEDIUM]
                        ]
                    ]);
                }

                // Set row heights
                for ($i = 1; $i <= 6; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(20);
                }
                $sheet->getRowDimension(7)->setRowHeight(25);
                $sheet->getRowDimension(8)->setRowHeight(25);
            }
        ];
    }

    private function getStatusTitle(): string
    {
        return match(strtolower($this->status)) {
            'aktif' => 'Aktif',
            'inaktif' => 'Inaktif',
            'permanen' => 'Permanen',
            'all', null, '' => 'Semua Status',
            default => 'Semua Status'
        };
    }
}