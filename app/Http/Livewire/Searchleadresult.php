<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Searchleadresult extends Component
{
    public $results="";

    protected $listeners = ['showResult'];

    public function showResult($results){
        $this->results =$results;
        if(!$results){
            session()->flash('result','No result found for your search');
        }
    }

    public function render()
    {
        return view('livewire.searchleadresult');
    }
}
