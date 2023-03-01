<?php

namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\MasterAgreement;
use Log;
use Carbon\Carbon;
use App\Consts;

class AgreementRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{

	public function __construct()
    {
    	parent::__construct();
    }
    
	public function model(){
		return MasterAgreement::class;
	}

	public function create(array $data){
		
		$date = aggr_date();
		$data['country_code'] = session('country_code');
		$data['aggr_doc_id'] = "AGRT-{$data['lender_code']}-$date";
		$data['status'] = "enabled";
	 	// if(!empty($data['product_ids'])){
   //         $product_id_csv = implode(',', $data['product_ids']) ;
   //     }else{
   //         thrw("Choose products to generate Agreement");
   //     } 
   //     $data['product_id_csv'] = $product_id_csv;
       
        
      /*	if(!array_key_exists('valid_from', $data)){            
            thrw("Valid from is required");
        }
		if(array_key_exists('valid_upto', $data)){ 
			if($data['valid_upto'] < Carbon::now()){
				thrw("Valid Upto date must be a future date");
			}
		} */
		if(!array_key_exists('aggr_duration', $data)){
			thrw("Agreement validity is required");
		}
		
		parent::insert_model($data);

		return $data;
	}

	public function list_master_agreements($country_code){
		$lender_code = config('app.lender_code_config')[$country_code];
		return parent::get_records_by_many(['lender_code'], [$lender_code]);
		//return parent::get_records_by_country_code();
		
	}
	 public function get_recent_master_agreement()
        {      
            return DB::selectOne("/*$this->api_req_id*/ select aggr_doc_id,product_id_csv,valid_from,
            valid_upto,status from master_agreements where status = ? and country_code = ? order by id desc limit 1", ["enabled", session('country_code')]);
        }
	public function list($id){
		throw new BadMethodCallException();
	}


	public function view($id){
		throw new BadMethodCallException();
	}

	public function update($id){
		throw new BadMethodCallException();
	}

	public function delete($id){
		throw new BadMethodCallException();
	}
}