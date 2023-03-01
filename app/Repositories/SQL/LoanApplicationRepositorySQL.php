<?php
 namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\LenderRepositorySQL;
use App\Repositories\SQL\AddressRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\LoanApplication;
use App\Consts;
use Carbon\Carbon;
use Log;

class LoanApplicationRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{
	
	public function __construct()
    {
      parent::__construct();

    }
    
	public function model(){
			return LoanApplication::class;
	}

	public function create(array $loan_application)
 	{	
 		$loan_application_id = parent::insert_model($loan_application); 
 		return $loan_application_id;
 	}
    
    public function update_loan_appl_id($loan_appl_doc_id, $id){
        $this->update_model(["id" => $id,  "loan_appl_doc_id" => $loan_appl_doc_id]);
    }
 	public function update(array $req){
 	    throw new BadMethodCallException();	
	}
	public function delete($id){
		throw new BadMethodCallException();	
	}

	public function list($id){
	   throw new BadMethodCallException();	
	}

    public function get_last_loan_appl($cust_id, $loan_appl_date){
       return DB::selectOne("/*$this->api_req_id*/ select status, loan_appl_doc_id from loan_applications where cust_id=? and country_code = ? and loan_appl_date <= ? order by id desc limit 1", [$cust_id, $this->country_code, $loan_appl_date]);
    }

    public function get_last_loan_appl_by_purpose($cust_id, $loan_appl_date, $purpose){
       return DB::connection('mysql::write')->selectOne("/*$this->api_req_id*/ select status, loan_appl_doc_id from loan_applications where cust_id=? and country_code = ? and loan_appl_date <= ? and loan_purpose = ? order by id desc limit 1", [$cust_id, $this->country_code, $loan_appl_date, $purpose]);
    }

    public function validate_credit_score($data){
        $product_id = $data['product_id'];
        $cust_id = $data['cust_id'];
        $min_credit_score = DB::selectOne("/*$this->api_req_id*/ select min_credit_score from loan_applications where cust_id=? and product_id= ? and country_code = ? limit 1",[$cust_id, $product_id, $this->country_code]);
        if($min_credit_score){
            return $min_credit_score->min_credit_score;
        }
        return null;

    }

   public function get_loan_appl($loan_appl_doc_id,$fields = ['loan_principal', 'due_amount', 'loan_appl_doc_id', 'loan_appl_date', 'duration', 'flow_fee', 'status'])
   {
       $fields = implode(", ",$fields);
     return DB::selectOne("/*$this->api_req_id*/ select {$fields} from loan_applications where loan_appl_doc_id = ? limit 1",[$loan_appl_doc_id]);
   }

   public function get_loan_appl_by_status($cust_id, $loan_appl_status, $acc_purpose){
	$today_date = Carbon::now();

    return DB::select("select loan_appl_doc_id, acc_prvdr_code from loan_applications where cust_id = ? and country_code = ? and loan_appl_date <= ? and loan_purpose = ? and status = ?", [$cust_id, session('country_code'), $today_date, $acc_purpose, $loan_appl_status]);
   }
   public function get_os_loan_appln($rm_id, $loan_appl_status, $acc_purpose = 'float_advance'){
	$today_date = Carbon::now();
	return collect(DB::select("select loan_appl_doc_id from loan_applications where loan_approver_id = ? and loan_appl_date <= ? and loan_purpose = ? and status = ?", [$rm_id, $today_date, $acc_purpose, $loan_appl_status]))->pluck('loan_appl_doc_id')->toArray();
   }
    

/*
    public function loan_appl_search($field_names, $field_values, $fields_arr)
    {

    	$sql_condition = "";
    	
    	$sub_frm_index = array_search("submitted_from", $field_names);
    	$sub_to_index = array_search("submitted_to", $field_names);
    	if(is_int($sub_frm_index)){
    		//dd($field_values);	
    		$sub_frm_value = $field_values[$sub_frm_index];
    		
    		$sql_condition .= "and loan_appl_date >= '$sub_frm_value 00:00:00'";
    		unset($field_names[$sub_frm_index]);
    		unset($field_values[$sub_frm_index]);
    		
    	}
    	if(is_int($sub_to_index)){
    		$sub_to_value = $field_values[$sub_to_index];
    		$sql_condition .= "and loan_appl_date <= '$sub_to_value 23:59:59'";
    		unset($field_names[$sub_to_index]);
    		unset($field_values[$sub_to_index]);
    		
    	}
     
     	if(sizeof($field_names) == 0){
			$sql_condition = ltrim($sql_condition, "and");
     	}

       return parent::get_records_by_many($field_names, $field_values, $fields_arr, "and ",  $sql_condition);
    
    }*/
}

 
