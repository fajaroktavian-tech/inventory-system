<?php

namespace App\Livewire;

use Livewire\Component;

class StudentRequestStatus extends Component
{
    public function render()
    {
        $myRequests = \App\Models\RequestModel::with(['details.item'])
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.student-request-status', [
            'myRequests' => $myRequests
        ]);
    }
}
