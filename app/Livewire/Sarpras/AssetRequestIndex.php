<?php

namespace App\Livewire\Sarpras;

use App\Models\Asset;
use App\Models\AssetProcurement;
use App\Models\AssetMaintenance;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AssetRequestIndex extends Component
{
    use WithPagination;
    public $assetSearch = '';

    public $activeTab = 'procurement'; // Pilihan tab: 'procurement' (Aset/BHP) atau 'maintenance' (Perbaikan)
    public $search = '';
    public $filterStatus = '';

    // Properti Form Pengadaan (Procurement)
    public $procurementId, $type = 'aset', $item_name, $qty = 1, $estimated_price, $reason;

    // Properti Form Perbaikan (Maintenance)
    public $maintenanceId, $asset_id, $damage_description;

    public $isModalOpen = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // Query Pengadaan (Aset / BHP)
        $procurementsQuery = AssetProcurement::with('user')
            ->when(!$user->isAdmin() && !$user->isPetugas(), function ($q) use ($user) {
                $q->where('user_id', $user->id); // Guru biasa hanya melihat pengajuannya sendiri
            })
            ->where(function ($q) {
                $q->where('item_name', 'like', '%' . $this->search . '%')
                    ->orWhere('reason', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->latest();

        // Query Perbaikan (Maintenance)
        $maintenancesQuery = AssetMaintenance::with(['user', 'asset.itemInfo', 'asset.room'])
            ->when(!$user->isAdmin() && !$user->isPetugas(), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where(function ($q) {
                $q->where('damage_description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('asset.itemInfo', function ($sub) {
                        $sub->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->latest();

        $assetsQuery = Asset::with('itemInfo', 'room')
            ->whereIn('condition', ['rusak_ringan', 'rusak_berat'])
            ->when($this->assetSearch, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('serial_number', 'like', '%' . $this->assetSearch . '%')
                        ->orWhereHas('itemInfo', function ($itemSub) {
                            $itemSub->where('name', 'like', '%' . $this->assetSearch . '%');
                        })
                        ->orWhereHas('room', function ($roomSub) {
                            $roomSub->where('name', 'like', '%' . $this->assetSearch . '%');
                        });
                });
            });

        return view('livewire.sarpras.asset-request-index', [
            'procurements' => $this->activeTab === 'procurement' ? $procurementsQuery->paginate(10) : [],
            'maintenances' => $this->activeTab === 'maintenance' ? $maintenancesQuery->paginate(10) : [],
            'assetsList' => $assetsQuery->limit(20)->get(),
        ])->layout('layouts.app');
    }

    public function openModal($tab = null)
    {
        $this->reset(['procurementId', 'maintenanceId', 'item_name', 'qty', 'estimated_price', 'reason', 'asset_id', 'damage_description', 'assetSearch']);
        $this->qty = 1;
        $this->type = 'aset';
        if ($tab) {
            $this->activeTab = $tab;
        }
        $this->isModalOpen = true;
    }

    public function saveProcurement()
    {
        $this->validate([
            'type' => 'required|in:aset,bhp',
            'item_name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'estimated_price' => 'nullable|numeric',
            'reason' => 'required|string',
        ]);

        AssetProcurement::create([
            'user_id' => Auth::id(),
            'type' => $this->type,
            'item_name' => $this->item_name,
            'qty' => $this->qty,
            'estimated_price' => $this->estimated_price ?? 0,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        $this->isModalOpen = false;
        session()->flash('message', 'Pengajuan pengadaan berhasil dikirim.');
    }

    public function saveMaintenance()
    {
        $this->validate([
            'asset_id' => 'required|exists:assets,id',
            'damage_description' => 'required|string',
        ]);

        AssetMaintenance::create([
            'user_id' => Auth::id(),
            'asset_id' => $this->asset_id,
            'damage_description' => $this->damage_description,
            'status' => 'pending',
        ]);

        $this->isModalOpen = false;
        session()->flash('message', 'Laporan perbaikan/pemeliharaan berhasil dikirim.');
    }

    // Aksi untuk Admin / Petugas mengubah status
    public function updateStatus($id, $status, $type = 'procurement')
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isPetugas()) {
            return;
        }

        if ($type === 'procurement') {
            $item = AssetProcurement::findOrFail($id);
            $item->update(['status' => $status]);
        } else {
            $item = AssetMaintenance::findOrFail($id);
            $item->update(['status' => $status]);
        }

        session()->flash('message', 'Status pengajuan berhasil diperbarui.');
    }
}