<?php

namespace App\Livewire\Perpustakaan;

use App\Models\LibraryVisit;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LibraryDashboardIndex extends Component
{
    use WithPagination;

    // Properti untuk Pencarian di Preview Laporan Hari Ini
    public $search = '';

    // Properti untuk Data Grafik
    public $visitChartData;
    public $purposeChartData;

    public function mount()
    {
        // Ambil data untuk grafik saat halaman dimuat
        $this->loadChartData();
    }

    /**
     * Logic untuk mengambil data grafik (Pengunjung)
     */
    public function loadChartData()
    {
        // 1. Grafik Tren Kunjungan Mingguan (7 hari terakhir)
        $visitsByDate = LibraryVisit::whereBetween('visit_date', [
            Carbon::now()->startOfMonth()->toDateString(), 
            Carbon::today()->toDateString() 
        ])
        ->select('visit_date', DB::raw('count(*) as total'))
        ->groupBy('visit_date')
        ->orderBy('visit_date')
        ->get();

        // Siapkan data untuk ApexCharts (format JSON)
        $this->visitChartData = json_encode([
            'labels' => $visitsByDate->pluck('visit_date')->map(function ($date) {
                return Carbon::parse($date)->format('j M'); 
            }),
            'series' => $visitsByDate->pluck('total'),
        ]);

        // 2. Grafik Tujuan Kunjungan Bulan Ini
        $purposes = LibraryVisit::whereBetween('visit_date', [
                Carbon::now()->startOfMonth()->toDateString(), 
                Carbon::today()->toDateString()
            ])
            ->select('purpose', DB::raw('count(*) as total'))
            ->groupBy('purpose')
            ->get();

        // Siapkan data untuk ApexCharts (Pie Chart)
        $this->purposeChartData = json_encode([
            'labels' => $purposes->pluck('purpose')->map(function ($p) {
                return ucfirst($p);
            }),
            'series' => $purposes->pluck('total'),
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Reset pagination untuk preview laporan
    }

    public function render()
    {        
        // Statistik Cepat (REAL)
        $totalToday = LibraryVisit::where('visit_date', Carbon::today())->count();
        
        $totalThisWeek = LibraryVisit::whereBetween('visit_date', [
            Carbon::now()->startOfWeek()->toDateString(), // Awal minggu (Senin)
            Carbon::now()->endOfWeek()->toDateString() // Akhir minggu (Minggu)
        ])
        ->count();
        // Ambil total 'belajar' vs 'membaca' Bulan Ini
        $totalPurposeBelajarMonth = LibraryVisit::whereBetween('visit_date', [Carbon::now()->startOfMonth()->toDateString(), Carbon::today()->toDateString()])
            ->where('purpose', 'belajar')
            ->count();
        $totalPurposeMembacaMonth = LibraryVisit::whereBetween('visit_date', [Carbon::now()->startOfMonth()->toDateString(), Carbon::today()->toDateString()])
            ->where('purpose', 'membaca')
            ->count();
        
        // Statistik Pengunjung Tertinggi Bulan Ini (REAL)
        $totalThisWeek = LibraryVisit::whereBetween('visit_date', [
            Carbon::now()->startOfWeek()->toDateString(), // Awal minggu (Senin)
            Carbon::now()->endOfWeek()->toDateString() // Akhir minggu (Minggu)
        ])
        ->count();

        $queryTodayReport = LibraryVisit::with(['user.class', 'user'])
            ->where('visit_date', Carbon::today());

        if (!empty($this->search)) {
            $queryTodayReport->whereHas('user', function ($q) {
                $q->where('name', 'like', '\%' .$this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%');
            });
        }

        // Ambil data untuk preview tabel di Dashboard (pagination kecil saja)
        $todayVisitsPagination =$queryTodayReport->latest('visit_time')->paginate(5);


        // --- DATA DUMMY (SESUAI PERMINTAAN) ---
        
        $statisticsDummy = [
            'total_books' => '1.2k', // Dummy
            'total_borrowed' => '450', // Dummy
        ];

        // 5 Buku Terpopuler (List Dummy)
        $topBooksDummy = [
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'borrow_count' => 120],
            ['title' => 'Bumi', 'author' => 'Tere Liye', 'borrow_count' => 88],
            ['title' => 'Filosofi Teras', 'author' => 'Henry Manampiring', 'borrow_count' => 75],
            ['title' => 'Harry Potter', 'author' => 'J.K. Rowling', 'borrow_count' => 60],
            ['title' => 'Negeri 5 Menara', 'author' => 'A. Fuadi', 'borrow_count' => 55],
        ];


        // Menggunakan layout utama aplikasi
        return view('livewire.perpustakaan.library-dashboard-index', [
            // Data REAL
            'totalToday' => $totalToday,
            'totalThisWeek' => $totalThisWeek, // Variabel Baru
            'totalPurposeBelajarMonth' => $totalPurposeBelajarMonth,
            'totalPurposeMembacaMonth' => $totalPurposeMembacaMonth,
            'todayVisitsPagination' => $todayVisitsPagination,

            // Data DUMMY
            'statisticsDummy' => $statisticsDummy,
            'topBooksDummy' => $topBooksDummy,
        ])->layout('layouts.app');
    }
}