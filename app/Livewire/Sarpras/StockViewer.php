<?php

namespace App\Livewire\Sarpras;

use App\Models\Item;
use Livewire\Component;

class StockViewer extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.stock-viewer', [
            'items' => Item::with('category')
                ->where('name', 'like', '%' . $this->search . '%')
                ->orderBy('name', 'asc')
                ->get()
        ]);
    }
}