<?php

namespace App\Livewire;

use App\Models\RequestModel;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\MyRequestExport; // Import class export
use Maatwebsite\Excel\Facades\Excel;

class RequestHistory extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $filterDate = '';

    // Reset pagination jika filter berubah
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterDate() { $this->resetPage(); }

    public function render()
    {
        $query = RequestModel::with(['details.item', 'room', 'class'])
            ->where('user_id', auth()->id());

        // Filter Status
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Filter Tanggal
        if ($this->filterDate) {
            $query->whereDate('request_date', $this->filterDate);
        }

        return view('livewire.request-history', [
            'requests' => $query->latest()->paginate(10),
        ]);
    }

    public function exportExcel()
    {
        $filename = 'Riwayat_Permintaan_' . auth()->user()->name . '_' . now()->format('Ymd') . '.xlsx';
        
        return Excel::download(
            new MyRequestExport($this->filterStatus, $this->filterDate), 
            $filename
        );
    }

    
}