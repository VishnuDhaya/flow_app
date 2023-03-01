<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Services\LeadService;
use PHPUnit\Framework\Warning;

class Searchlead extends Component
{
    public $searchfilter = [
        'created_from' => '',
        'created_to' => '',
        'account_num' => '',
        'mobile_num' => '',
        'UEZM_MainContent_txtRecruiterID' => '',
        'status' => '',
        'biz_name' => '',
    ];

    protected $listeners = ['search'];

    public $result;

    public function search(){
        $this->searchfilter['country_code'] = session('country_code') ;
        $this->searchfilter['acc_purpose'] = 'terminal_financing';
        $leadserv = new LeadService();
        $this->result = $leadserv->search_lead($this->searchfilter,['id','biz_name','tf_status','created_at','account_num','profile_status']);
        $this->emit('showResult',$this->result);
    }
    public function resetVal()
    {
        $this->reset('searchfilter');
    }

    public $status = ['tf_01A_pending_prod_sel' => 'Pending Product Selection','tf_01_pending_dp' => 'Pending Downpayment', "tf_01_pending_dp_ver" => "Downpayment Verification", 'tf_02A_pending_rm_alloc' => 'Pending RM Allocation', 'tf_02_pending_flow_kyc' => 'Pending KYC', 'tf_10_pending_sc_gen' => 'Pending SC Code Generation', 'tf_10_pending_transfer_dp' => 'Pending Transfer of Downpayment', 'tf_20_pending_terminal_act' => 'Pending Terminal ID Activation', 'tf_30_pending_flow_disb' => 'Penidng Flow Loan Disbursal', 'tf_40_pending_pos_to_rm' => 'Pending POS to Flow RM', 'tf_50_pending_pos_to_cust' => 'Pending POS to Customer', 'tf_50_pending_repay_cycle' => 'Pending Repayment Cycle'];
    public function render()
    {
        return view('livewire.searchlead');
    }
}
