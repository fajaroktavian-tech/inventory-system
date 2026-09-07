<?php

namespace App\Livewire\Sarpras;

use App\Models\Asset;
use App\Models\Room;
use App\Models\AssetHistory;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AssetActionModal extends Component
{
    public $assetId;
    public $asset;
    
    // Properti Modal Pindah Ruangan
    public $isTransferModalOpen = false;
    public $new_room_id;
    public $transfer_notes;

    // Properti Modal Perubahan Kondisi
    public $isConditionModalOpen = false;
    public $new_condition;
    public $condition_notes;
    public $isPicModalOpen = false;
    public $new_pic_id;
    public $pic_notes;

    protected $listeners = ['openTransferModal' => 'openTransfer', 'openConditionModal' => 'openCondition', 'openPicModal' => 'openPic'];

    public function openTransfer($id)
    {
        $this->assetId = $id;
        $this->asset = Asset::with('room')->find($id);
        $this->new_room_id = $this->asset->room_id;
        $this->transfer_notes = '';
        $this->isTransferModalOpen = true;
    }

    public function openCondition($id)
    {
        $this->assetId = $id;
        $this->asset = Asset::find($id);
        $this->new_condition = $this->asset->condition;
        $this->condition_notes = '';
        $this->isConditionModalOpen = true;
    }

    public function saveTransfer()
    {
        $this->validate([
            'new_room_id' => 'required|exists:rooms,id',
            'transfer_notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () {
            $asset = Asset::findOrFail($this->assetId);
            $oldRoomName = $asset->room->name ?? '-';
            
            $newRoom = Room::findOrFail($this->new_room_id);

            // Update ruangan di tabel master aset
            $asset->update(['room_id' => $this->new_room_id]);

            // Catat log histori perpindahan ruangan
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'activity_type' => 'room_transfer',
                'title' => 'Mutasi / Pindah Ruangan',
                'description' => $this->transfer_notes ? 'Keterangan: ' . $this->transfer_notes : 'Mutasi unit aset antar ruangan.',
                'old_value' => $oldRoomName,
                'new_value' => $newRoom->name,
            ]);
        });

        $this->isTransferModalOpen = false;
        session()->flash('message', 'Aset berhasil dipindahkan ke ruangan baru.');
        $this->dispatch('refresh-timeline');
    }

    public function saveCondition()
    {
        $this->validate([
            'new_condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'condition_notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () {
            $asset = Asset::findOrFail($this->assetId);
            $oldCondition = $asset->condition;

            // Update kondisi di tabel master aset
            $asset->update(['condition' => $this->new_condition]);

            // Catat log histori perubahan kondisi fisik
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'activity_type' => 'condition_update',
                'title' => 'Perubahan Kondisi Fisik',
                'description' => $this->condition_notes ? 'Catatan: ' . $this->condition_notes : 'Pembaruan status kondisi fisik aset.',
                'old_value' => ucwords(str_replace('_', ' ', $oldCondition)),
                'new_value' => ucwords(str_replace('_', ' ', $this->new_condition)),
            ]);
        });

        $this->isConditionModalOpen = false;
        session()->flash('message', 'Kondisi fisik aset berhasil diperbarui.');
        $this->dispatch('refresh-timeline');
    }

    public function render()
    {
        return view('livewire.sarpras.asset-action-modal', [
            'rooms' => Room::all(),
        ]);
    }

    public function openPic($id)
    {
        $this->assetId = $id;
        $this->asset = Asset::with('pic')->find($id);
        $this->new_pic_id = $this->asset->pic_id;
        $this->pic_notes = '';
        $this->isPicModalOpen = true;
    }

    public function savePic()
    {
        $this->validate([
            'new_pic_id' => 'required|exists:users,id',
            'pic_notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () {
            $asset = Asset::findOrFail($this->assetId);
            $oldPicName = $asset->pic->name ?? '-';
            
            $newPic = User::findOrFail($this->new_pic_id);

            // Update PIC di tabel master aset
            $asset->update(['pic_id' => $this->new_pic_id]);

            // Catat log histori perubahan PIC
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'activity_type' => 'pic_change',
                'title' => 'Pergantian Penanggung Jawab (PIC)',
                'description' => $this->pic_notes ? 'Catatan: ' . $this->pic_notes : 'Pembaruan penanggung jawab aset.',
                'old_value' => $oldPicName,
                'new_value' => $newPic->name,
            ]);
        });

        $this->isPicModalOpen = false;
        session()->flash('message', 'Penanggung jawab aset berhasil diperbarui.');
        $this->dispatch('refresh-timeline');
    }
}