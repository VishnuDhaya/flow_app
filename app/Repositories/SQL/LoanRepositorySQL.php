<?php
 
 namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use App\Services\LoanApplicationService;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\Loan;
use App\Consts;
use Illuminate\Support\Facades\Log;
use App\Exceptions\FlowCustomException;
use Carbon\Carbon;

class LoanRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{
	
  public function __construct()
  {
      parent::__construct();

  }
	public function model(){
			return Loan::class;
	}
	public function create(array $loan)
 	{	
 		$loan_id = parent::insert_model($loan); 
 		return $loan_id;
 	}
  public function update(array $loan){
  return parent::update_model($loan);
  }

	public function delete($id){
		throw new BadMethodCallException();	

	}
    public function list($id){
    throw new BadMethodCallException(); 
  }
	  public function loan_search($field_names, $field_values, $fields_arr)
    {
       return parent::get_records_by_many($field_names, $field_values, $fields_arr);
    }

   public function get_last_loan($cust_id, $loan_appl_date){
    return DB::selectOne("/*$this->api_req_id*/ select status, loan_doc_id, loan_approver_id, loan_purpose from loans where cust_id=? and country_code = ? and loan_appl_date <= ? order by id desc limit 1", [$cust_id, $this->country_code, $loan_appl_date]);
   }

   public function get_last_loan_by_purpose($cust_id, $loan_appl_date, $purpose){
    return DB::connection('mysql::write')->selectOne("/*$this->api_req_id*/ select status, loan_doc_id, loan_approver_id, loan_purpose, acc_prvdr_code  from loans where cust_id=? and country_code = ? and loan_appl_date <= ? and loan_purpose = ? and status != 'voided' order by id desc limit 1", [$cust_id, $this->country_code, $loan_appl_date, $purpose]);
   }

   public function get_unsettled_loan($cust_id){
    return DB::selectOne("/*$this->api_req_id*/ select loan_doc_id from loans where cust_id= ? and country_code = ? and status in ('ongoing','due') order by id desc limit 1", [$cust_id, $this->country_code]);
   }
   public function get_due_loans($date){
      //$date = "2019-05-29 23:59:59";
      return DB::select("/*$this->api_req_id*/ select due_date, cust_id, cust_name, cust_mobile_num, current_os_amount, currency_code from loans where due_date = ? and country_code = ? and current_os_amount > 0", [$date, $this->country_code]);
   }
   public function get_loans($loan_doc_ids){
    //$today = Carbon::now()->endOfDay();
    return DB::table(DB::raw("loans /*this->api_req_id*/ "))->where('country_code',$this->country_code)->whereIn( 'loan_doc_id', $loan_doc_ids)->select('flow_rel_mgr_id', 'due_date','cust_id','cust_name', 'cust_mobile_num', 'paid_amount',DB::raw('(due_amount - paid_amount) as balance_amount'),'due_amount', 'current_os_amount', 'currency_code')->get();
    //DB::select("select due_date,cust_id,cust_mobile_num,(due_amount - paid_amount) as due_amount, current_os_amount from loans where loan_doc_id in (?)", [$loan_doc_ids]);
   }
   
   public function get_outstanding_loan($ongoing_loan_doc_id,$fields = ['status','due_amount','paid_amount','paid_date','cust_acc_id', 'currency_code','loan_principal', 'duration','loan_doc_id', 'loan_appl_id', 'product_id', 'provisional_penalty', 'data_prvdr_code', 'lender_code', 'loan_appl_date', 'status', 'date(due_date) as due_date', 'flow_fee', 'current_os_amount', 'date(disbursal_date) as disbursal_date'])
   {
       $fields = implode(", ",$fields);
     return DB::selectOne("/*$this->api_req_id*/ select {$fields} from loans where loan_doc_id = ? limit 1",[$ongoing_loan_doc_id]);
   }
   
