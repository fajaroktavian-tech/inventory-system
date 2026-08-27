<?php

namespace App\Livewire\Sarpras;

use App\Models\Asset;
use Livewire\Component;

class AssetTimelineShow extends Component
{
    public Asset $asset;

    public function mount(Asset $asset)
    {
        $this->asset = $asset->load(['itemInfo.category', 'room', 'pic']);
    }

    public function render()
    {
        // 1. Ambil riwayat peminjaman
        $loans = $this->asset->loans()->with('user')->get()->map(function($loan) {
            return [
                'date' => $loan->created_at,
                'title' => 'Peminjaman Aset',
                'description' => 'Dipinjam oleh ' . ($loan->user->name ?? 'User') . ' (Status: ' . ucwords($loan->status) . '). Catatan: ' . ($loan->notes ?? '-'),
                'icon' => 'arrow-top-right-on-square',
                'color' => 'blue',
                'raw' => $loan
            ];
        });

        // 2. Tambahkan event registrasi awal aset
        $registration = collect([
            [
                'date' => $this->asset->created_at,
                'title' => 'Registrasi Unit Aset',
                'description' => 'Aset terdaftar ke sistem dengan kondisi awal: ' . ucwords($this->asset->condition) . ' di ruangan ' . ($this->asset->room->name ?? '-'),
                'icon' => 'plus-circle',
                'color' => 'emerald',
                'raw' => $this->asset
            ]
        ]);

        // Gabungkan dan urutkan secara kronologis terbalik (terbaru di atas)
        $timeline = $registration->concat($loans)->sortByDesc('date');

        return view('livewire.sarpras.asset-timeline-show', [
            'timeline' => $timeline
        ])->layout('layouts.app');
    }
}