<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengajuanReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;
    protected $reportType;

    public function __construct($data, $reportType)
    {
        $this->data = $data;
        $this->reportType = $reportType;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        if ($this->reportType === 'procurement') {
            return ['No', 'Tanggal', 'Pemohon', 'Nama Barang', 'Tipe', 'Jumlah', 'Estimasi Biaya (Rp)', 'Status'];
        } else {
            return ['No', 'Tanggal', 'Pemohon', 'Nama Aset', 'Nomor Seri', 'Ruangan', 'Deskripsi Kerusakan', 'Status'];
        }
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        if ($this->reportType === 'procurement') {
            return [
                $no,
                $row->created_at->format('Y-m-d H:i'),
                $row->user->name ?? '-',
                $row->item_name,
                ucfirst($row->type),
                $row->qty,
                $row->estimated_price * $row->qty,
                ucwords(str_replace('_', ' ', $row->status)),
            ];
        } else {
            return [
                $no,
                $row->created_at->format('Y-m-d H:i'),
                $row->user->name ?? '-',
                $row->asset->itemInfo->name ?? '-',
                $row->asset->serial_number ?? '-',
                $row->asset->room->name ?? '-',
                $row->damage_description,
                ucwords(str_replace('_', ' ', $row->status)),
            ];
        }
    }
}