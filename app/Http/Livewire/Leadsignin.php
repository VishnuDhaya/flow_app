<?php

namespace App\Http\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
class Leadsignin extends Component
{
       public $email;
       public $password;


    public function loginUser()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $email=$this->email;
        $password=$this->password;
        if(\Auth("leadportal")->attempt(array('email' => $email, 'password' => $password))){
            session()->flash('message', 'loged in.');
            $this->sessioning();
            return redirect(route("leadhome"));
        }else{
            session()->flash('error', 'email and password are wrong.');
        }
    }
    public function sessioning(){
        session()->put('app_user_id',auth('leadportal')->user()->id);
        session()->put('country_code', auth('leadportal')->user()->country_code);
        session()->put('acc_prvdr_code', 'UEZM');
        session()->put('user_role',auth('leadportal')->user()->role);
        $natureDrop = db::select("select * from master_data where data_key = 'UEZM_MainContent_ddlNatureOfBusiness'");
        $zoneDrop = db::select("select * from master_data where data_key = 'UEZM_MainContent_ddlZone'");
        $opertedbyDrop = db::select("select * from master_data where data_key = 'UEZM_MainContent_ddOperatedBy'");
        $walletDrop = db::select("select * from master_data where data_key = 'UEZM_MainContent_ddWallet'");
        session()->put('UEZM_MainContent_ddlNatureOfBusiness',$natureDrop);
        session()->put('UEZM_MainContent_ddlZone',$zoneDrop);
        session()->put('UEZM_MainContent_ddOperatedBy',$opertedbyDrop);
        session()->put('UEZM_MainContent_ddWallet',$walletDrop);
        session()->put('dropValues',true);
    }

    public function render()
    {
        return view('livewire.leadsignin');
    }

}
