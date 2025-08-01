<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BulkArchiveExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $archiveIds;

    public function __construct($archiveIds)
    {
        $this->archiveIds = $archiveIds;
    }

    public function collection()
    {
        return Archive::with(['category', 'classification', 'createdByUser'])
            ->whereIn('id', $this->archiveIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Klasifikasi',
            'Indeks/Lampiran Arsip',
            'Uraian Arsip',
            'Kurun Waktu',
            'Tingkat Perkembangan',
            'Jumlah',
            'Keterangan',
            'Jangka Simpan',
            'Nasib Akhir',
            'Nomor Definitif',
            'Nomor Boks',
            'Rak',
            'Baris',
            'Status',
            'Dibuat Oleh',
            'Tanggal Input'
        ];
    }

    public function map($archive): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $archive->classification->code ?? '',
            $archive->index_number ?? '',
            $archive->description ?? '',
            $archive->kurun_waktu_start ?
                $archive->kurun_waktu_start->format('F Y') .
                ($archive->kurun_waktu_end ? ' - ' . $archive->kurun_waktu_end->format('F Y') : '')
                : '',
            $archive->tingkat_perkembangan ?? '',
            $archive->jumlah_berkas ?? '',
            $archive->keterangan ?? '',
            'Aktif: ' . ($archive->retention_aktif ?? 0) . ' tahun, Inaktif: ' . ($archive->retention_inaktif ?? 0) . ' tahun',
            $archive->category->nasib_akhir ?? '',
            '', // Nomor Definitif - empty for manual input
            '', // Nomor Boks - empty for manual input
            '', // Rak - empty for manual input
            '', // Baris - empty for manual input
            $archive->status,
            $archive->createdByUser->name ?? '',
            $archive->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Header styling
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Data styling
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ]
        ]);

        // Auto-width for columns
        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set minimum row height
        for ($row = 2; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        return [];
    }

    public function title(): string
    {
        return 'Bulk Export Arsip';
    }
}
