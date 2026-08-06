<?php

namespace App\Livewire\Sarpras;

use App\Models\RequestModel;
use App\Models\Item;
use App\Models\RequestDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class RequestApproval extends Component
{
    use WithPagination;
    public $search = '';
    public $filterStatus = 'pending';

    // Untuk fitur edit jumlah saat approval
    public $editingId = null;
    public $editQuantities = [];

    // Reset pagination ketika filter atau pencarian berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = RequestModel::with(['student', 'class', 'room', 'details.item', 'approver'])
            ->when($this->search, function ($q) {
                $q->whereHas('student', function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%');
                });
            });

        // Terapkan filter status jika dipilih
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.request-approval', [
            'requests' => $query->latest()->paginate(10)
        ])->layout('layouts.app');
    }

    public function startEdit($requestId)
    {
        $this->editingId = $requestId;
        $request = RequestModel::with('details')->find($requestId);
        foreach ($request->details as $detail) {
            $this->editQuantities[$detail->id] = $detail->quantity_requested;
        }
    }

    public function approve($requestId)
    {
        try {
            DB::transaction(function () use ($requestId) {
                $request = RequestModel::findOrFail($requestId);

                foreach ($request->details as $detail) {
                    $item = Item::lockForUpdate()->find($detail->item_id);

                    // Ambil jumlah yang akan disetujui (bisa hasil edit atau default)
                    $qtyToApprove = $this->editQuantities[$detail->id] ?? $detail->quantity_requested;

                    if ($item->stock < $qtyToApprove) {
                        throw new \Exception("Stok {$item->name} tidak cukup untuk menyetujui permintaan ini.");
                    }

                    // FR-16: Update Stok Otomatis
                    $item->decrement('stock', $qtyToApprove);

                    // Update detail dengan jumlah yang disetujui
                    $detail->update([
                        'quantity_approved' => $qtyToApprove
                    ]);
                }

                $request->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            });

            session()->flash('message', 'Permintaan berhasil disetujui dan stok telah diperbarui.');
            $this->editingId = null;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reject($requestId)
    {
        RequestModel::findOrFail($requestId)->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(), // Mencatat siapa yang menolak
            'approved_at' => now(),       // Mencatat waktu penolakan
        ]);
        session()->flash('message', 'Permintaan telah ditolak.');
    }
}