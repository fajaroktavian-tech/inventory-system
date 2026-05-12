<?php

namespace App\Livewire;

use App\Models\ClassModel;
use Livewire\Component;
use Livewire\WithPagination;

class ClassManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $classId, $name;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.class-management', [
            'classes' => ClassModel::where('name', 'like', '%'.$this->search.'%')
                        ->latest()->paginate(10)
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
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|unique:class_models,name,'.$this->classId
        ]);

        ClassModel::updateOrCreate(['id' => $this->classId], ['name' => $this->name]);

        $this->isModalOpen = false;
        session()->flash('message', 'Data Kelas berhasil disimpan.');
    }

    public function delete($id)
    {
        ClassModel::destroy($id);
        session()->flash('message', 'Data Kelas berhasil dihapus.');
    }
}