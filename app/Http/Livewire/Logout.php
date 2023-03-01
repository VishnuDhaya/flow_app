<?php

namespace App\Http\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Logout extends Component
{

    public function logout()
    {
        Auth('leadportal')->logout();

        return redirect('/leads/login');
    }

    public function render()
    {
        return view('livewire.logout');
    }
}
