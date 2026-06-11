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
    
    // Untuk fitur edit jumlah saat approval
    public $editingId = null;
    public $editQuantities = [];

    public function render()
    {
        return view('livewire.request-approval', [
            'requests' => RequestModel::with(['student', 'class', 'room', 'details.item'])
                ->where('status', 'pending')
                ->whereHas('student', function($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                })
                ->latest()
                ->paginate(10)
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
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        session()->flash('message', 'Permintaan telah ditolak.');
    }
}