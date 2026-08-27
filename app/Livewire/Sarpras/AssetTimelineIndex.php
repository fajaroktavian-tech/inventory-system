<?php

namespace App\Livewire\Sarpras;

use App\Models\Asset;
use Livewire\Component;
use Livewire\WithPagination;

class AssetTimelineIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $assets = Asset::with(['itemInfo', 'room', 'pic'])
            ->when($this->search, function ($query) {
                $query->where('serial_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('itemInfo', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('room', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            })
            ->latest()
            ->paginate(10);

        return view('livewire.sarpras.asset-timeline-index', [
            'assets' => $assets,
        ])->layout('layouts.app');
    }
}
