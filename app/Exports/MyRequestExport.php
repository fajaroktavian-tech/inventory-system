<?php

namespace App\Exports;

use App\Models\RequestModel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MyRequestExport implements FromQuery, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $status;
    protected $date;

    public function __construct($status, $date)
    {
        $this->status = $status;
        $this->date = $date;
    }

    public function query()
    {
        $query = RequestModel::query()->where('user_id', auth()->id());

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->date) {
            $query->whereDate('request_date', $this->date);
        }

        return $query->with(['details.item', 'room', 'class']);
    }

    // Mapping data ke kolom Excel
    public function map($request): array
    {
        $items = $request->details->map(function ($detail) {
            return $detail->item->name . ' (' . $detail->quantity_requested . ')';
        })->implode(', ');

        return [
            $request->request_date,
            $request->type === 'class' ? $request->class->name : $request->room->name,
            $items,
            strtoupper($request->status),
            $request->notes,
        ];
    }

    // Header Excel
    public function headings(): array
    {
        return [
            'Tanggal',
            'Tujuan (Kelas/Ruangan)',
            'Daftar Barang',
            'Status',
            'Catatan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}