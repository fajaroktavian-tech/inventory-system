<?php

namespace App\Livewire;

use App\Models\ClassModel;
use Livewire\Component;
use App\Models\Prodi;
use Livewire\WithPagination;

class ClassManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $classId, $name, $prodi_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.class-management', [
            'classes' => ClassModel::with('prodi') // Eager load prodi untuk performa
                        ->where('name', 'like', '%'.$this->search.'%')
                        ->latest()->paginate(10),
            'prodis' => Prodi::all() // Ambil semua data prodi untuk dropdown
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['name', 'classId']);
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $class = ClassModel::findOrFail($id);
        $this->classId = $id;
        $this->name = $class->name;
        $this->prodi_id = $class->prodi_id;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|unique:class_models,name,'.$this->classId,
            'prodi_id' => 'required|exists:prodis,id'
        ]);

        ClassModel::updateOrCreate(['id' => $this->classId], ['name' => $this->name, 'prodi_id' => $this->prodi_id]);

        $this->isModalOpen = false;
        session()->flash('message', 'Data Kelas berhasil disimpan.');
    }

    public function delete($id)
    {
        ClassModel::destroy($id);
        session()->flash('message', 'Data Kelas berhasil dihapus.');
    }
}