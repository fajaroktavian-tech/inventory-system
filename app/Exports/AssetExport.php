<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetExport implements FromCollection, WithHeadings, WithMapping
{
    protected $assets;

    public function __construct($assets)
    {
        $this->assets = $assets;
    }

    public function collection()
    {
        return $this->assets;
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Nomor Seri (SN)',
            'Lokasi',
            'Penanggung Jawab',
            'Kondisi',
            'Status',
            'Sumber Dana',
            'Tahun',
            'Harga'
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->itemInfo->name,
            $asset->serial_number,
            $asset->room->name,
            $asset->pic->name,
            strtoupper($asset->condition),
            strtoupper($asset->status),
            $asset->source_fund,
            $asset->acquisition_year,
            $asset->price,
        ];
    }
}