   public function get_loan($loan_doc_id)
   {
     $loan =  DB::selectOne("/*$this->api_req_id*/ select loan_doc_id, status, country_code, lender_code, data_prvdr_code, cust_id from loans where loan_doc_id = ? limit 1",[$loan_doc_id]);
      //Log::warning('$loan-------');
      //Log::warning($loan);
      return $loan;
  }
   
   
   public function get_ongoing_cust_id(){
    $ongoing_loan = DB::select("/*$this->api_req_id*/ select cust_id from loans where status = 'ongoing' and country_code = ? ", [$this->country_code]);

    if (!sizeof($ongoing_loan) > 0){
      return 'id is null';
    }
    $cust_id = 'cust_id in (';
    foreach ($ongoing_loan as $value) {
      $customer_id = $value->cust_id;
      $cust_id = $cust_id."'$customer_id'".',';
    }
   
    $condition =rtrim($cust_id, ',');

    $cust_id = $condition.')';
    return $cust_id;

   }

  
  public function get_os_loan_doc_id_by($field, $value, $stmt_txn_date, $loan_purpose = null, $op = ' = '){
    $params = Consts::DISBURSED_LOAN_STATUS;    
    $place_holders = array_fill(0, count($params) , '?');
    $place_holders = implode(', ', $place_holders);
    array_push($params,$value,$value,$stmt_txn_date);

    $addt_sql= '';
    if($loan_purpose != null){
      $addt_sql = " and loan_purpose ='$loan_purpose' ";
    }

    return collect(DB::select("select acc_prvdr_code, loan_doc_id, loan_principal from loans where status in ({$place_holders}) and ($field $op ? or data_prvdr_cust_id = ?) and disbursal_date < ? $addt_sql", $params))->toArray();
    
    // $params[] = $value;
    // return collect(DB::select("select loan_doc_id from loans where status in ({$place_holders}) and $field $op ?", $params))->pluck('loan_doc_id')->toArray();
  }

//    public function get_os_loan($loan_doc_id){
//     $status = Consts::DISBURSED_LOAN_STATUS;

//     $loan_doc_id = DB::select("select loan_doc_id from loans where status = ?",[$status]);
//     return $loan_doc_id;
//  }

    public function get_loan_txn_data($txn_id){
		  return DB::selectOne("/*$this->api_req_id*/ select loan_doc_id, amount, txn_date, to_ac_id, txn_id, txn_mode, txn_exec_by  from loan_txns where txn_id = ? ", [$txn_id]);
	  }
  public function get_os_loan($loan_doc_id){
    $params = Consts::DISBURSED_LOAN_STATUS;
    
    $place_holders = array_fill(0, count($params) , '?');
    $place_holders = implode(', ', $place_holders);
    $params[] = $loan_doc_id;
    return collect(DB::select("select * from loans where status in ({$place_holders}) and loan_doc_id = ?",$params))->pluck('*');
  }

  public function get_last_n_loans($cust_id, $perf_eff_date, $n){
    return collect(DB::select("select loans.loan_approved_date, loans.approver_role 
    from loans inner join loan_products 
    on loans.product_id = loan_products.id where cust_id = ? 
    and loan_products.product_type !='probation' and loans.loan_approved_date >= ? 
    order by disbursal_date desc limit {$n}",
    [$cust_id, $perf_eff_date]));
    // ->pluck('loan_approved_date');
  }

  public function update_loan_event($event_type, $loan_doc_id, $time = null){
    if($time == null){
          $time =  datetime_db();
     }
    $this->update_json_arr_by_code('loan_event_time', [$event_type => $time], $loan_doc_id);
  }

  public function get_loan_by_status($cust_id, $status, $acc_purpose){
    $today_date = Carbon::now();
    $status_csv = csv($status);

    return DB::select("select loan_doc_id, acc_prvdr_code from loans where cust_id = ? and country_code = ? and loan_appl_date <= ? and loan_purpose = ? and status in ($status_csv)", [$cust_id, session('country_code'), $today_date, $acc_purpose]);
  }

  public function get_loan_by_not_in_status($cust_id, $status, $acc_purpose){
    $today_date = Carbon::now();
    $status_csv = csv($status);

    return DB::select("select loan_doc_id, acc_prvdr_code from loans where cust_id = ? and country_code = ? and loan_appl_date <= ? and loan_purpose = ? and status not in ($status_csv)", [$cust_id, session('country_code'), $today_date, $acc_purpose]);
  }

}

 
