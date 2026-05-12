<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\RequestDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventoryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $tab;
    protected $start;
    protected $end;

    // Terima parameter dari Component
    public function __construct($tab, $start, $end)
    {
        $this->tab = $tab;
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        if ($this->tab === 'stok') {
            return Item::with('category')->get();
        } 
        
        if ($this->tab === 'rekap') {
            return Item::withSum('incomings as total_masuk', 'quantity')
                ->withSum(['details as total_keluar' => function($query) {
                    $query->whereHas('request', fn($q) => $q->where('status', 'approved'));
                }], 'quantity_approved')
                ->get();
        }

        if ($this->tab === 'keluar') {
            return RequestDetail::with(['request.student', 'request.class', 'item'])
                ->whereHas('request', function ($q) {
                    $q->where('status', 'approved')
                        ->whereBetween('request_date', [$this->start, $this->end]);
                })->get();
        }

        return Item::all();
    }

    public function headings(): array
    {
        if ($this->tab === 'stok') {
            return ['Nama Barang', 'Kategori', 'Stok Saat Ini', 'Satuan'];
        }
        if ($this->tab === 'rekap') {
            return ['Nama Barang','Kategori', 'Total Masuk', 'Total Keluar', 'Sisa Stok'];
        }
        if ($this->tab === 'keluar') {
            return ['Tanggal', 'Penerima', 'Kelas/Ruang', 'Nama Barang', 'Jumlah'];
        }
        return [];
    }

    public function map($row): array
    {
        if ($this->tab === 'stok') {
            return [
                $row->name,
                $row->category->name ?? '-',
                $row->stock,
                $row->unit
            ];
        }
        if ($this->tab === 'rekap') {
            return [
                $row->name,
                $row->category->name ?? '-',
                $row->total_masuk ?? 0,
                $row->total_keluar ?? 0,
                $row->stock . ' ' . $row->unit
            ];
        }
        if ($this->tab === 'keluar') {
            return [
                $row->request->request_date,
                $row->request->student->name ?? 'User',
                $row->request->class->name ?? ($row->request->room->name ?? '-'),
                $row->item->name,
                $row->quantity_approved . ' ' . $row->item->unit
            ];
        }
        return [];
    }
}
