<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArchiveExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Archive::with(['kategori', 'klasifikasi'])->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Definitif',
            'Nama Arsip',
            'Kategori',
            'Klasifikasi',
            'Kurun Waktu',
            'Jumlah',
            'Tingkat Perkembangan',
            'skkd',
            'Retensi Aktif',
            'Retensi Inaktif',
            'Keterangan Retensi',
            'Rak',
            'Baris',
            'Box',
            'Lokasi Simpan',
        ];
    }

    public function map($archive): array
    {
        static $no = 1;
        return [
            $no++,
            $archive->nomor_definitif,
            $archive->nama_arsip,
            $archive->kategori->nama_kategori ?? '-',
            $archive->klasifikasi->nama_klasifikasi ?? '-',
            $archive->kurun_waktu,
            $archive->jumlah,
            $archive->tingkat_perkembangan,
            $archive->skkd,     
            $archive->retensi_aktif,
            $archive->retensi_inaktif,
            $archive->keterangan_retensi,
            $archive->rak,
            $archive->baris,
            $archive->box,
            'Rak ' . $archive->rak . ' Baris ' . $archive->baris,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Bold header
        ];
    }
}

