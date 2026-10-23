<?php

namespace App\Livewire\Sarpras;

use App\Models\Item;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\ItemsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;



class ItemManagement extends Component
{
    use WithPagination, WithFileUploads;
    public $isItemsGuideOpen = false;

    public $search = '';
    public $photo, $new_photo;
    public $itemId, $name, $category_id, $unit, $stock, $min_stock;
    public $isModalOpen = false;

    public function mount()
    {
        // Izinkan Admin dan Petugas
        if (!in_array(auth()->user()->role, ['admin', 'petugas'])) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.item-management', [
            // Eager loading kategori agar tidak lambat (N+1 query)
            'items' => Item::with('category')
                ->where('name', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
            // Kirim daftar kategori untuk dropdown
            'categories' => Category::orderBy('name', 'asc')->get()
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['name', 'category_id', 'unit', 'stock', 'min_stock', 'itemId', 'photo', 'new_photo']);
        $this->stock = 0;
        $this->min_stock = 0;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $this->itemId = $id;
        $this->name = $item->name;
        $this->category_id = $item->category_id;
        $this->unit = $item->unit;
        $this->stock = $item->stock;
        $this->min_stock = $item->min_stock;
        $this->photo = $item->photo; // Simpan path foto lama
        $this->new_photo = null;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id', // Pastikan ID ada di tabel categories
            'unit' => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'new_photo' => 'nullable|image|max:2048',
        ]);

        Item::updateOrCreate(['id' => $this->itemId], [
            'name' => $this->name,
            'category_id' => $this->category_id,
            'unit' => $this->unit,
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
        ]);

        // Handle upload foto baru
        if ($this->new_photo) {
            if ($this->photo) {
                Storage::disk('public')->delete($this->photo); // Hapus foto lama
            }
            $data['photo'] = $this->new_photo->store('items', 'public');
        }
        Item::updateOrCreate(['id' => $this->itemId], $data);

        $this->isModalOpen = false;
        session()->flash('message', 'Data Barang Berhasil Disimpan.');
    }
    public function delete($id)
    {
        $item = Item::find($id);
        if ($item && $item->photo) {
            Storage::disk('public')->delete($item->photo);
        }
        Item::destroy($id);
        session()->flash('message', 'Barang Berhasil Dihapus.');
    }

    public function exportExcel()
    {
        return Excel::download(new ItemsExport, 'Data-Barang-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportPDF()
    {
        // Ambil data untuk PDF
        $items = Item::with('category')->orderBy('name', 'asc')->get();

        $pdf = Pdf::loadView('pdf.items-report', [
            'items' => $items,
            'date' => now()->format('d/m/Y')
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Data-Barang-' . now()->format('Ymd') . '.pdf');
    }
}