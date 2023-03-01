<?php

namespace App\Http\Livewire;

use App\Consts;
use App\Services\Mobile\RMService;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use App\Services\Partners\UEZM\EzeeMoneyService;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Services\LeadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Updatelead extends Component
{
    use WithFileUploads;
    public  $user_id;

    public $leadDataArr =[
        'id' => '',
        'acc_purpose' => [],
        'product' => '',
        'biz_name' => '',
        'cust_id' => '',
        'mobile_num' => '',
        'national_id' => '',
        'acc_prvdr_code' => 'UEZM',
        'terminal_id' => '',
        'account_num' => '',
        'created_by' => '',
        'tf_status' => '',
        'status' => '',
        'UEZM_MainContent_txtAbbreviationName' => '',
        'UEZM_MainContent_txtCompanyRegistrationNo' => '',
        'UEZM_MainContent_ddlNatureOfBusiness' => '',
        'UEZM_MainContent_ddOperatedBy' => '',
        'UEZM_MainContent_ddWallet' => '',
        'UEZM_MainContent_txtRecruiterID' => '',
        'UEZM_MainContent_ddlZone' => ''
    ];

    public $txn_img;

    public $update_data = [
        'downpayment' => ['amount' => '','date' => '','proof' => ''],
        'verification' => ['name' => '','ver_date' => '', 'txn_id' => ''],
        'sc_code' => ['sc_code' => ''],
        'transfer' => ['amount' => '','date' => '','txn_id' => ''],
        'activation' => ['terminal_id' => ''],
        'loan' => ['status' => 'pending','amount' => '','txn_id' => '','date' => ''],
        'rm_handover' => ['date' => ''],
        'cust_handover' => ['date' => '']
    ];

    protected $messages = [
        'update_data.verification.name.required' => "Approver Name can't be empty",
        'update_data.verification.txn_id.required' => "Transaction ID can't be empty",
        ];


    public $downpayment;
    protected $listeners=['crntStatus','saveUpdate','reload'];

    public $status;
    public $tf_status;
    public $title;
    public $crnt_tf_status;

    public function mount(){
        $this->user_id = session()->get('id');
        $this->title = session()->get('title');
        $this->updatelead();
        $this->prdctList();
    }
    public $natureDrop;
    public $opertedbyDrop;
    public $zoneDrop;
    public $productDrop="";

    public function prdctList(){
        $rm_serv = new RMService();
        $prdctjson = $rm_serv->prdctList();
        foreach ($prdctjson as $prdctsub){
            $this->productDrop .= '<option value="'.$prdctsub['data_code'].'">'.$prdctsub['data_value'].'</option>';
        }

        $this->productDrop = trim($this->productDrop,'"');
    }

    public function reload(){
        sleep(4);
        return $this->redirect(route('leadupdate'));
    }
    public function crntStatus($status){
        $this->crnt_tf_status = $status;
    }

    public $leadType;

    public $clsname = ['tf_00_pending_profile','tf_01A_pending_prod_sel','tf_01_pending_dp','tf_02_pending_flow_kyc','tf_10_pending_sc_gen','tf_10_pending_transfer_dp','tf_20_pending_terminal_act','tf_30_pending_flow_disb','tf_40_pending_pos_to_rm','tf_50_pending_pos_to_cust','tf_50_pending_repay_cycle','type'];

    public function updatelead(){
        $lead_repo = new LeadRepositorySQL();
        $prdct_repo = new LoanProductRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $this->status = $lead_repo->get_record_by("id",$this->user_id,['status'])->status;
        if($this->status <= '40_pending_kyc'){
            $lead = $lead_repo->get_record_by("id",$this->user_id,['tf_status','flow_rel_mgr_id','lead_json','product','update_data_json','cust_id','type']);
            $lead_array = json_decode($lead->lead_json,true);
            $update_array = json_decode($lead->update_data_json, true);
            if($update_array){
                $this->update_data = array_merge($this->update_data,$update_array);
            }
            $this->leadDataArr = array_merge($this->leadDataArr,$lead_array);
            $this->leadDataArr['tf_status'] = $lead->tf_status;
            $this->leadDataArr['product'] = $lead->product;
            $this->leadDataArr['flow_rm'] = $lead->flow_rel_mgr_id != null ? $person_repo->full_name($lead->flow_rel_mgr_id) : null;
            $this->type = $lead->type;
            if($lead->product){
                $product = $prdct_repo->get_tf_product($lead->product);
                $this->downpayment =  $product['purchase_cost'] - $product['amount'];
            }
//            $this->txn_img = $this->update_data['downpayment']['proof'];
        }
        else{
            $lead = $lead_repo->get_record_by("id",$this->user_id,['tf_status','flow_rel_mgr_id','product','cust_reg_json','update_data_json','cust_id','account_num','type']);
            $lead_array = json_decode($lead->cust_reg_json,true);
            $update_array = json_decode($lead->update_data_json, true);
            if($update_array){
                $this->update_data = array_merge($this->update_data,$update_array);
            }
            $this->leadDataArr['biz_name'] = $lead_array['biz_info']['biz_name']['value'];
            $this->leadDataArr['mobile_num'] = $lead_array['biz_identity']['mobile_num']['value'];
            $this->leadDataArr['account_num'] = $lead->account_num;
            $this->leadDataArr['national_id'] = $lead_array['owner_person']['national_id']['value'];
            $this->leadDataArr['product'] = $lead->product;
            $this->leadDataArr['cust_id'] = $lead_array['cust_id'];
            $this->leadDataArr['UEZM_MainContent_txtAbbreviationName'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_txtAbbreviationName']['value'];
            $this->leadDataArr['UEZM_MainContent_txtCompanyRegistrationNo'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_txtCompanyRegistrationNo']['value'];
            $this->leadDataArr['UEZM_MainContent_ddlNatureOfBusiness'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_ddlNatureOfBusiness']['value'];
            $this->leadDataArr['UEZM_MainContent_ddOperatedBy'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_ddOperatedBy']['value'];
            $this->leadDataArr['UEZM_MainContent_ddWallet'] = array_key_exists('UEZM_MainContent_ddWallet',$lead_array) ? $lead_array['UEZM_MainContent_ddWallet'] : 3;
            $this->leadDataArr['UEZM_MainContent_txtRecruiterID'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_txtRecruiterID']['value'];
            $this->leadDataArr['UEZM_MainContent_ddlZone'] = $lead_array['partner_kyc']['UEZM']['UEZM_MainContent_ddlZone']['value'];
            $this->leadDataArr['tf_status'] = $lead->tf_status;
            $this->leadDataArr['flow_rm'] = $lead->flow_rel_mgr_id != null ? $person_repo->full_name($lead->flow_rel_mgr_id) : null;
            $this->type = $lead->type;
            if($lead->product){
                $product = $prdct_repo->get_tf_product($lead->product);
                $this->downpayment =  $product['purchase_cost'] - $product['amount'];
            }
//            $this->txn_img = $this->update_data['downpayment']['proof'];
        }
        $this->tf_status = $this->leadDataArr['tf_status'];
        $this->leadDataArr['status'] = $this->status;
    }


    public $txns;
    public function txn_ids(){
        $stmt_txns = db::select("select stmt_txn_id,stmt_txn_date from account_stmts where cr_amt = ? and date(stmt_txn_date) = ? and country_code = 'UGA'",[$this->update_data['downpayment']['amount'],$this->update_data['downpayment']['date']]);
        $lead_txns = db::select("select json_extract(update_data_json,'$.verification.txn_id') as lead_txn_id from leads where json_extract(update_data_json,'$.verification.txn_id') is not null and json_extract(update_data_json,'$.verification.txn_id') != '\"\"' and country_code = 'UGA'");
        $this->txns = $stmt_txns;
        foreach ($stmt_txns as $key => $stxn){
            foreach ($lead_txns as $ltxn){
                if($ltxn->lead_txn_id == ('"'.$stxn->stmt_txn_id.'"')){
                    unset($this->txns[$key]);
                    break;
                }
            }
        }
    }

    public function verify(){
        // $this->validate([
        //     'update_data.verification.name' => 'required',
        //     'update_data.verification.txn_id' => 'required',
        // ]);
        $ver = true;
        $status = "tf_01_pending_dp_ver";
        // foreach ($this->txns as $txn) {
        //     if ($txn['stmt_txn_id'] == $this->update_data['verification']['txn_id']) {
        //         $ver = true;
        //     }
        // }
        if ($ver) {
            if ($this->leadDataArr['flow_rm']) {
                if ($this->type == 're_kyc') {
                    $this->leadDataArr['status'] = Consts::PENDING_KYC;
                } else {
                    $this->leadDataArr['status'] = Consts::PENDING_RM_EVAL;
                }
                $status = 'tf_02_pending_flow_kyc';
            } else {
                $this->leadDataArr['status'] = '09_pending_rm_alloc';
                $status = 'tf_02A_pending_rm_alloc';
            }
            $this->update_data['verification']['ver_date'] = Carbon::now();
            $this->saveUpdate($status);
        }
        else{
            $this->update_data['verification']['txn_id'] = '';
            session()->flash('updation',"No records for this Transaction ID");
        }
    }

    // public function status($clsname,$sbar = false){
    //     if($sbar){
    //         if($this->status == $clsname){
    //             return "crnt.png";
    //         }
    //         elseif($this->status > $clsname){
    //             return "pending.png";
    //         }
    //         else{
    //             return "finished.png";
    //         }
    //     }
    //     else{
    //         if($this->status == $clsname){
    //             return "";
    //         }
    //         else{
    //             return "dis-input";
    //         }
    //     }
    // }

    public function get_sc_code(){
        $ezeeServ =new EzeeMoneyService;
        $resp = $ezeeServ->create_sc_code($this->user_id);
        if($resp['status'] == "success"){
            $this->update_data['sc_code']['sc_code']  = $resp['sc_code'];
            session()->flash('updation',$resp['message']);
        }
        else{
            session()->flash('updation',$resp['message']);
        }
        return 1;
    }

    public function saveUpdate($status){
        if($this->leadDataArr['tf_status'] == 'tf_01_pending_dp'){
            $info="";
            if($this->update_data['downpayment']['amount'] != $this->downpayment){
                if($this->update_data['downpayment']['amount'] < $this->downpayment){
                    $info = "Downpayment amount is low by ".($this->downpayment - $this->update_data['downpayment']['amount']);
                }
                else{
                    $info = "Downpayment amount is excessive by ".($this->update_data['downpayment']['amount']-$this->downpayment);
                }
                session()->flash('downpayment',$info);
                $status = null;
            }
            else{
                $img_name = date("ymdHi").'.png';
                $this->update_data['downpayment']['proof'] = $this->txn_img->storeAs('files/UGA/ezeeMoney_leadPortal/downpayment_proof',$img_name);
            }
        }
        if($this->leadDataArr['tf_status'] == 'tf_10_pending_transfer_dp'){
            $info="";
            if($this->update_data['transfer']['amount'] != $this->downpayment){
                if($this->update_data['transfer']['amount'] < $this->downpayment){
                    $info = "Transfer amount is low by ".($this->downpayment - $this->update_data['transfer']['amount']);
                }
                else{
                    $info = "Transfer amount is excessive by ".($this->update_data['transfer']['amount'] - $this->downpayment);
                }
                session()->flash('downpayment',$info);
                $status = null;
            }
        }
        if($status){
            $dup = false;
            if($this->update_data['transfer']['txn_id']){
                $leadrepo = new LeadRepositorySQL();
                $dup_data = $leadrepo->tf_update_dup_check($this->update_data['transfer']['txn_id'],$this->user_id);
                if($dup_data){
                    session()->flash('updation','Another lead exist with same Transaction ID');
                    $dup = true;
                }
            }
            if(!$dup){
                $this->leadDataArr['account_num'] = $this->update_data['sc_code']['sc_code'];
                $this->leadDataArr['tf_status'] = $status;
                $this->leadDataArr['id'] = $this->user_id ;
                $this->leadDataArr['update_data_json'] = json_encode($this->update_data);
                $leadserv = new LeadService();
                $queryupdate['lead'] = $this->leadDataArr;
                m_array_filter($queryupdate['lead']);
                $leadserv->update($queryupdate,true);
                session()->flash('updation','Lead Updated Successfully');
                return $this->redirect(route('leadupdate'));
            }
        }
        else{
            session()->flash('updation','Nothing to Save');
        }
    }

    public function toHome(){
        session()->flash('crntId',$this->user_id);
        return $this->redirect(route('leadhome'));
    }


    public function render()
    {
        if($this->update_data['downpayment']['date']){
            $this->txn_ids();
        }
        if(session()->has('dropValues')){
            $this->natureDrop = session('UEZM_MainContent_ddlNatureOfBusiness');
            $this->zoneDrop = session('UEZM_MainContent_ddlZone');
            $this->opertedbyDrop = session('UEZM_MainContent_ddOperatedBy');
        }
        else{
            $this->natureDrop = db::select("select * from master_data where data_key = 'UEZM_MainContent_ddlNatureOfBusiness'");
            $this->zoneDrop = db::select("select * from master_data where data_key = 'UEZM_MainContent_ddlZone'");
            $this->opertedbyDrop = db::select("select * from master_data where data_key = 'UEZM_MainContent_ddOperatedBy'");
        }

        return view('livewire.updatelead');
    }
}
