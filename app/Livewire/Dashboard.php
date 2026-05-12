<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\ItemIncoming;
use App\Models\RequestModel;
use Livewire\Component;

class Dashboard extends Component
{
    public $lastUpdated;
    public function render()
    {
        $this->lastUpdated = now()->timezone('Asia/Jakarta')->format('H:i:s');
        $today = now()->format('Y-m-d'); // Ambil tanggal hari ini

        // 1. Total Jenis Barang
        $totalItems = Item::count();

        // 2. Stok Menipis
        $lowStockItems = Item::whereRaw('stock <= min_stock')->get();
        $lowStockCount = $lowStockItems->count();

        // 3. Aktivitas HARI INI (Filter tanggal hari ini)
        // Ambil semua aktivitas hari ini untuk dihitung jumlahnya
        $todayIncomingCount = ItemIncoming::whereDate('created_at', $today)->count();
        $todayOutgoingCount = RequestModel::whereDate('created_at', $today)
            ->where('status', 'approved')
            ->count();

        // 4. Data untuk List (Tetap ambil 5 terbaru untuk ditampilkan di tabel/list)
        $recentIncoming = ItemIncoming::with(['item', 'user'])->latest()->limit(5)->get();
        $recentOutgoing = RequestModel::with(['student', 'class', 'details.item'])
            ->where('status', 'approved')
            ->latest()
            ->limit(5)
            ->get();

        $layout = request()->routeIs('public.display') ? 'layouts.display' : 'layouts.app';

        return view('livewire.dashboard', [
            'totalItems' => $totalItems,
            'lowStockCount' => $lowStockCount,
            'lowStockItems' => $lowStockItems,
            'recentIncoming' => $recentIncoming,
            'recentOutgoing' => $recentOutgoing,
            'todayActivityCount' => $todayIncomingCount + $todayOutgoingCount,
        ])->layout($layout);
    }
}
