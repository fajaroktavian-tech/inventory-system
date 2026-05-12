<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Mengambil semua data barang beserta kategorinya (Eager Loading)
        return Item::with('category')->get();
    }

    // Menentukan Header di Excel
    public function headings(): array
    {
        return [
            'No',
            'Nama Barang',
            'Kategori',
            'Stok Saat Ini',
            'Satuan',
            'Minimal Stok',
        ];
    }

    private $rowNumber = 0;

    // Menentukan data apa saja yang masuk ke kolom
    public function map($item): array
    {
        return [
            ++$this->rowNumber,
            $item->name,
            $item->category->name ?? 'Tanpa Kategori',
            $item->stock,
            $item->unit,
            $item->min_stock,
        ];
    }
}