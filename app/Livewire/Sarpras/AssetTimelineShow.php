<?php

namespace App\Livewire\Sarpras;

use App\Models\Asset;
use Livewire\Component;

class AssetTimelineShow extends Component
{
    public Asset $asset;

    protected $listeners = ['refresh-timeline' => '$refresh'];
    public function mount(Asset $asset)
    {
        $this->asset = $asset->load(['itemInfo.category', 'room', 'pic']);
    }

    public function render()
    {
        // 1. Ambil riwayat langsung dari tabel asset_histories (Registrasi, Mutasi Ruangan, Perubahan Kondisi)
        $histories = $this->asset->histories()->with('user')->get()->map(function ($history) {
            $icon = 'information-circle';
            $color = 'zinc';

            if ($history->activity_type === 'registration') {
                $icon = 'plus-circle';
                $color = 'emerald';
            } elseif ($history->activity_type === 'room_transfer') {
                $icon = 'arrow-path-rounded-square';
                $color = 'blue';
                
            } elseif ($history->activity_type === 'condition_update') {
                $icon = 'shield-exclamation';
                $color = 'amber';
            } elseif ($history->activity_type === 'pic_change') {
                $icon = 'user-group';
                $color = 'indigo';
            }

            $desc = $history->description;
            if ($history->old_value && $history->new_value) {
                $desc .= " (Dari: <b>{$history->old_value}</b> &rarr; Menjadi: <b>{$history->new_value}</b>)";
            }

            return [
                'date' => $history->created_at,
                'title' => $history->title,
                'description' => $desc,
                'icon' => $icon,
                'color' => $color,
                'raw' => $history
            ];
        });

        // 2. Ambil riwayat peminjaman
        $loans = $this->asset->loans()->with('user')->get()->map(function ($loan) {
            return [
                'date' => $loan->created_at,
                'title' => 'Peminjaman Aset',
                'description' => 'Dipinjam oleh ' . ($loan->user->name ?? 'User') . ' (Status: ' . ucwords($loan->status) . '). Catatan: ' . ($loan->notes ?? '-'),
                'icon' => 'arrow-top-right-on-square',
                'color' => 'purple',
                'raw' => $loan
            ];
        });

        // Gabungkan seluruh riwayat dan urutkan secara kronologis terbalik (terbaru di atas)
        $timeline = $histories->concat($loans)->sortByDesc('date');

        return view('livewire.sarpras.asset-timeline-show', [
            'timeline' => $timeline
        ])->layout('layouts.app');
    }
}