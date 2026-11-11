<?php

namespace App\Livewire\Perpustakaan;

use App\Models\LibraryVisit;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class LibraryReportIndex extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    
    public $search = '';

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();$this->endDate = Carbon::today()->toDateString();
    }

    // --- MODIFIKASI: Listener untuk Kedua Tanggal ---
    public function updatedStartDate()
    {
        // Pastikan endDate tidak mendahului startDate
        if ($this->endDate <$this->startDate) {
            $this->endDate =$this->startDate;
        }
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        if ($this->startDate >$this->endDate) {
            $this->startDate =$this->endDate;
        }
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Query Dasar: Ambil kunjungan, eager load User dan Kelas agar cepat
        $query = LibraryVisit::with(['user.class', 'user'])
            // --- MODIFIKASI: Gunakan whereBetween untuk Rentang Tanggal ---
            ->whereBetween('visit_date', [$this->startDate,$this->endDate]);

        // Jika ada input pencarian (Nama, NIS, NIP)
        if (!empty($this->search)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '\%' .$this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%');
            });
        }

       
        $totalToday = LibraryVisit::where('visit_date', Carbon::today())->count();
        $totalFiltered =$query->count(); 
        
        $visits =$query->latest('visit_time')->latest('visit_date')->paginate(15);

        return view('livewire.perpustakaan.library-report-index', [
            'visits' => $visits,
            'totalToday' => $totalToday,
            'totalFiltered' => $totalFiltered,
        ])->layout('layouts.app');
    }
}