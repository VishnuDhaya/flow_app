<?php

namespace App\Http\Livewire;

use App\Models\Lead;
use App\Models\LeadportalUser;
use App\Repositories\SQL\LeadRepositorySQL;
use Livewire\Component;

class Sidemenu extends Component
{
    public $username;
    public $role;
    public $acronym;

    protected $listeners= ['to_page'];

    public function mount(){
        $app_user_id = session('app_user_id');
        $user = LeadportalUser::where('id',$app_user_id)->get();
        $this->username = $user[0]->fname." ".$user[0]->lname;
        $this->role = $user[0]->role;
        $this->dp();
        $this->emit("statusVal",["tf_10_pending_choose_product" => "Product Selection","tf_10_pending_dp" => "Downpayment","tf_10_pending_dp_ver" => "Downpayment Verification","tf_10_pending_sc_gen" => "SC Code Generation", "tf_10_pending_transfer" => "Transfer Downpayment to SC Code", "tf_10_pending_terminal_act" => "Activate Terminal", "tf_20_pending_flow" => "Loan Disbursal", "tf_30_pos_to_rm" => "Handover POS to Flow RM", "tf_40_pos_to_cust" => "Handover POS to Customer", "tf_50_pending_repay_to_cycle" => "Customer On Board"]);
    }
    public function to_page($page){
        return $this->redirect(route($page));
    }

    public function dp(){
        $words = explode(" ", $this->username);
        $acronym = "";

        foreach ($words as $w) {
            $acronym .= $w[0];
        }
        $this->acronym = $acronym;
    }
    public function render()
    {
        return view('livewire.sidemenu');
    }
}
