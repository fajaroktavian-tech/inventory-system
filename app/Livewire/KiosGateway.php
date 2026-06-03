<?php

namespace App\Livewire;

use Livewire\Component;

class KiosGateway extends Component
{
    public function render()
    {
        return view('livewire.kios-gateway')->layout('layouts.guest');
    }
}