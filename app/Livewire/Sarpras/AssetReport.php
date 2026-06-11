<?php

namespace App\Livewire\Sarpras;

use App\Models\Asset;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Exports\AssetSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AssetReport extends Component
{
    public $search = '';
    use WithPagination;

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        $assetSummary = Asset::query()
            ->join('asset_items', 'assets.asset_item_id', '=', 'asset_items.id')
            ->select(
                'asset_items.name',
                'asset_items.brand',
                'asset_items.specification',
                DB::raw('count(*) as total_unit'),
                DB::raw("SUM(CASE WHEN assets.condition = 'baik' THEN 1 ELSE 0 END) as kondisi_baik"),
                DB::raw("SUM(CASE WHEN assets.condition != 'baik' THEN 1 ELSE 0 END) as kondisi_rusak")
            )
            // Logic Search
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('asset_items.name', 'like', '%' . $this->search . '%')
                        ->orWhere('asset_items.brand', 'like', '%' . $this->search . '%');
                });
            })
            ->groupBy('asset_items.id', 'asset_items.name', 'asset_items.brand', 'asset_items.specification')
            ->orderBy('total_unit', 'desc')
            ->paginate(10);

        return view('livewire.asset-report', [
            'assetSummary' => $assetSummary
        ])->layout('layouts.app');
    }

    public function getAssetSummaryData()
    {
        return Asset::query()
            ->join('asset_items', 'assets.asset_item_id', '=', 'asset_items.id')
            ->select(
                'asset_items.name',
                'asset_items.brand',
                'asset_items.specification',
                DB::raw('count(*) as total_unit'),
                DB::raw("SUM(CASE WHEN assets.condition = 'baik' THEN 1 ELSE 0 END) as kondisi_baik"),
                DB::raw("SUM(CASE WHEN assets.condition != 'baik' THEN 1 ELSE 0 END) as kondisi_rusak")
            )
            ->when($this->search, function ($query) {
                $query->where('asset_items.name', 'like', '%' . $this->search . '%')
                    ->orWhere('asset_items.brand', 'like', '%' . $this->search . '%');
            })
            ->groupBy('asset_items.id', 'asset_items.name', 'asset_items.brand', 'asset_items.specification')
            ->orderBy('total_unit', 'desc')
            ->get();
    }

    public function exportPDF()
    {
        $data = $this->getAssetSummaryData();
        $pdf = Pdf::loadView('pdf.asset-report-pdf', [
            'assetSummary' => $data,
            'date' => now()->format('d/m/Y')
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan_Rekap_Aset_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        // Untuk excel, kita bisa buat class Export sederhana
        return Excel::download(new AssetSummaryExport($this->getAssetSummaryData()), 'Rekap_Aset.xlsx');
    }
}