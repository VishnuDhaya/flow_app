<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Navbar extends Component
{

    protected $listeners = ['deleted','fadeAlert'];

    public function deleted($id){
        session()->flash('delete',$id." has been deleted");
        $this->render();
        $this->dispatchBrowserEvent('lead-deleted');
    }

    public function fadeAlert(){
        $this->dispatchBrowserEvent('no-alert');
    }

    public function logout()
    {
        Auth('leadportal')->logout();

        return redirect('/leads/login');
    }

    public function render()
    {
        return view('livewire.navbar');
    }
}
