<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ArchiveInaktifPermanenExport implements WithEvents
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

                // Query data dengan relasi yang diperlukan
                $query = Archive::with([
                    'classification' => function($q) {
                        $q->select('id', 'code', 'nama_klasifikasi', 'retention_aktif', 'nasib_akhir');
                    }
                ])->where('status', $this->status);

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

                // HEADER UTAMA
                // Baris 1: Judul
                $sheet->setCellValue('A1', 'DAFTAR ARSIP ' . strtoupper($this->status));
                $sheet->mergeCells('A1:M1');
                
                // Baris 2: Dinas
                $sheet->setCellValue('A2', 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU');
                $sheet->mergeCells('A2:M2');
                
                // Baris 3: Provinsi
                $sheet->setCellValue('A3', 'PROVINSI JAWA TIMUR');
                $sheet->mergeCells('A3:M3');
                
                // Baris 4: Sub Bagian (dengan font tipis dan italic)
                $sheet->setCellValue('A4', 'SUB BAGIAN PTSP');
                $sheet->mergeCells('A4:M4');
                
                // Baris 5: Tahun
                $yearText = $this->yearFrom && $this->yearTo ? 
                    "TAHUN {$this->yearFrom} - {$this->yearTo}" : 
                    ($this->yearFrom ? "TAHUN {$this->yearFrom}" : 
                    ($this->yearTo ? "TAHUN {$this->yearTo}" : "TAHUN .........."));
                $sheet->setCellValue('A5', $yearText);
                $sheet->mergeCells('A5:M5');
                
                // Baris 6: Status
                $sheet->setCellValue('A6', strtoupper($this->status));
                $sheet->mergeCells('A6:M6');

                // Style Header Utama (Baris 1-6)
                $sheet->getStyle('A1:M6')->applyFromArray([
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
                        'color' => ['rgb' => 'B3E5FC'] // Warna biru muda
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM
                        ],
                        'inside' => [
                            'borderStyle' => Border::BORDER_THIN
                        ]
                    ]
                ]);

                // Khusus untuk Sub Bagian PTSP (Baris 4)
                $sheet->getStyle('A4:M4')->getFont()->setItalic(true);
                $sheet->getStyle('A4:M4')->getFont()->setBold(false);

                // HEADER KOLOM (Baris 7-8)
                // Baris 7
                $sheet->setCellValue('A7', 'No.');
                $sheet->mergeCells('A7:A8');
                
                $sheet->setCellValue('B7', 'Kode Klasifikasi');
                $sheet->mergeCells('B7:B8');
                
                $sheet->setCellValue('C7', 'Indeks');
                $sheet->mergeCells('C7:C8');
                
                $sheet->setCellValue('D7', 'Uraian');
                $sheet->mergeCells('D7:D8');
                
                $sheet->setCellValue('E7', 'Kurun Waktu');
                $sheet->mergeCells('E7:E8');
                
                $sheet->setCellValue('F7', 'Tingkat Perkembangan');
                $sheet->mergeCells('F7:F8');
                
                $sheet->setCellValue('G7', 'Jumlah');
                $sheet->mergeCells('G7:G8');
                
                $sheet->setCellValue('H7', 'ket');
                $sheet->mergeCells('H7:H8');
                
                $sheet->setCellValue('I7', 'Nomor Definitif dan Boks');
                $sheet->mergeCells('I7:J7');
                
                $sheet->setCellValue('K7', 'Lokasi Simpan');
                $sheet->mergeCells('K7:L7');
                
                $sheet->setCellValue('M7', 'Jangka Simpan dan Nasib Akhir');
                $sheet->mergeCells('M7:M8');

                // Baris 8 (sub header)
                $sheet->setCellValue('I8', 'Nomor Definitif /Berkas');
                $sheet->setCellValue('J8', 'Nomor Boks');
                $sheet->setCellValue('K8', 'Rak');
                $sheet->setCellValue('L8', 'Baris');

                // Style Header Kolom dengan border
                $sheet->getStyle('A7:M8')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'] // Teks putih
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '4472C4'] // Warna biru tua
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

                // ISI DATA (Mulai dari Baris 9)
                $row = 9;
                foreach ($data as $index => $archive) {
                    $sheet->setCellValue('A'.$row, $index + 1);
                    $sheet->setCellValue('B'.$row, $archive->classification->code ?? '-');
                    
                    // Indeks diambil dari keterangan kode klasifikasi
                    $indeks = $archive->classification->keterangan ?? $archive->classification->nama_klasifikasi ?? '-';
                    $sheet->setCellValue('C'.$row, $indeks);
                    
                    $sheet->setCellValue('D'.$row, $archive->description ?? '-');
                    $sheet->setCellValue('E'.$row, $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('Y') : '-');
                    $sheet->setCellValue('F'.$row, $archive->tingkat_perkembangan ?? '-');
                    $sheet->setCellValue('G'.$row, $archive->jumlah_berkas ?? '-');
                    
                    // Keterangan diambil langsung dari model Archive
                    $sheet->setCellValue('H'.$row, $archive->ket ?? '(tidak ada keterangan)');
                    $sheet->setCellValue('I'.$row, $archive->nomor_definitif ?? '-');
                    $sheet->setCellValue('J'.$row, $archive->box ?? '-');
                    $sheet->setCellValue('K'.$row, $archive->rak ?? '-');
                    $sheet->setCellValue('L'.$row, $archive->baris ?? '-');
                    
                    // Jangka Simpan dan Nasib Akhir
                    $jangkaSimpan = ($archive->classification->retention_aktif ?? 0) . ' Tahun';
                    $nasibAkhir = $archive->classification->nasib_akhir ?? 'Permanen';
                    $sheet->setCellValue('M'.$row, $jangkaSimpan . ' (' . $nasibAkhir . ')');
                    
                    $row++;
                }

                // Style Data dengan border
                $lastRow = max($row - 1, 9);
                $sheet->getStyle('A9:M'.$lastRow)->applyFromArray([
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
                $sheet->getStyle('A9:A'.$lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E9:M'.$lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Set lebar kolom
                $sheet->getColumnDimension('A')->setWidth(5);   // No
                $sheet->getColumnDimension('B')->setWidth(15);  // Kode Klasifikasi
                $sheet->getColumnDimension('C')->setWidth(25);  // Indeks (diperlebar untuk keterangan klasifikasi)
                $sheet->getColumnDimension('D')->setWidth(40);  // Uraian
                $sheet->getColumnDimension('E')->setWidth(15);  // Kurun Waktu
                $sheet->getColumnDimension('F')->setWidth(20);  // Tingkat Perkembangan
                $sheet->getColumnDimension('G')->setWidth(10);  // Jumlah
                $sheet->getColumnDimension('H')->setWidth(15);  // Ket.
                $sheet->getColumnDimension('I')->setWidth(20);  // Nomor Definitif
                $sheet->getColumnDimension('J')->setWidth(15);  // Nomor Boks
                $sheet->getColumnDimension('K')->setWidth(10);  // Rak
                $sheet->getColumnDimension('L')->setWidth(10);  // Baris
                $sheet->getColumnDimension('M')->setWidth(30);  // Jangka Simpan

                // Set tinggi baris
                for ($i = 1; $i <= 8; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(25);
                }
            }
        ];
    }
}