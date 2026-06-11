<?php

namespace App\Livewire\Sarpras;

use App\Models\AssetItem;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class AssetItemManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $itemId, $name, $category_id, $brand, $specification;
    public $search_category = '';
    public $selectedCategoryName = null;
    public $isCatalogGuideOpen = false;
    public $isModalOpen = false;

    protected $rules = [
        'name' => 'required|min:3',
        'category_id' => 'required|exists:categories,id',
        'brand' => 'nullable',
        'specification' => 'nullable',
    ];

    public function render()
    {
        return view('livewire.asset-item-management', [
            'items' => AssetItem::with('category')
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('brand', 'like', '%' . $this->search . '%')
                ->latest()->paginate(10),

            // Tambahkan filteredCategories
            'filteredCategories' => strlen($this->search_category) > 1
                ? Category::where('name', 'like', '%' . $this->search_category . '%')->get()
                : []
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['itemId', 'name', 'category_id', 'brand', 'specification', 'search_category', 'selectedCategoryName']);
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        AssetItem::updateOrCreate(['id' => $this->itemId], [
            'name' => $this->name,
            'category_id' => $this->category_id,
            'brand' => $this->brand,
            'specification' => $this->specification,
        ]);

        $this->isModalOpen = false;
        session()->flash('message', $this->itemId ? 'Master Aset diperbarui.' : 'Master Aset ditambahkan.');
    }

    public function edit($id)
    {
        $item = AssetItem::findOrFail($id);
        $this->itemId = $id;
        $this->name = $item->name;
        $this->category_id = $item->category_id;
        $this->brand = $item->brand;
        $this->selectedCategoryName = $item->category->name;
        $this->specification = $item->specification;
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        AssetItem::destroy($id);
        session()->flash('message', 'Master Aset dihapus.');
    }

    public function selectCategory($id, $name)
    {
        $this->category_id = $id;
        $this->selectedCategoryName = $name;
        $this->search_category = '';
    }
}