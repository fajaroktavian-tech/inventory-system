<?php

namespace App\Livewire\Sarpras;

use Livewire\Component;

class KiosGateway extends Component
{
    public function render()
    {
        return view('livewire.kios-gateway')->layout('layouts.guest');
    }
}