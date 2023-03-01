<?php

namespace App\Http\Livewire;

use App\Repositories\SQL\LeadRepositorySQL;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use DB;

class Searchpad extends Component
{
    public $user_id;
    public $leads;
    public $searchterm="";

    protected $listeners = [
        'statusVal',
        'deleted'
    ];
    public function mount(){
        $this->user_id = session('app_user_id');
    }


    public function deleted($id){
        $this->render();
    }

    public function render()
    {
        $lead_repo = new LeadRepositorySQL();
        if($this->searchterm == ""){
            $this->leads = DB::select("select id, biz_name,tf_status,created_at,account_num, profile_status from leads where JSON_CONTAINS(acc_purpose, JSON_ARRAY('terminal_financing')) and country_code = ?", [session('country_code')]);
            $this->leads = array_reverse($this->leads);
        }
        else{
            $term = "%".$this->searchterm."%";
            $this->leads = $lead_repo->searchterm($term);
        }
        if($this->leads) {
            session()->put('lead_id', $this->leads[0]->id);
            $this->emit('homeProfile', $this->leads[0]->id);
        }
        return view('livewire.searchpad');
    }
}
