<?php

namespace App\Livewire\Sarpras;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Item;
use App\Models\AssetLoan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAsset extends Component
{
    public $tab = 'aset';

    public function render()
    {
        // Mendapatkan penanda waktu sinkronisasi data terakhir
        $lastAsset = Asset::latest('updated_at')->first();
        $lastUpdated = $lastAsset ? $lastAsset->updated_at->translatedFormat('d M Y H:i') : '-';

        // Mengambil data peminjaman yang terlambat/jatuh tempo
        // Sesuaikan nama kolom status ('dipinjam' / 'active') dan tanggal tenggat ('due_date')
        $overdueLoans = AssetLoan::with(['asset.itemInfo', 'user'])
            ->whereIn('status', ['active', 'late'])
            ->where('due_date', '<', Carbon::today())
            ->orderBy('due_date', 'asc')
            ->get();

        $roomStats = Asset::join('rooms', 'assets.room_id', '=', 'rooms.id')
            ->select('rooms.name', DB::raw('count(*) as total'))
            ->groupBy('rooms.id', 'rooms.name')
            ->orderBy('total', 'desc')
            ->take(10) // Ambil 10 ruangan terbanyak
            ->get();

        // 1. Ambil Registrasi Aset Terbaru
        $recentRegistrations = Asset::with('itemInfo')->latest()->take(5)->get()->map(function ($item) {
            return [
                'type' => 'registration',
                'title' => 'Aset Baru Terdaftar',
                'description' => ($item->itemInfo->name ?? 'Unit Aset') . ' baru saja diregistrasikan ke dalam sistem dengan S/N: ' . ($item->serial_number ?? '-'),
                'date' => $item->created_at,
                'icon' => 'plus-circle',
                'color' => 'blue'
            ];
        });

        // 2. Ambil Sirkulasi Terbaru (Pinjam/Kembali)
        $recentLoans = AssetLoan::with(['asset.itemInfo', 'user'])->latest()->take(5)->get()->map(function ($item) {
            $name = $item->user->name ?? 'User';
            $isReturned = $item->status === 'returned';
            $statusText = $isReturned ? 'telah mengembalikan' : 'meminjam';
            $barangName = $item->asset->itemInfo->name ?? 'Unit Aset';
            return [
                'type' => 'loan',
                'title' => $isReturned ? 'Pengembalian Aset' : 'Peminjaman Aset',
                'description' => $name . ' ' . $statusText . ' ' . $barangName,
                'date' => $item->updated_at,
                'icon' => $isReturned ? 'arrow-path' : 'arrows-right-left',
                'color' => $isReturned ? 'green' : 'purple'
            ];
        });

        // Gabungkan dan Urutkan
        $activities = collect()
            ->merge($recentRegistrations)
            ->merge($recentLoans)
            ->sortByDesc(function ($activity) {
                return $activity['date'];
            })
            ->take(5);

        return view('livewire.dashboard-asset', [
            'totalValue' => Asset::sum('price'),
            'totalUnit' => Asset::count(),
            'totalAvailable' => Asset::where('status', 'tersedia')->count(),
            'totalLoaned' => Asset::where('status', 'dipinjam')->count(),
            'totalBroken' => Asset::where('condition', 'rusak_berat')->count(),
            'lastUpdated' => $lastUpdated,

            'conditionStats' => [
                'baik' => Asset::where('condition', 'baik')->count(),
                'rusak_ringan' => Asset::where('condition', 'rusak_ringan')->count(),
                'rusak_berat' => Asset::where('condition', 'rusak_berat')->count(),
            ],
            // Kirim data jatuh tempo ke view
            'overdueLoans' => $overdueLoans,
            'activities' => $activities,
            'roomLabels' => $roomStats->pluck('name'),
            'roomData' => $roomStats->pluck('total'),
        ])->layout('layouts.app');
    }
}
