<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryId, $name;
    public $isModalOpen = false;

    public function mount() {
        if (!in_array(auth()->user()->role, ['admin', 'petugas'])) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.category-management', [
            'categories' => Category::where('name', 'like', '%' . $this->search . '%')
                ->latest()->paginate(10)
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['name', 'categoryId']);
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate(['name' => 'required|unique:categories,name,' . $this->categoryId]);

        Category::updateOrCreate(['id' => $this->categoryId], ['name' => $this->name]);

        $this->isModalOpen = false;
        session()->flash('message', 'Kategori berhasil disimpan.');
    }

    public function delete($id)
    {
        $category = \App\Models\Category::findOrFail($id);

        // Cek apakah ada barang yang pakai kategori ini
        if ($category->items()->count() > 0) {
            session()->flash('error', 'Kategori tidak bisa dihapus karena masih digunakan oleh beberapa barang.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Kategori berhasil dihapus.');
    }
}