<?php

namespace App\Http\Controllers\FlowApp;

use App\Services\WriteOffService;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\WriteOffRepositorySQL;
use App\Repositories\SQL\LoanProvisioningRepositorySQL;
use Illuminate\Support\Facades\Log;

class WriteOffController extends ApiController
{   
	    //$loan_service = null;
   
    public function req_write_off(Request $req)
    {
        $data = $req->data;
        $write_off_serv =  new WriteOffService();
        $response = $write_off_serv->request_write_off($data); 
        
        return $this->respondData($response);   
    }

    public function get_write_off(Request $req)
    {
        $data = $req->data;
        $write_off_serv =  new WriteOffService();
        $response = $write_off_serv->get_write_off($data);
        
        return $this->respondData($response);   
    }

    public function listWriteOff(Request $req){
        $data = $req->data;  
        $write_off_serv =  new WriteOffService();
        $response = $write_off_serv->list_write_off($data);
        
        return $this->respondData($response);
    }

    public function appr_reject_write_off(Request $req){
        $data = $req->data;
        $write_off_serv =  new WriteOffService();
        if($data['mode'] == 'Approve'){
            $response = $write_off_serv->approve_write_off($data);
        }else if($data['mode'] == 'Reject'){
            $response = $write_off_serv->reject_write_off($data);
        }
        
        return $this->respondData($response);   
    }

    public function get_recovery_amount(Request $req){
        $data = $req->data;
        $write_off_serv =  new WriteOffService();
        $loan_prov_repo =  new LoanProvisioningRepositorySQL();
        $loan_prov = $loan_prov_repo->get_record_by('id', $data['loan_prov_id'], ['year']);
        $data['year'] = $loan_prov->year;
        $response = $write_off_serv->get_recovery_amount($data);
        return $this->respondData($response);   
    }

    public function get_loan_prov_year(Request $req){
        $data = $req->data;
        $loan_prov_repo = new LoanProvisioningRepositorySQL();
        $resp['list'] = $loan_prov_repo->get_loan_prov_year();
        return $this->respondData($resp);
    }
}