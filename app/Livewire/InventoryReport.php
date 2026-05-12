<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\RequestDetail;
use App\Models\RequestModel;
use App\Models\ClassModel;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;

class InventoryReport extends Component
{
    use WithPagination;

    // Filter untuk FR-22
    public $startDate,
    $endDate,
    $selectedClass,
    $selectedItem,
    $searchStockItem,
    $selectedStockCategory;
    public $searchRekap = '';
    public $searchOutbound = '';
    // Tab Aktif (Stok / Barang Keluar / Statistik)
    public $activeTab = 'stok';

    public function mount()
    {
        // Proteksi: Hanya role 'admin' dan 'owner' yang bisa masuk
        if (!in_array(auth()->user()->role, ['admin', 'owner', 'petugas'])) {
            abort(403, 'Hanya Admin atau Owner yang dapat melihat laporan.');
        }

        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }
    public function render()
    {
        // 1. Definisikan query dengan filter
        $inventoryStock = Item::with('category')
            ->when($this->searchStockItem, function ($q) {
                $q->where('name', 'like', '%' . $this->searchStockItem . '%');
            })
            ->when($this->selectedStockCategory, function ($q) {
                $q->where('category_id', $this->selectedStockCategory);
            })
            ->orderBy('stock', 'asc')
            ->get();

        // 2. Ambil data kategori untuk dropdown filter
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('livewire.inventory-report', [
            // Kirim variabel yang sudah difilter ($inventoryStock)
            'inventoryStock' => $inventoryStock,
            'categories' => $categories,

            'outboundLogs' => $this->getOutboundLogs(),
            'topItems' => $this->getTopItems(),
            'topClasses' => $this->getTopClasses(),
            'topUsers' => $this->getTopUsers(),
            'summaryReport' => $this->getSummaryReport(),
            'classes' => ClassModel::orderBy('name')->get(),
            'items' => Item::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
    private function getOutboundLogs()
    {
        return RequestDetail::with(['request.student', 'request.class', 'item'])
            ->whereHas('request', function ($q) {
                $q->where('status', 'approved')
                    ->whereBetween('request_date', [$this->startDate, $this->endDate]);
            })
            ->when($this->searchOutbound, function ($q) {
                $q->where(function ($query) {
                    // Cari berdasarkan Nama Barang
                    $query->whereHas('item', function ($iq) {
                        $iq->where('name', 'like', '%' . $this->searchOutbound . '%');
                    })
                        // ATAU Cari berdasarkan Nama Penerima (Siswa)
                        ->orWhereHas('request.student', function ($sq) {
                        $sq->where('name', 'like', '%' . $this->searchOutbound . '%');
                    })
                        // ATAU Cari berdasarkan Nama Kelas
                        ->orWhereHas('request.class', function ($cq) {
                        $cq->where('name', 'like', '%' . $this->searchOutbound . '%');
                    });
                });
            })
            ->latest()
            ->paginate(10);
    }

    private function getTopItems()
    {
        return RequestDetail::query()
            ->select('item_id', DB::raw('SUM(quantity_approved) as total'))
            ->whereHas('request', fn($q) => $q->where('status', 'approved'))
            ->groupBy('item_id')
            ->with('item')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }
    private function getTopClasses()
    {
        return RequestModel::query()
            ->join('class_models', 'requests.class_id', '=', 'class_models.id') // Join langsung
            ->select('requests.class_id', 'class_models.name', DB::raw('COUNT(*) as total'))
            ->where('requests.status', 'approved')
            ->whereNotNull('requests.class_id')
            ->groupBy('requests.class_id', 'class_models.name') // Tambahkan name di group by
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }
    public function exportPDF()
    {
        // Mengambil data berdasarkan filter yang sedang aktif
        $data = [
            'title' => 'Laporan Inventaris Barang',
            'date' => now()->format('d/m/Y'),
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'activeTab' => $this->activeTab,

            // Data sesuai tab yang sedang dibuka
            'stok' => Item::with('category')->orderBy('stock', 'asc')->get(),
            'keluar' => RequestDetail::with(['request.student', 'request.class', 'item'])
                ->whereHas('request', function ($q) {
                    $q->where('status', 'approved')
                        ->whereBetween('request_date', [$this->startDate, $this->endDate]);
                    if ($this->selectedClass)
                        $q->where('class_id', $this->selectedClass);
                })
                ->when($this->selectedItem, function ($q) {
                    $q->where('item_id', $this->selectedItem);
                })->get(),
        ];

        $pdf = Pdf::loadView('pdf.inventory-report', $data)->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan-' . $this->activeTab . '-' . now()->format('Ymd') . '.pdf');
    }

    private function getSummaryReport()
    {
        return Item::query()
            ->with('category')
            // Menghitung total qty dari tabel item_incomings
            ->withSum('incomings as total_masuk', 'quantity')
            // Menghitung total qty_approved dari tabel request_details (yang statusnya approved)
            ->withSum([
                'details as total_keluar' => function ($query) {
                    $query->whereHas('request', fn($q) => $q->where('status', 'approved'));
                }
            ], 'quantity_approved')
            ->when($this->searchRekap, function ($q) {
                $q->where('name', 'like', '%' . $this->searchRekap . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate(10, ['*'], 'rekapPage');
    }

    public function updatingSearchRekap()
    {
        $this->resetPage(); // Reset ke halaman 1 setiap kali mengetik pencarian
    }
    private function getTopUsers()
    {
        return RequestModel::query()
            ->join('users', 'requests.user_id', '=', 'users.id')
            ->select('requests.user_id', 'users.name', DB::raw('COUNT(*) as total'))
            ->where('requests.status', 'approved')
            ->groupBy('requests.user_id', 'users.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }
    public function exportExcel()
    {
        return Excel::download(
            new InventoryExport($this->activeTab, $this->startDate, $this->endDate), 
            'Laporan-' . ucfirst($this->activeTab) . '-' . now()->format('Ymd') . '.xlsx'
        );
    }
}