<?php

namespace App\Http\Livewire;

use App\Repositories\SQL\LeadRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Services\LeadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use PHPUnit\Framework\Warning;

class Homeprofile extends Component
{
    public $btn_view =[
        'rejected' => 'hidden',
        'edit_delete' => '',
        'chg_prdct' => '',
        'update' => '',
        'view' => ''
    ];
    public $user_id;
    public $leadData = [
        'biz_name' => '',
        'account_num'=>'',
        'mobile_num' => '',
        'product' => '',
        'national_id' => '',
        'cust_id' => '',
        'flow_rm' => '',
        'tf_status' => '',
        'terminal_id' => '',
        'UEZM_MainContent_txtAbbreviationName' => '',
        'UEZM_MainContent_txtCompanyRegistrationNo' => '',
        'UEZM_MainContent_ddlNatureOfBusiness' => '',
        'UEZM_MainContent_ddOperatedBy' => '',
        'UEZM_MainContent_ddWallet' => '',
        'UEZM_MainContent_txtRecruiterID' => '',
        'UEZM_MainContent_ddlZone' => ''
    ];

    public $update_data = [
        'downpayment' => ['amount' => '','date' => '','proof' => ''],
        'sc_code' => ['sc_code' => ''],
        'transfer' => ['amount' => '','date' => '','txn_id' => ''],
        'activation' => ['terminal_id' => ''],
        'loan' => ['status' => 'pending','amount' => '','txn_id' => '','date' => ''],
        'rm_handover' => ['date' => ''],
        'cust_handover' => ['date' => '']
    ];

    public $profile_status;
    protected $listeners = ['homeProfile','delete'];

    public function mount(){
        $id = session('lead_id');
        if($id){
            $this->homeProfile($id);
        }

    }

    public function homeProfile($lead_id){
        $this->reset('leadData');
        if(session()->has('crntId')){
            $this->user_id = session()->get('crntId');
        }
        else{
            $this->user_id = $lead_id;
        }
        $lead_repo = new LeadRepositorySQL();
        $prdct_repo = new LoanProductRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $status = $lead_repo->get_record_by("id",$this->user_id,['status'])->status;
        if($status <= '40_pending_kyc'){
            $lead = $lead_repo->get_record_by("id",$this->user_id,['tf_status','product','flow_rel_mgr_id','lead_json','profile_status']);
            $lead_array = json_decode($lead->lead_json,true);
            $this->leadData = array_merge($this->leadData,$lead_array);
            $this->profile_status = $lead->profile_status;
            $this->leadData['tf_status'] = $lead->tf_status;
            $this->leadData['flow_rm'] = $lead->flow_rel_mgr_id != null ? $person_repo->full_name($lead->flow_rel_mgr_id) : null;
            if($lead->product){
                $this->leadData['product'] = $prdct_repo->get_tf_product($lead->product);
            }
        }
        else{
            $lead = $lead_repo->get_record_by("id",$this->user_id,['tf_status','product','flow_rel_mgr_id','cust_reg_json','update_data_json','account_num','cust_id','profile_status']);
            $lead_array = json_decode($lead->cust_reg_json,true);
            $update_array = json_decode($lead->update_data_json, true);
            if($update_array){
                $this->update_data = array_merge($this->update_data,$update_array);
            }
            $this->leadData['biz_name'] = $lead_array['biz_info']['biz_name']['value'];
            $this->leadData['mobile_num'] = $lead_array['biz_identity']['mobile_num']['value'];
            $this->leadData['national_id'] = $lead_array['owner_person']['national_id']['value'];;
            $this->leadData['cust_id'] = $lead_array['cust_id'];
            $this->leadData['UEZM_MainContent_txtAbbreviationName'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_txtAbbreviationName']['value'];
            $this->leadData['UEZM_MainContent_txtCompanyRegistrationNo'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_txtCompanyRegistrationNo']['value'];
            $this->leadData['UEZM_MainContent_ddlNatureOfBusiness'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_ddlNatureOfBusiness']['value'];
            $this->leadData['UEZM_MainContent_ddOperatedBy'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_ddOperatedBy']['value'];
            $this->leadData['UEZM_MainContent_ddWallet'] = array_key_exists('UEZM_MainContent_ddWallet',$lead_array) ? $lead_array['UEZM_MainContent_ddWallet'] : 3;
            $this->leadData['UEZM_MainContent_txtRecruiterID'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_txtRecruiterID']['value'];
            $this->leadData['UEZM_MainContent_ddlZone'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_ddlZone']['value'];
            $this->leadData['tf_status'] = $lead->tf_status;
            $this->leadData['flow_rm'] = $lead->flow_rel_mgr_id != null ? $person_repo->full_name($lead->flow_rel_mgr_id) : null;
            $this->profile_status = $lead->profile_status;
            if($lead->product){
                $this->leadData['product'] = $prdct_repo->get_tf_product($lead->product);
            }
            if(strcmp($this->leadData['tf_status'],"tf_10_pending_sc_gen") > 0){
                $this->leadData['account_num'] = $lead->account_num;
            }
        }

        if($status == "20_rm_rejected"){
            $this->btn_visible("rejected");
        }
        elseif($status >= "59_pending_enable" or $this->leadData['tf_status'] == 'tf_50_pending_repay_cycle'){
            $this->btn_visible("view");
        }
        elseif($status >= "51_pending_tf_process"){
            $this->btn_visible("update");
        }
        elseif($status >= "40_pending_kyc"){
            $this->btn_visible("chg_prdct");
        }
        else{
            $this->btn_visible("edit_delete");
        }
    }

    public function dropValue($dataKey,$code){
        if($code) {
            if(session('dropValues')) {
                $drops = session($dataKey);
                foreach ($drops as $drop) {
                    if ($drop->data_code == $code) {
                        return $drop->data_value;
                    }
                }
            }
            else{
                $val = db::select("select data_value from master_data where data_key = '$dataKey' and data_code = '$code'");
                return $val[0]->data_value;
            }
        }
        else{
            return "";
        }
    }

    public function delete(){
        $lead_repo = new LeadRepositorySQL();
        $lead_repo->delete($this->user_id);;
        $this->emit('deleted',$this->leadData['biz_name']);
        $this->emit('search');
        $this->dispatchBrowserEvent('modal-deleted');
    }

    public function btn_visible($visible){
        $this->btn_view['rejected'] = "hidden";
        $this->btn_view['edit_delete'] = "hidden";
        $this->btn_view['chg_prdct'] = "hidden";
        $this->btn_view['update'] = "hidden";
        $this->btn_view['view'] = "hidden";
        $this->btn_view[$visible] = "";
    }
    public function RMName($id){
        if($id == null){
            return null;
        }
        $persons_repo = new PersonRepositorySQL();
        $rm_name = $persons_repo->full_name($id);
        return $rm_name;
    }

    public function tosite($mode,$id,$title){
        session()->put('id',$id);
        session()->put('title',$title);
        return redirect(route($mode));
    }


    public function render()
    {
        session()->forget('message');
        return view('livewire.homeprofile');
    }
}
