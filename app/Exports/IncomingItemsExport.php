<?php

namespace App\Exports;

use App\Models\ItemIncoming;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncomingItemsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $startDate, $endDate, $search;

    public function __construct($startDate, $endDate, $search)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->search = $search;
    }

    public function query()
    {
        return ItemIncoming::with(['item', 'user'])
            ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
            ->whereHas('item', fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->latest();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Nama Barang', 'Jumlah', 'Satuan', 'Petugas', 'Keterangan'];
    }

    public function map($incoming): array
    {
        return [
            \Carbon\Carbon::parse($incoming->date)->format('d/m/Y'),
            $incoming->item->name,
            $incoming->quantity,
            $incoming->item->unit,
            $incoming->user->name,
            $incoming->description ?? '-',
        ];
    }
}