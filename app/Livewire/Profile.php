<?php

namespace App\Livewire;

use Livewire\Component;

class Profile extends Component
{
    public $count = 'haii';

    public function ubah(){
        $this->count = 'ubah';
        // $this->dispatch('alert', [
        //     'type' => 'success',
        //     'message' => 'Profile updated successfully!'
        // ]);
    }
    public function render()
    {
        return view('livewire.profile.index')
            ->layout('layouts.app', [
                'title' => 'Profile'
            ]);
    }
}
