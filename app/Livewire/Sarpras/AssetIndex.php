<?php

namespace App\Livewire\Sarpras;

use App\Models\Asset;
use App\Models\Room;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;
use Barryvdh\DomPDF\Facade\Pdf;

class AssetIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterCondition = '';
    
    // Variabel untuk modal edit/detail
    public $isEditModalOpen = false;
    public $assetId;
    public $newStatus, $newCondition, $newRoomId, $newPicId;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // 1. Hitung Statistik Ringkasan Aset
        $stats = [
            'total' => Asset::count(),
            'tersedia' => Asset::where('status', 'tersedia')->count(),
            'dipinjam' => Asset::where('status', 'dipinjam')->count(),
            'diserahkan' => Asset::where('status', 'diserahkan')->count(),
            'hilang' => Asset::where('status', 'hilang')->count(),
        ];

        // relasi activeLoan.user (jika ada relasi peminjaman aktif ke user)
        $assets = Asset::with(['itemInfo', 'room', 'pic', 'activeLoan.user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('serial_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('itemInfo', function ($sub) {
                          $sub->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterCondition, function ($q) {
                $q->where('condition', $this->filterCondition);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.sarpras.asset-index', [
            'stats' => $stats,
            'assets' => $assets,
            'rooms' => Room::orderBy('name')->get(),
            'users' => User::whereIn('role', ['guru', 'staff', 'admin'])->orderBy('name')->get(),
        ])->layout('layouts.app');
    }

    public function openEditModal($id)
    {
        $asset = Asset::findOrFail($id);
        $this->assetId = $asset->id;
        $this->newStatus = $asset->status;
        $this->newCondition = $asset->condition;
        $this->newRoomId = $asset->room_id;
        $this->newPicId = $asset->pic_id;
        
        $this->isEditModalOpen = true;
    }

    public function updateAsset()
    {
        $this->validate([
            'newStatus' => 'required|in:tersedia,dipinjam,hilang,diserahkan',
            'newCondition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'newRoomId' => 'required|exists:rooms,id',
            'newPicId' => 'required|exists:users,id',
        ]);

        $asset = Asset::findOrFail($this->assetId);
        $asset->update([
            'status' => $this->newStatus,
            'condition' => $this->newCondition,
            'room_id' => $this->newRoomId,
            'pic_id' => $this->newPicId,
        ]);

        $this->isEditModalOpen = false;
        Flux::toast('Data aset berhasil diperbarui.');
    }

    // Fungsi Ekspor PDF untuk Monitor Unit Aset
    public function exportPdf()
    {
        $assets = Asset::with(['itemInfo', 'room', 'pic', 'activeLoan.user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('serial_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('itemInfo', function ($sub) {
                          $sub->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterCondition, function ($q) {
                $q->where('condition', $this->filterCondition);
            })
            ->latest()
            ->get();

        $data = [
            'title' => 'Laporan Monitoring Aset',
            'date' => now()->format('d/m/Y'),
            'assets' => $assets,
        ];

        $pdf = Pdf::loadView('pdf.asset-monitor-report', $data)->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan-Monitoring-Aset-' . now()->format('Ymd') . '.pdf');
    }
}