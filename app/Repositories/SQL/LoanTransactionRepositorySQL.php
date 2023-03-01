<?php
 namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;

use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\LoanTransaction;
use App\Services\AccountService;
use App\Consts;
use Carbon\Carbon;
use Log;

class LoanTransactionRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{
	
	public function __construct()
  	{
      parent::__construct();

    }
	
	public function model(){
			return LoanTransaction::class;
	}

	public function create(array $loan_txn, $create_event = false, $event_type = null , $event_data=null)
 	{	  
 	 
 		$loan_txns_id = parent::insert_model($loan_txn); 
 		if($create_event){
 			if($event_type == null){
 				$event_type = $loan_txn['txn_type'];
 			}
 			// $loan_event_repo = new LoanEventRepositorySQL();    
 			Log::warning('$$$$$$$$$$$loan_txn');
 			Log::warning($loan_txn);
 			$txn_date = array_key_exists('txn_date', $loan_txn) ? $loan_txn['txn_date'] : datetime_db();
 			
	 		// $result = $loan_event_repo->create_event($loan_txn['loan_doc_id'], $event_type, $event_data, $loan_txns_id, $txn_date);
 		}
 		return $loan_txns_id;
 	}
 
 	public function get_penalty_loans(){
 		$today = Carbon::now()->endOfDay();
 		return DB::table(DB::raw("loan_txns /*$this->api_req_id*/"))->whereRaw("txn_type = 'penalty' and txn_date = ?", [$today])->pluck('loan_doc_id');
 		
 	}

 	public function get_prov_penalty($loan_doc_id){
 		return DB::selectOne("/*$this->api_req_id*/ select amount from loan_txns where txn_type= ? and loan_doc_id = ? limit 1", ['provisional_penalty', $loan_doc_id]);
 	}

 	public function get_payment_date($loan_doc_id){
 		$result = DB::selectOne("/*$this->api_req_id*/ select txn_date from loan_txns where txn_type = ? and loan_doc_id = ? limit 1",['payment', $loan_doc_id]);
 		if($result){
            return $result->txn_date;
        }
        return null;
 	}

 	public function update(array $req){
 	    throw new BadMethodCallException();	
	}
	public function delete($id){
		throw new BadMethodCallException();	
	}

	public function list(array $data){
		$loan_txns = DB::table(DB::raw("loan_txns /*$this->api_req_id*/ "))->whereRaw("loan_doc_id = ?", [$data['loan_doc_id']])->get();
		if(sizeof($loan_txns) > 0){
			$acc_ids = array();
			foreach($loan_txns as $loan_txn){
				if(!empty($loan_txn->from_ac_id)){
					$acc_ids[] = $loan_txn->from_ac_id;
				}
				if(!empty($loan_txn->to_ac_id)){
					$acc_ids[] = $loan_txn->to_ac_id;	
				}
				$distinct_acc_ids = array_unique($acc_ids);
			}	
		
			$acc_serv = new AccountRepositorySQL();
			$account_map = $acc_serv->get_accounts_by_id($distinct_acc_ids);
			foreach($loan_txns as $loan_txn){
				if($loan_txn->from_ac_id){
					$loan_txn->from_ac_text = $account_map[$loan_txn->from_ac_id];
				}
				if($loan_txn->to_ac_id){
					$loan_txn->to_ac_text = $account_map[$loan_txn->to_ac_id];
				}
				if($loan_txn->txn_exec_by != 0){
					$person = (new PersonRepositorySQL)->get_record_by('id', $loan_txn->txn_exec_by);
					if($person){
						$loan_txn->txn_exec_by = (new PersonRepositorySQL)->get_first_name($loan_txn->txn_exec_by);
					}
					
				}else{
					$loan_txn->txn_exec_by = 'System';
				}
			}
			return $loan_txns;
		}else{
			return [];
		}

	}

	public function get_loan_doc_id($txn_id){
		return DB::select("/*$this->api_req_id*/ select loan_doc_id, reversed_date from loan_txns where txn_id = ? ", [$txn_id]);
		
	}

	public function get_last_n_months_txn($loan_doc_id, $n_months, $prov_year){
		$loan_prov_date =Carbon::parse("$prov_year-12-31 23:59:59");
		$last_n_months_date = Carbon::parse("$prov_year-12-31 23:59:59")->subMonths($n_months);
		
		return DB::select("select id from loan_txns where txn_type = 'payment' and loan_doc_id = ? and txn_date between  ? and  ? ", [$loan_doc_id, $last_n_months_date, $loan_prov_date]);
	}

	public function get_txns_by_year($loan_doc_id, $year){
		// $dateTime = Carbon::createFromDateTime($year, 12, 31)->startOfYear(); // $year-12-31 23:59:59
		$today_date = Carbon::now();
		$loan_prov_date = "$year-12-31 23:59:59";
		return DB::select("select amount from loan_txns where txn_type = 'payment' and loan_doc_id = ? and txn_date between  ? and  ? ", [$loan_doc_id, $loan_prov_date, $today_date]);
	}

    public function get_payments($loan_doc_id,array $fields = ['amount','txn_id','txn_date']){
        $fields = implode(",",$fields);
        $payments  = DB::select("select {$fields} from loan_txns where loan_doc_id = ? and txn_type = ? order by id desc",[$loan_doc_id,'payment']);

        return $payments;
    }
}

 
