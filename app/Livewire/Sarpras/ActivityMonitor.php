<?php

namespace App\Livewire\Sarpras;

use App\Models\RequestModel;
use App\Models\Item;
use App\Models\Room;
use Livewire\Component;

class ActivityMonitor extends Component
{
    public function render()
    {
        return view('livewire.activity-monitor', [
            'totalItems' => Item::sum('stock'),
            'totalRooms' => Room::count(),
            'todayRequests' => RequestModel::whereDate('created_at', now())->count(),
            'lowStockCount' => Item::where('stock', '<', 5)->count(),
            
            'recentActivities' => RequestModel::with(['student', 'details.item', 'class', 'room'])
            ->latest()
            ->take(10)
            ->get(),
        ])->layout('layouts.guest1');
    }
}