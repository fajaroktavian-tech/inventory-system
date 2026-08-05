<?php

namespace App\Livewire\Sarpras;

use App\Models\User;
use App\Models\Asset;
use App\Models\AssetLoan;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class AssetRfidLoan extends Component
{
    public $step = 1;
    public $rfid_uid = '';
    public $userId; 
    public $search_asset = '';
    public $selectedAsset = null;
    public $due_date, $notes;

    public function mount()
    {
        $this->step = 1;
        $this->due_date = date('Y-m-d');
    }

    #[Computed]
    public function userData()
    {
        if (!$this->userId) return null;
        return User::with('class')->find($this->userId);
    }

    public function updatedRfidUid()
    {
        $uid = trim($this->rfid_uid);
        if (empty($uid)) return;

        $user = User::where('rfid_uid', $uid)->where('is_active', true)->first();

        if ($user) {
            $this->userId = $user->id;
            $this->rfid_uid = ''; // Reset input agar siap scan berikutnya
            $this->step = 2;
        } else {
            session()->flash('error', 'Kartu RFID tidak terdaftar!');
            $this->rfid_uid = '';
        }
    }

    public function selectAsset($id)
    {
        $asset = Asset::with('itemInfo')->find($id);
        $this->selectedAsset = [
            'id' => $asset->id,
            'name' => $asset->itemInfo->name,
            'sn' => $asset->serial_number
        ];
        $this->search_asset = '';
        $this->step = 3;
    }

    public function submitLoan()
    {
        if (!$this->selectedAsset) return;

        try {
            DB::transaction(function () {
                AssetLoan::create([
                    'asset_id' => $this->selectedAsset['id'],
                    'user_id' => $this->userId,
                    'loan_date' => now(),
                    'due_date' => $this->due_date,
                    'notes' => $this->notes,
                    'status' => 'active',
                ]);

                Asset::find($this->selectedAsset['id'])->update(['status' => 'dipinjam']);
            });

            session()->flash('success', 'Peminjaman berhasil! Silakan ambil aset Anda.');
            $this->reset(['userId', 'selectedAsset', 'notes', 'search_asset', 'rfid_uid']);
            $this->step = 1;

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function render()
    {
        $availableAssets = [];
        if (strlen($this->search_asset) > 1) {
            $availableAssets = Asset::with('itemInfo')
                ->where('status', 'tersedia')
                ->where(function($q) {
                    $q->whereHas('itemInfo', function($query) {
                        $query->where('name', 'like', '%' . $this->search_asset . '%');
                    })
                    ->orWhere('serial_number', 'like', '%' . $this->search_asset . '%');
                })->limit(5)->get();
        }

        return view('livewire.asset-rfid-loan', [
            'availableAssets' => $availableAssets
        ])->layout('layouts.guest');
    }
}