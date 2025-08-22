<?php

    namespace App\Exports;

    use App\Models\Archive;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithMultipleSheets;
    use Maatwebsite\Excel\Concerns\WithTitle;
    use Maatwebsite\Excel\Concerns\WithEvents;
    use Maatwebsite\Excel\Concerns\WithStyles;
    use Maatwebsite\Excel\Events\AfterSheet;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Border;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Font;
    use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

    class ArchiveStatusExport implements WithMultipleSheets
    {
        protected $status;
        protected $yearFrom;
        protected $yearTo;
        protected $createdBy;
        protected $categoryId;
        protected $classificationId;

        public function __construct($status, $yearFrom = null, $yearTo = null, $createdBy = null, $categoryId = null, $classificationId = null)
        {
            $this->status = $status;
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
                $sheets[] = new ArchiveStatusSheet(
                    $this->status, 
                    null, 
                    $this->createdBy, 
                    $this->categoryId, 
                    $this->classificationId
                );
                return $sheets;
            }
            
            // Untuk tahun tertentu
            $startYear = $this->yearFrom ?: Archive::min('kurun_waktu_start');
            $endYear = $this->yearTo ?: Archive::max('kurun_waktu_start');
            
            // Konversi Carbon ke tahun jika perlu
            if ($startYear instanceof \Carbon\Carbon) {
                $startYear = $startYear->year;
            }
            
            if ($endYear instanceof \Carbon\Carbon) {
                $endYear = $endYear->year;
            }
            
            for ($year = $startYear; $year <= $endYear; $year++) {
                $sheets[] = new ArchiveStatusSheet(
                    $this->status, 
                    $year, 
                    $this->createdBy, 
                    $this->categoryId, 
                    $this->classificationId
                );
            }
            
            return $sheets;
        }
    }

    class ArchiveStatusSheet implements FromCollection, WithTitle, WithEvents, WithStyles
    {
        protected $status;
        protected $year;
        protected $createdBy;
        protected $categoryId;
        protected $classificationId;

        public function __construct($status, $year = null, $createdBy = null, $categoryId = null, $classificationId = null)
        {
            $this->status = $status;
            $this->year = $year;
            $this->createdBy = $createdBy;
            $this->categoryId = $categoryId;
            $this->classificationId = $classificationId;
        }

        public function collection()
        {
            $query = Archive::with([
                'classification' => function($q) {
                    $q->select('id', 'code', 'nama_klasifikasi', 'retention_aktif', 'nasib_akhir');
                }
            ]);

            // Filter status - hanya jika bukan 'all'
            if ($this->status !== 'all') {
                $query->where('status', $this->status);
            }

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

            return $query->orderBy('created_at', 'asc')->get();
        }

        public function title(): string
        {
            return $this->year ? "TAHUN {$this->year}" : "SEMUA TAHUN";
        }

        public function styles(Worksheet $sheet)
        {
            // Set default style for all cells
            $sheet->getStyle('A:M')->applyFromArray([
                'font' => [
                    'name' => 'Arial',
                    'size' => 10,
                ],
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            
            // Hide columns after M
            foreach (range('N', 'Z') as $col) {
                $sheet->getColumnDimension($col)->setWidth(0);
                $sheet->getColumnDimension($col)->setVisible(false);
            }
        }

        public function registerEvents(): array
        {
            return [
                AfterSheet::class => function(AfterSheet $event) {
                    $sheet = $event->sheet->getDelegate();
                    $data = $this->collection();
                    $year = $this->year;

                    // 1. CLEAR ALL CELLS AFTER COLUMN M
                    $highestRow = $sheet->getHighestRow();
                    for ($row = 1; $row <= $highestRow; $row++) {
                        for ($col = 'N'; $col <= 'Z'; $col++) {
                            $sheet->setCellValue($col.$row, null);
                        }
                    }
                    
                    // 2. SET WHITE BACKGROUND FOR AREA AFTER M
                    $sheet->getStyle('N1:Z'.$highestRow)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFFFFF']
                        ]
                    ]);

                    // 3. MAIN HEADERS
                    $headers = [
                        ['DAFTAR ARSIP ' . strtoupper($this->status), 'A1:M1'],
                        ['DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU', 'A2:M2'],
                        ['PROVINSI JAWA TIMUR', 'A3:M3'],
                        ['ISI Bagian....', 'A4:M4'],
                        [$year ? "TAHUN {$year}" : "SEMUA TAHUN", 'A5:M5'],
                        [strtoupper($this->status), 'A6:M6']
                    ];

                    foreach ($headers as $header) {
                        $sheet->setCellValue('A' . substr($header[1], 1, 1), $header[0]);
                        $sheet->mergeCells($header[1]);
                    }

                    // Style Header Utama (Baris 1-6)
                    $event->sheet->getStyle('A1:M6')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'B3E5FC']
                        ],
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['rgb' => '000000']
                            ],
                            'inside' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    ]);

                    // Khusus untuk Sub Bagian PTSP (Baris 4)
                    $event->sheet->getStyle('A4:M4')->getFont()->setItalic(true);
                    $event->sheet->getStyle('A4:M4')->getFont()->setBold(false);

                    // 4. COLUMN HEADERS (Baris 7-8)
                    $columnHeaders = [
                        ['No.', 'A7:A8'],
                        ['Kode Klasifikasi', 'B7:B8'],
                        ['Indeks', 'C7:C8'],
                        ['Uraian', 'D7:D8'],
                        ['Kurun Waktu', 'E7:E8'],
                        ['Tingkat Perkembangan', 'F7:F8'],
                        ['Jumlah', 'G7:G8'],
                        ['Ket.', 'H7:H8'],
                        ['Nomor Definitif dan Boks', 'I7:J7'],
                        ['Lokasi Simpan', 'K7:L7'],
                        ['Jangka Simpan dan Nasib Akhir', 'M7:M8']
                    ];

                    foreach ($columnHeaders as $header) {
                        $sheet->setCellValue(substr($header[1], 0, 2), $header[0]);
                        $sheet->mergeCells($header[1]);
                    }

                    // Sub headers
                    $sheet->setCellValue('I8', 'Nomor Definitif /Berkas');
                    $sheet->setCellValue('J8', 'Nomor Boks');
                    $sheet->setCellValue('K8', 'Rak');
                    $sheet->setCellValue('L8', 'Baris');

                    // Style Header Kolom dengan border
                    $event->sheet->getStyle('A7:M8')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '4472C4']
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

                    // 5. DATA CONTENT
                    $row = 9;
                    $nomorDefinitif = 1;
                    foreach ($data as $index => $archive) {
                        $sheet->setCellValue('A'.$row, $index + 1);
                        $sheet->setCellValue('B'.$row, $archive->classification->code ?? '-');
                        $sheet->setCellValue('C'.$row, $archive->lampiran_surat ?? '-');
                        $sheet->setCellValue('D'.$row, $archive->description ?? '-');
                        $sheet->setCellValue('E'.$row, $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('Y') : '-');
                        $sheet->setCellValue('F'.$row, $archive->tingkat_perkembangan ?? '-');
                        $sheet->setCellValue('G'.$row, $archive->jumlah_berkas ?? '-');
                        $sheet->setCellValue('H'.$row, $archive->ket ?? '(tidak ada keterangan)');
                        $sheet->setCellValue('I'.$row, $nomorDefinitif);
                        $sheet->setCellValue('J'.$row, $archive->box_number ?? '-');
                        $sheet->setCellValue('K'.$row, $archive->rack_number ?? '-');
                        $sheet->setCellValue('L'.$row, $archive->row_number ?? '-');
                        $jangkaSimpan = ($archive->classification->retention_aktif ?? 0) . ' Tahun';
                        $nasibAkhir = $archive->classification->nasib_akhir ?? 'Permanen';
                        $sheet->setCellValue('M'.$row, $jangkaSimpan . ' (' . $nasibAkhir . ')');
                        $row++;
                        $nomorDefinitif++;
                    }

                    // Style Data dengan border
                    $lastRow = max($row - 1, 9);
                    $event->sheet->getStyle('A9:M'.$lastRow)->applyFromArray([
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
                    $event->sheet->getStyle('A9:A'.$lastRow)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $event->sheet->getStyle('E9:M'.$lastRow)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // 6. SET COLUMN WIDTHS
                    $columnWidths = [
                        'A' => 5,   'B' => 15,  'C' => 25,
                        'D' => 40,  'E' => 15,  'F' => 20,
                        'G' => 10,  'H' => 15,  'I' => 20,
                        'J' => 15,  'K' => 10,  'L' => 10,
                        'M' => 30
                    ];

                    foreach ($columnWidths as $col => $width) {
                        $sheet->getColumnDimension($col)->setWidth($width);
                    }

                    // 7. SET ROW HEIGHTS
                    for ($i = 1; $i <= 8; $i++) {
                        $sheet->getRowDimension($i)->setRowHeight(25);
                    }

                    // 8. ADD RIGHT BORDER TO COLUMN M
                    $event->sheet->getStyle('M1:M'.$lastRow)->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    ]);
                }
            ];
        }
    }