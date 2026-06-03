<?php

namespace App\Livewire;

use App\Models\Prodi;
use Livewire\Component;
use Livewire\WithPagination;

class ProdiManagement extends Component
{
    use WithPagination;

    public $name, $alias, $prodiId;
    public $search = '';
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.prodi-management', [
            'prodis' => Prodi::where('name', 'like', '%'.$this->search.'%')
                ->orWhere('alias', 'like', '%'.$this->search.'%')
                ->latest()
                ->paginate(10)
        ]);
    }

    public function create()
    {
        $this->reset(['name', 'alias', 'prodiId']);
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3',
            'alias' => 'required|max:10',
        ]);

        Prodi::updateOrCreate(['id' => $this->prodiId], [
            'name' => $this->name,
            'alias' => $this->alias,
        ]);

        session()->flash('message', $this->prodiId ? 'Prodi berhasil diupdate.' : 'Prodi berhasil ditambahkan.');
        
        $this->isModalOpen = false;
        $this->reset(['name', 'alias', 'prodiId']);
    }

    public function edit($id)
    {
        $prodi = Prodi::findOrFail($id);
        $this->prodiId = $id;
        $this->name = $prodi->name;
        $this->alias = $prodi->alias;
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        Prodi::find($id)->delete();
        session()->flash('message', 'Prodi berhasil dihapus.');
    }
}