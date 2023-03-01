<?php

namespace App\Http\Livewire;

use App\Repositories\SQL\LeadRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Services\Mobile\RMService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Services\LeadService;

class Createlead extends Component
{
    public $leadDataArr =[
        'acc_purpose' => ["terminal_financing"],
        'product' => '',
        'biz_name' => '',
        'mobile_num' => '',
        'national_id' => '',
        'acc_prvdr_code' => 'UEZM',
        'created_by' => '',
        'tf_status' => '',
        'status' => '',
        'channel' => 'partner_portal',
        'UEZM_MainContent_txtAbbreviationName' => '',
        'UEZM_MainContent_txtCompanyRegistrationNo' => '',
        'UEZM_MainContent_ddlNatureOfBusiness' => '',
        'UEZM_MainContent_ddOperatedBy' => '',
        'UEZM_MainContent_ddWallet' => '',
        'UEZM_MainContent_txtRecruiterID' => '',
        'UEZM_MainContent_ddlZone' => ''
        ];

    protected $messages = [
        'leadDataArr.biz_name.required' => 'The Biz name cannot be empty.',
        'leadDataArr.mobile_num.required' => 'The Mobile number cannot be empty.',
        'leadDataArr.national_id.required' => 'The National ID cannot be empty.',
        'leadDataArr.product.required' => 'Select the Product to Continue'
    ];

    public $natureDrop;
    public $zoneDrop;
    public $opertedbyDrop;
    public $prdct;

    public function mount(){
        $this->prdctList();
    }
    public $productDrop="";
    public function prdctList(){
        $rm_serv = new RMService();
        $prdctjson = $rm_serv->prdctList();
        foreach ($prdctjson as $prdctsub){
            $this->productDrop .= '<option value="'.$prdctsub['data_code'].'">'.$prdctsub['data_value'].'</option>';
        }

        $this->productDrop = trim($this->productDrop,'"');
    }

    public function create_lead(){
        $queryArry=[];
        $this->validate([
            'leadDataArr.biz_name' => 'required',
            'leadDataArr.mobile_num' => 'required',
            'leadDataArr.national_id' => 'required',
            'leadDataArr.product' => 'required'
        ]);
        $this->leadDataArr['created_by'] = auth('leadportal')->user()->id;
        $this->leadDataArr['tf_status'] = 'tf_01_pending_dp';
        $this->leadDataArr['status'] = '08_pending_dp';
        m_array_filter($this->leadDataArr);
        $queryArry['lead']=$this->leadDataArr;
        try{
            $leadserv = new LeadService();
            $leadserv->create_lead($queryArry,true);
            session()->flash('status', 'Lead Successfully Created!');
            $this->reset();
        }
        catch (\Exception $e){
            session()->flash('status',$e->getMessage());
        }
    }


    public function render()
    {
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
        return view('livewire.createlead');
    }
}
