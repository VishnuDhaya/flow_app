<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\AgreementRepositorySQL;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Validators\FlowValidator;
use App\Services\AgreementService;
use App\Consts;
use Log;
use PDF;

class AgreementController extends ApiController
{

	public function generate_new_master_agreement(Request $req){

    	$data = $req->data;
    	$check_validate = FlowValidator::validate($data, array("master_agreement"));
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate);       
        }

        $aggr_serv = new AgreementService($req->data['country_code']);
        $aggr = $aggr_serv->generate_new_master_agreement($data);	

        if ($aggr) {
             return $this->respondCreated("New Master Agreement created successfully. \nAGRMT DOC ID : ".$aggr['aggr_doc_id'], ["aggr_file_rel_path" => $aggr['aggr_file_rel_path']]);
        }else{
            return $this->respondInternalError("Unknown Error");

        }
      
    }

  

    public function list_master_agreements(Request $req){
      
        $data = $req->data;
        $aggr_serv = new AgreementService($req->data['country_code']);
        $agreements = $aggr_serv->list_master_agreements($data);
        return $this->respondData($agreements);

    }
    

    /*public function get_name_list(Request $req){
    	$data = $req->data;    
        $aggr_repo = new AgreementRepositorySQL();
        $aggr_doc_ids = $aggr_repo->get_name_list($data['country_code'],["id","aggr_doc_id"], null, $data['status']);
        
        return $this->respondData($aggr_doc_ids);
    }*/ 

    public function generate_new_cust_agreement(Request $req){

        $data = $req->data;


        $aggr_serv = new AgreementService($data['country_code']);
        $aggr = $aggr_serv->generate_new_cust_agreement($data);   
       
        if ($aggr['aggr_doc_id']) {
             return $this->respondCreated("Agreement generated successfully\n".$aggr['aggr_doc_id'], ["aggr_doc_id" => $aggr["aggr_doc_id"]]);
        }else{
            return $this->respondInternalError("Unknown Error");

        }
    }
    
     public function get_existing_aggr(Request $req){
        $data = $req->data;
        $cust_aggr_repo = new CustAgreementRepositorySQL();
        $response = $cust_aggr_repo->get_existing_aggr($data['cust_id']);

        return $this->respondData($response);
    }

    public function save_agreement(Request $req){
        $data = $req->data;
        $aggr_serv = new AgreementService($data['country_code']);
        $update_status = $aggr_serv->save_agreement($data);
        return $this->respondData($update_status);
    }

    public function load_aggrs_to_upload(Request $req){
        $data = $req->data;
        $aggr_serv = new AgreementService($data['country_code']);
        $data = $aggr_serv->load_aggrs_to_upload($data);
        return $this->respondData($data);
    }

    public function update_aggr_status(Request $req){
        $data = $req->data;

        $aggr_serv = new AgreementService($data['country_code']);
        $aggr_serv->inactivate_aggr($data['aggr_id'], $data['cust_id']);
      
        return $this->respondSuccess("Inactivated Agreement successfully.");
    }

    public function list_dp_lndr_spcfc_products(Request $req){
        $data = $req->data;
        Log::warning("$$$ DIVINE...");
        $loan_product_repo = new LoanProductRepositorySQL();
        $dp_lndr_spcfc_products = $loan_product_repo->get_products_by($data['lender_code'], session('acc_prvdr_code'), $data['agrmt_for'], 'enabled');
        
        return $this->respondData($dp_lndr_spcfc_products);

    }


}