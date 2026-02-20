<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DisplayAntrian extends Controller
{
    // --- LOGIN PAGE
    public function render()
    {
        return view('livewire.other.display-antrian');
    }

}
