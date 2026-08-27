<?php

namespace App\Livewire\Sarpras;

use App\Exports\PengajuanReportExport;
use App\Models\AssetProcurement;
use App\Models\AssetMaintenance;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class AssetReportIndex extends Component
{
    public $startDate;
    public $endDate;
    public $reportType = 'procurement'; // procurement atau maintenance

    public function mount()
    {
        // Default rentang tanggal bulan ini
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');

        // Validasi akses tambahan di controller
        if (!in_array(Auth::user()->role, ['admin', 'petugas', 'owner'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function exportPdf()
    {
        $data = $this->getQueryData();

        $pdf = Pdf::loadView('pdf.pengajuan-report-pdf', [
            'reportData' => $data,
            'reportType' => $this->reportType,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-sarpras-' . $this->reportType . '-' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        $data = $this->getQueryData();
        $fileName = 'laporan-sarpras-' . $this->reportType . '-' . date('Y-m-d') . '.xlsx';

        return Excel::download(new PengajuanReportExport($data, $this->reportType), $fileName);
    }

    public function render()
    {
        $data = $this->getQueryData();
        
        $totalCost = $this->reportType === 'procurement' 
            ? $data->sum(fn($item) => $item->estimated_price * $item->qty)
            : 0;

        // Hitung metrik status dari koleksi data yang sudah terfilter
        $totalCount = $data->count();
        $pendingCount = $data->where('status', 'pending')->count();
        
        // Untuk disetujui, bisa mencakup 'approved' atau 'repaired'
        $approvedCount = $data->filter(fn($item) => in_array($item->status, ['approved', 'repaired']))->count();
        
        $rejectedCount = $data->where('status', 'rejected')->count();

        return view('livewire.sarpras.asset-report-index', [
            'reportData' => $data,
            'totalCost' => $totalCost,
            'totalCount' => $totalCount,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
        ])->layout('layouts.app');
    }

    private function getQueryData()
    {
        $query = $this->reportType === 'procurement'
            ? AssetProcurement::with('user')
            : AssetMaintenance::with(['user', 'asset.itemInfo', 'asset.room']);

        return $query->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->latest()
            ->get();
    }
}
