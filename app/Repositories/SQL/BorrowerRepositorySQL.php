<?php

namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\OrgRepositorySQL;
use App\Repositories\SQL\LenderRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Borrower;
use App\Consts;
use Exception;
use Carbon\Carbon;
class BorrowerRepositorySQL extends BaseRepositorySQL{
	public function __construct()
    {
      parent::__construct();
      $this->class = Borrower::class;
    }

	public function model(){

			return $this->class;
	}


	public function list(array $data){
		//Log::warning($data);
				//return DB::select("/*$this->api_req_id*/ select cust_id, master_cust_id, biz_name, b.status, b.data_prvdr_cust_id,  b.id,b.biz_type,l.name as lender_name,d.name as data_prvdr_name, mobile_num, first_name, middle_name ,last_name from borrowers as b  join lenders as l on b.lender_code = l.lender_code join data_prvdrs as d on b.data_prvdr_code = d.data_prvdr_code left join orgs as o on b.org_id= o.id left join persons as p on b.owner_person_id= p.id where b.country_code = ?", [$data['country_code']]);
        throw new BadMethodCallException();

	}

	public function additional_approval($data){

        $addl_appr_until = null;
        if($data['reject'] == 'false' && $data['approve'] == 'false'){
            thrw("Please select either approve or reject");
        }

        if($data['approve'] == 'true'){
            if(!array_key_exists('days', $data)){
                thrw("Please enter valid until days");
            }
            $days = $data['days'];
            if($days != 2){
                #$addl_appr_until = Carbon::now()->addYears(99);
                thrw("Please choose not more than 2 days");
            }else{
                $addl_appr_until = addDays($days);
            }
        }
        if($data['reject'] == 'true'){
            $addl_appr_until = null;
        }


        $upd_data = ['cust_id' => $data['cust_id'],
                                'addl_appr_until' => $addl_appr_until,
                            ];
        $this->update_model_by_code($upd_data);

    }

	/*public function update(array $borrower){
		$person_id = null;
		$org_id = null;

		$result = parent::update_model($borrower);

		return $result;
	}*/

	public function update_first_loan_date($cust_id,$loan_date = null){

        $loan_date = ($loan_date == null) ? date_db() : $loan_date;
		DB::table(DB::raw("borrowers /*$this->api_req_id*/ "))->where('cust_id', $cust_id)->where('country_code', $this->country_code)->whereNull('first_loan_date')->update(['first_loan_date' => $loan_date, 'perf_eff_date' => $loan_date]);

	}

    public function update_last_loan_date($cust_id){
        DB::table(DB::raw("borrowers /*$this->api_req_id*/ "))->where('cust_id', $cust_id)->where('country_code', $this->country_code)->update(['last_loan_date' => date_db()]);

    }

    public function get_customer($cust_id)
    {
           return DB::selectOne("/*$this->api_req_id*/ select * from borrowers where cust_id = ? limit 1",[$cust_id]);
    }

/*
	public add_ref_borrower($borrower_id, $ref_borrower_id){



	}

	public attach_lender_to_borrower($borrower_id, $lender_id){


	}

	public add_account_to_borrwer($borrower_id, $lender_id){



	}
*/
	public function delete($id){
		throw new BadMethodCallException();

	}

	public function get_borrower_by($field_name,  $field_value, $fields_arr = ["*"]){
		return parent::get_record_by($field_name,  $field_value, $fields_arr);

	}


	 public function view($cust_id, array $models = ["*"], array $columns = ["borrower" => ["*"], "owner_person" => ["*"], "owner_address" => ["*"], "org" => ["*"], "reg_address" => ["*"], "biz_address" => ["*"], "contact_person" => ["*"]]){

                $person_repo = new PersonRepositorySQL();
                $addr_repo = new AddressInfoRepositorySQL();
                // TO DO SET Country Code in addr_repo
        //var_dump($id);
                $borrower = parent::find_by_code($cust_id, $columns["borrower"]);

                if($borrower->biz_type == Consts::INDIVIDUAL){

                        if(in_array("*", $models) || in_array("owner_person", $models)){
                                $borrower->owner_person = $person_repo->find($borrower->owner_person_id, $columns["owner_person"]);
                        }

                        if(in_array("*", $models) || in_array("owner_address", $models)){
                                $borrower->owner_person->owner_address = $addr_repo->find($borrower->owner_address_id, $columns["owner_address"]);
                        }

                }elseif ($borrower->biz_type == Consts::INSTITUTION){
                        $org_repo = new OrgRepositorySQL();

                        if(in_array("*", $models) || in_array("org", $models)){
                                $org  = $org_repo->view($borrower->org_id, $columns["org"]);
                                if(in_array("reg_address", $models))
                                {

                                        $address = $addr_repo->find($org->reg_address_id, $columns["reg_address"]);
                                        $org->reg_address = $address;
                                }
                                $borrower->org = $org;
                        }
                }

                if(isset($borrower->biz_address_id) && (in_array("*", $models) || in_array("biz_address", $models))){
                        $biz_address = $addr_repo->find($borrower->biz_address_id,$columns["biz_address"]);
                        $borrower->biz_address = $biz_address;
                }


                return $borrower;

        }



