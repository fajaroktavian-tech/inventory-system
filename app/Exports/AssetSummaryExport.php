<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetSummaryExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function collection() {
        return $this->data->map(function($item) {
            return [
                $item->name,
                $item->brand,
                $item->total_unit,
                $item->kondisi_baik,
                $item->kondisi_rusak,
            ];
        });
    }

    public function headings(): array {
        return ["Nama Barang", "Merk", "Total Unit", "Kondisi Baik", "Kondisi Rusak"];
    }
}