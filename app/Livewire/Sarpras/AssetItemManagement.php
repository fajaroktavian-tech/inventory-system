<?php

namespace App\Livewire\Sarpras;

use App\Models\AssetItem;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image; 
use Spatie\Image\Enums\Fit;

class AssetItemManagement extends Component
{
    use WithPagination, WithFileUploads; 

    public $search = '';
    public $itemId, $name, $category_id, $brand, $specification;
    public $search_category = '';
    public $image, $new_image;
    public $selectedCategoryName = null;
    public $isCatalogGuideOpen = false;
    public $isModalOpen = false;

    protected $rules = [
        'name' => 'required|min:3',
        'category_id' => 'required|exists:categories,id',
        'brand' => 'nullable',
        'specification' => 'nullable',
        'new_image' => 'nullable|image|max:5120',
    ];

    public function render()
    {
        return view('livewire.asset-item-management', [
            'items' => AssetItem::with('category')
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('brand', 'like', '%' . $this->search . '%')
                ->latest()->paginate(10),

            'filteredCategories' => strlen($this->search_category) > 1
                ? Category::where('name', 'like', '%' . $this->search_category . '%')->get()
                : []
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['itemId', 'name', 'category_id', 'brand', 'specification', 'image', 'new_image', 'search_category', 'selectedCategoryName']);
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate($this->rules());

        // 1. Siapkan data dasar yang akan disimpan
        $data = [
            'name' => $this->name,
            'category_id' => $this->category_id,
            'brand' => $this->brand,
            'specification' => $this->specification,
        ];

        // 2. Handle Upload Foto Baru (jika user mengunggah foto baru)
        if ($this->new_image) {
            // Hapus foto lama di storage jika ada foto sebelumnya
            if ($this->image) {
                Storage::disk('public')->delete($this->image);
            }
            // Simpan foto baru ke folder 'asset-items' pada disk 'public'
            $filename = time() . '_' . $this->new_image->getClientOriginalName();
            $folder = 'asset-items';
            
            // b. Tentukan path lengkap penyimpanan fisik di dalam container (menunjuk ke Drive D)
            $targetPath = storage_path('app/public/' . $folder . '/' . $filename);

            // c. Pastikan direktori tujuan ada, jika tidak, buat
            if (!file_exists(storage_path('app/public/' . $folder))) {
                mkdir(storage_path('app/public/' . $folder), 0777, true);
            }

            // d. Lakukan Kompresi & Resize dengan Spatie Image, Kita resize maks Full HD dan kualitas 70%
            Image::load($this->new_image->getRealPath())
                ->fit(Fit::Max, 1920, 1080) 
                ->quality(70) 
                ->save($targetPath); 

            // e. Set path relatif untuk disimpan di Database
            $data['image'] = $folder . '/' . $filename;
        }

        // 3. Simpan atau perbarui data ke database
        AssetItem::updateOrCreate(['id' => $this->itemId], $data);

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
        $this->selectedCategoryName = $item->category->name ?? null;
        $this->specification = $item->specification;
        $this->image = $item->image; 
        $this->new_image = null;
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        $item = AssetItem::find($id);
        if ($item) {
            // Hapus file fisik dari storage jika ada
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $item->delete();
        }
        session()->flash('message', 'Master Aset dihapus.');
    }

    public function selectCategory($id, $name)
    {
        $this->category_id = $id;
        $this->selectedCategoryName = $name;
        $this->search_category = '';
    }
}