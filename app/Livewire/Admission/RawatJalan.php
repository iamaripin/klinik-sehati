<?php

namespace App\Livewire\Admission;

use Livewire\Component;

class RawatJalan extends Component
{
    public function render()
    {
        return view('livewire.admission.rawat-jalan')
            ->layout('layouts.app', [
                'title' => 'Admission Rawat Jalan',
            ]);
    }
}
