<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\ItemIncoming;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Exports\IncomingItemsExport; // Import Excel
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF

class ItemIncomingManagement extends Component
{
    use WithPagination;

    public $search = '', $startDate, $endDate;
    public $itemId, $date, $quantity, $description;
    public $search_item = '';
    public $selectedItemName = '';
    public $isModalOpen = false;

    public function mount()
    {
        $this->date = date('Y-m-d'); // Default tanggal hari ini
    }

    public function render()
    {
        $query = ItemIncoming::with(['item', 'user'])
            ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
            ->whereHas('item', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->latest();

        // Logika pencarian barang di dalam modal
        $availableItems = [];
        if (strlen($this->search_item) > 1) {
            $availableItems = Item::where('name', 'like', '%' . $this->search_item . '%')
                ->limit(5)
                ->get();
        }

        return view('livewire.item-incoming-management', [
            'incomings' => $query->paginate(10),
            'availableItems' => $availableItems
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['itemId', 'quantity', 'description', 'search_item', 'selectedItemName']);
        $this->date = date('Y-m-d');
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'itemId' => 'required|exists:items,id',
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:1',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () {
            // 1. Simpan Riwayat
            ItemIncoming::create([
                'item_id' => $this->itemId,
                'date' => $this->date,
                'quantity' => $this->quantity,
                'description' => $this->description,
                'created_by' => auth()->id(),
            ]);

            // 2. Update Stok di tabel Item
            $item = Item::find($this->itemId);
            $item->increment('stock', $this->quantity);
        });

        $this->isModalOpen = false;
        session()->flash('message', 'Stok barang berhasil ditambah.');
    }

    // Tambahkan fungsi untuk memilih barang
    public function selectItem($id, $name)
    {
        $this->itemId = $id;
        $this->selectedItemName = $name;
        $this->search_item = ''; // Bersihkan input pencarian setelah dipilih
    }

    public function exportExcel()
    {
        return Excel::download(
            new IncomingItemsExport($this->startDate, $this->endDate, $this->search),
            'Barang-Masuk-' . now()->format('Ymd') . '.xlsx'
        );
    }

    public function exportPDF()
    {
        $data = ItemIncoming::with(['item', 'user'])
            ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
            ->whereHas('item', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->get();

        $pdf = Pdf::loadView('pdf.incoming-report', [
            'incomings' => $data,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan-Barang-Masuk-' . now()->format('Ymd') . '.pdf');
    }

}