        public function update_brwr_aggr($cust_id, $aggr_doc_id, $valid_upto){

            $update_brwr = parent::update_model([
                                "cust_id" => $cust_id,
                                "current_aggr_doc_id" => $aggr_doc_id,
                                "aggr_valid_upto" => $valid_upto,
                                "aggr_status" => 'active'
                                ],"cust_id");

           return $update_brwr;
        }

       public function inactivate_current_aggr($cust_id){
            $this->update_model(['cust_id' => $cust_id, 'aggr_status' => 'inactive'], 'cust_id');
        }

        public function update_person_id($person_id){
        DB::update("/*$this->api_req_id*/ update borrowers set `contact_person_id` = ?  where `contact_person_id` = ?" , [$person_id, $person_id]);
        }
        public function get_cust_id($person_id)
        {
              return DB::selectOne("/*$this->api_req_id*/ select cust_id from borrowers where owner_person_id = ? and country_code = ? limit 1",[$person_id, session('country_code')]);
        }

        public function get_flow_rel_mgr_id($cust_id){
            $borrower = $this->find_by_code($cust_id);
            return $borrower->flow_rel_mgr_id;
        }

        public function get_all_customers(){
            return DB::select("/*$this->api_req_id*/ select * from borrowers where country_code = ? ", [session('country_code')]);
        }
        public function get_cca_customers($acc_pvrdr_code){
            return DB::select("/*$this->api_req_id*/ select acc_number, cust_id, biz_name,owner_person_id from borrowers where acc_prvdr_code = ?", [$acc_pvrdr_code]);
        }

        public function get_cust_id_from_mobile_num($mobile_num, $alternate = false){
            $person_repo = new PersonRepositorySQL();
            if(!$alternate){
                $persons =  collect($person_repo->get_records_by('mobile_num', $mobile_num, ['id']))->pluck('id')->toArray();
            }
            else{
                $persons =  collect($person_repo->get_records_by_any(['mobile_num','alt_biz_mobile_num_1', 'alt_biz_mobile_num_2'],
                                                              [$mobile_num, $mobile_num, $mobile_num], ['id']))->pluck('id')->toArray();
            }

            $custs = DB::table('borrowers')->where('country_code',session('country_code'))->where('status','enabled')->whereIn('owner_person_id',$persons)->pluck('cust_id')->toArray();
            return empty($custs) ? null : (sizeof($custs) > 1 ? $custs : $custs[0]);
        }

        public function get_cust_district($cust_id)
        {
           $biz_addr_id = ($this->find_by_code($cust_id, ['biz_address_id']))->biz_address_id;
           $addr_repo = new AddressInfoRepositorySQL();
           $addr_obj = $addr_repo->find($biz_addr_id);
           return $addr_obj ? $addr_obj['district'] : null;
        }


        public function get_last_loan($cust_id,$fields_arr){

            $loan_repo = new LoanRepositorySQL();
		    $borr_repo = new BorrowerRepositorySQL();
		    $borrower = $borr_repo->find_by_code($cust_id,['last_loan_doc_id']);
            $last_loan = null;
            if(isset($borrower->last_loan_doc_id)){
                $last_loan = $loan_repo->get_record_by('loan_doc_id', $borrower->last_loan_doc_id, $fields_arr);
            }

            return $last_loan;

        }

        public function get_current_loan($cust_id,$fields_arr){

            $loan_repo = new LoanRepositorySQL();
		    $borr_repo = new BorrowerRepositorySQL();
		    $borrower = $borr_repo->find_by_code($cust_id,['ongoing_loan_doc_id']);
            $ongoing_loan = null;
            if(isset($borrower->ongoing_loan_doc_id)){
                $ongoing_loan = $loan_repo->get_record_by('loan_doc_id', $borrower->ongoing_loan_doc_id, $fields_arr);
            }
            return $ongoing_loan;
        }
}