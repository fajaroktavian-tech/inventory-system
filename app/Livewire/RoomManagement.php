<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use Livewire\WithPagination;

class RoomManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $roomId, $name;
    public $selectedRoom;
    public $totalValue = 0;
    public $isModalOpen = false;
    public $assetsData = [];

    public function render()
    {
        return view('livewire.room-management', [
            'rooms' => Room::where('name', 'like', '%' . $this->search . '%')
                ->latest()->paginate(10)
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['name', 'roomId']);
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        $this->roomId = $id;
        $this->name = $room->name;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|unique:rooms,name,' . $this->roomId
        ]);

        Room::updateOrCreate(['id' => $this->roomId], ['name' => $this->name]);

        $this->isModalOpen = false;
        session()->flash('message', 'Data Ruangan berhasil disimpan.');
    }

    public function delete($id)
    {
        Room::destroy($id);
        session()->flash('message', 'Data Ruangan berhasil dihapus.');
    }
    public function showAssets($roomId)
    {
        $this->selectedRoom = \App\Models\Room::with(['assets.itemInfo', 'assets.pic'])->find($roomId);

        if ($this->selectedRoom) {
            $this->totalValue = $this->selectedRoom->assets->sum('price');

            // Siapkan data untuk cetak di sini (sisi server)
            $this->assetsData = $this->selectedRoom->assets->map(fn($asset) => [
                'name' => $asset->itemInfo->name,
                'sn' => $asset->serial_number ?? '-',
                'condition' => $asset->condition,
                'source' => $asset->source_fund
            ])->toArray();
        }

        $this->js("\$flux.modal('room-assets-modal').show()");
    }

    public function printDir()
    {
        if (!$this->selectedRoom)
            return;

        $this->dispatch('trigger-print-dir', [
            'roomName' => $this->selectedRoom->name,
            'picName' => $this->selectedRoom->assets->first()->pic->name ?? '-',
            'totalValue' => 'Rp ' . number_format($this->totalValue, 0, ',', '.'),
            'assets' => $this->assetsData
        ]);
    }
}