<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ArchiveExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithCustomStartCell
{
    protected $status;
    protected $year;
    
    public function __construct($status, $year = null)
    {
        $this->status = $status;
        $this->year = $year;
    }

    public function collection()
    {
        $query = Archive::with(['category', 'classification']);
        
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }
        
        if ($this->year) {
            $query->whereRaw('EXTRACT(YEAR FROM kurun_waktu_start) = ?', [$this->year]);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Kode Klasifikasi',
            'Indeks',
            'Uraian',
            'Kurun Waktu',
            'Tingkat Perkembangan',
            'Jumlah',
            'Ket.',
            'Nomor Definitif dan Boks',
            'Nomor Boks',
            'Rak',
            'Baris',
            'Jangka Simpan dan Nasib Akhir',
            'Lokasi Simpan'
        ];
    }

    public function map($archive): array
    {
        static $counter = 0;
        $counter++;
        
        // Format bulan untuk Kurun Waktu
        $kurunWaktu = $archive->kurun_waktu_start ? 
            $archive->kurun_waktu_start->format('F Y') : '';
        
        // Format Jangka Simpan dan Nasib Akhir
        $jangkaSimpan = $archive->retention_active . ' Tahun (Aktif), ' . 
                       $archive->retention_inactive . ' Tahun (Inaktif), ' . 
                       $archive->category->nasib_akhir;
        
        return [
            $counter,
            $archive->classification->code ?? '',
            $archive->index_number ?? '',
            $archive->uraian ?? '',
            $kurunWaktu,
            $archive->tingkat_perkembangan ?? '',
            $archive->jumlah ?? '',
            $archive->ket ?? '',
            '', // Nomor Definitif dan Boks - kosong untuk input manual
            '', // Nomor Boks - kosong untuk input manual
            '', // Rak - kosong untuk input manual
            '', // Baris - kosong untuk input manual
            $jangkaSimpan,
            '' // Lokasi Simpan - kosong untuk input manual
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set width untuk setiap kolom
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(15);  // Kode Klasifikasi
        $sheet->getColumnDimension('C')->setWidth(20);  // Indeks
        $sheet->getColumnDimension('D')->setWidth(30);  // Uraian
        $sheet->getColumnDimension('E')->setWidth(15);  // Kurun Waktu
        $sheet->getColumnDimension('F')->setWidth(15);  // Tingkat Perkembangan
        $sheet->getColumnDimension('G')->setWidth(10);  // Jumlah
        $sheet->getColumnDimension('H')->setWidth(15);  // Ket
        $sheet->getColumnDimension('I')->setWidth(20);  // Nomor Definitif
        $sheet->getColumnDimension('J')->setWidth(12);  // Nomor Boks
        $sheet->getColumnDimension('K')->setWidth(8);   // Rak
        $sheet->getColumnDimension('L')->setWidth(8);   // Baris
        $sheet->getColumnDimension('M')->setWidth(25);  // Jangka Simpan
        $sheet->getColumnDimension('N')->setWidth(15);  // Lokasi Simpan

        // Header styling
        $headerRange = 'A6:N6';
        
        return [
            // Title styling (row 1-5)
            'A1:N1' => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E3F2FD']]
            ],
            'A2:N5' => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E3F2FD']]
            ],
            
            // Header row styling
            $headerRange => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '1976D2']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                ]
            ],
            
            // Data rows styling
            'A7:N1000' => [
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        $statusTitle = $this->getStatusTitle();
        return "Daftar Arsip {$statusTitle}" . ($this->year ? " - {$this->year}" : "");
    }

    public function startCell(): string
    {
        return 'A6'; // Start from row 6 to leave space for title
    }

    private function getStatusTitle(): string
    {
        return match($this->status) {
            'Aktif' => 'Aktif',
            'Inaktif' => 'Inaktif', 
            'Permanen' => 'Permanen',
            'Musnah' => 'Usul Musnah',
            default => 'Semua Status'
        };
    }
} 