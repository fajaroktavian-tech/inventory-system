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
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.room-management', [
            'rooms' => Room::where('name', 'like', '%'.$this->search.'%')
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
            'name' => 'required|unique:rooms,name,'.$this->roomId
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
}