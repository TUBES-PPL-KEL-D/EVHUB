<?php

namespace App\Exports;

use App\Models\Spklu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Tambahan interface auto-size
use Maatwebsite\Excel\Concerns\WithStyles;    // Tambahan interface style
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SpkluExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * Mengambil data SPKLU beserta relasi vendornya
    */
    public function collection()
    {
        return Spklu::with('vendor')->get();
    }

    /**
    * Menentukan judul kolom di baris pertama Excel
    */
    public function headings(): array
    {
        return [
            'ID SPKLU',
            'Nama Stasiun',
            'Vendor Pemilik',
            'Alamat Lengkap',
            'Latitude',
            'Longitude',
            'Tanggal Didaftarkan'
        ];
    }

    /**
    * Memetakan data dari database ke dalam kolom Excel
    */
    public function map($spklu): array
    {
        return [
            $spklu->id,
            $spklu->name,
            $spklu->vendor->company_name ?? 'Tidak Diketahui',
            $spklu->address,
            $spklu->latitude,
            $spklu->longitude,
            $spklu->created_at ? $spklu->created_at->format('d M Y H:i') : '-',
        ];
    }

    /**
    * Mengatur dekorasi baris judul kolom (Header) menjadi tebal
    */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}