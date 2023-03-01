<?php
namespace App\Repositories\SQL;

use Illuminate\Database\QueryException;
use App\Exceptions\FlowCustomException;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\AccountTxn;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\LenderRepositorySQL;
use App\Services\AccountService;

class AccountRepositorySQL  extends BaseRepositorySQL implements BaseRepositoryInterface
{

	public function __construct(){
            parent::__construct();
            $this->class = Account::class;

    }
        
    public function model(){        
        return $this->class;
    }


	/*public function create(array $account , $txn = true){
   	try
        {
        	if($txn){
        		DB::beginTransaction(); 
        	}
		if($account['is_primary_acc'] == true)
		{
			$updateByColumn = null;
			$updateByValue = null;
			$this->load_update_fields($account, $updateByColumn, $updateByValue);
			$this->make_accounts_regular($updateByColumn,$updateByValue);
		}
        $acc_repo = new AccProviderRepositorySQL();
        $account_provider_name = $acc_repo->get_acc_prvdr_name($account['acc_prvdr_code']);
        $account['acc_prvdr_name'] = $account_provider_name->name;
		$account_id = parent::insert_model($account);
			if($txn){
		   		DB::commit();
		   	}	
	    }
	    catch (\Exception $e) {
		    if($txn){	
				DB::rollback();
			}	
			Log::warning($e->getTraceAsString());
			thrw($e->getMessage());
		}
	   return $account_id;
	}*/

	public function create(array $account){
		
		return parent::insert_model($account);
	}

	
	public function delete($id){
		throw new BadMethodCallException();
	}
	public function update_balance($ac_id, $txn_amt){
		return parent::increment('balance', $ac_id, "id", $txn_amt);
		
	}
	public function list($data){
		$field_names = array_keys($data);
		$field_values = array_values($data);
		$fields_arr = ["alt_acc_num","acc_prvdr_name", "acc_purpose", "acc_prvdr_code","type", "holder_name", "acc_number", "branch","status","is_primary_acc", 'balance', "tp_acc_owner_id"];
		return parent::get_records_by_many($field_names, $field_values, $fields_arr);
	}

	public function get_acc_num($acc_id){
		$acc = parent::find($acc_id, ['acc_number']);
		return $acc->acc_number;
	}


	public function get_accounts_by(array $field_names,array $field_values, $fields_arr= ["acc_prvdr_name","acc_prvdr_code", "acc_purpose", 'type', 'acc_number', 'is_primary_acc', 'balance'], $repay_acc = false , $addl_sql = "")
	{
		$common_accounts = array();
		$key = array_search('acc_purpose', $field_names);
		if($key !== false){
			$addl_sql .= " and JSON_CONTAINS(acc_purpose, JSON_ARRAY('{$field_values[$key]}'))";
			unset($field_names[$key], $field_values[$key]);
		}
		
		$accounts = parent::get_records_by_many(array_values($field_names), array_values($field_values), $fields_arr, "and", $addl_sql);

		if($repay_acc){
			$addl_sql .= " and JSON_CONTAINS(acc_purpose, JSON_ARRAY('repayment'))";
			$common_accounts = parent::get_records_by_many(['network_prvdr_code', "status" ], ['*', "enabled"], $fields_arr, "and", $addl_sql);
		}
		
		return array_merge($accounts, $common_accounts);
	}


	public function get_account_by(array $field_names,array $field_values, $fields_arr= ["acc_prvdr_name","acc_prvdr_code", "acc_purpose", 'type', 'acc_number', 'is_primary_acc', 'balance'])
	{

		$addl_sql = "";

		$key = array_search('acc_purpose', $field_names);
		if($key !== false){
			$addl_sql = "and JSON_CONTAINS(acc_purpose, JSON_ARRAY('{$field_values[$key]}'))";
			unset($field_names[$key], $field_values[$key]);
		}
		
		return parent::get_record_by_many(array_values($field_names), array_values($field_values), $fields_arr, "and", $addl_sql);

	}


	public function update_account_status(array $account)
	{
	   return parent::update_record_status($account['status'], $account['id']);
	}

	public function update(array $data){
		$result = parent::update_model($data);
		return $result;
	}



    public function make_accounts_regular($updateByColumn,$updateByValue)
    {
    	$primary_account = DB::update("/*$this->api_req_id*/ UPDATE accounts set is_primary_acc= ?, status ='disabled' where $updateByColumn = ? and is_primary_acc=? and country_code = ?",[false, $updateByValue,true, $this->country_code]);
    	return $primary_account;
    }


   /* public function load_update_fields($account , &$updateByColumn, &$updateByValue)
    {
          if(isset($account['cust_id']))
          {
          	$updateByColumn = "cust_id";
		    $updateByValue = $account['cust_id'];
          }
         else if(isset($account['lender_code']))
          {
          	$updateByColumn = "lender_code";
		    $updateByValue = $account['lender_code'];
          }
          else if(isset($account['data_prvdr_code']))
          {
          	$updateByColumn = "data_prvdr_code";
		    $updateByValue = $account['data_prvdr_code'];
          }
          else
		  {
		    throw new FlowCustomException("lender_code or cust_id or data_prvdr_code not specified");
		  }
        $result = array($updateByColumn,$updateByValue);
        return $result;
  }  */


public function get_accounts_by_id(array $id){
	$acc_details = DB::table(DB::raw("accounts /*$this->api_req_id*/ "))->select('id','acc_number','acc_prvdr_name', 'acc_prvdr_code', 'balance', 'type','is_primary_acc', 'acc_purpose')->whereIn('id', $id)->get();
	$acc_serv = new AccountService();
	$acc_map = array();
	foreach($acc_details as $acc_detail){
        $acc_detail->acc_purpose = json_decode($acc_detail->acc_purpose);
		$acc_map[$acc_detail->id] = $acc_serv->get_acc_txt($acc_detail);
	}
	return $acc_map;
}

/*public function get_ref_accounts($data){
	
	//$field_names = ["country_code"];
	//$field_values = [$data['country_code']];
	if(array_key_exists("lender_code", $data)){
		$field_names[] = "lender_code";
		$field_values[] = $data['lender_code'];
	}else if(array_key_exists("data_prvdr_code", $data)){
		$field_names[] = "data_prvdr_code";
		$field_values[] = $data['data_prvdr_code'];
	}
	
	
	$fields_arr = ["acc_prvdr_name", "acc_prvdr_code","acc_purpose", "type", "holder_name", "acc_number", "branch","status","is_primary_acc", 'balance'];
	$addl_condition = "and id != {$data['acc_id']}";
	$accounts = parent::get_records_by_many($field_names, $field_values, $fields_arr, "and",$addl_condition);
	$ref_acc = array();
	foreach($accounts as $account){
		$ref_acc[] =["id" => $account->id, "name" => $this->get_acc_txt($account)] ;
	}
	return $ref_acc;

	}

	

	private function get_acc_txt($acc_detail){
		$acc_prvdr_code = $acc_detail->acc_prvdr_code;
		$acc_purpose = $acc_detail->acc_purpose;
		$account_number = $acc_detail->acc_number;
		$balance = $acc_detail->balance;
		$type = $acc_detail->type;
		if(!$acc_purpose){
			$acc_purpose = $type;
		}
		$star = null; 
		if($acc_detail->is_primary_acc == 1){
			$star = "*";
		}
		if($balance){
			//$account_txt = "$account_number BAL : $balance ($acc_prvdr_code - $type) $star";
			$account_txt = "$account_number ($acc_prvdr_code - $acc_purpose) $star";		
		}else{
			$account_txt = "$account_number ($acc_prvdr_code - $acc_purpose) $star";		
		}
		return $account_txt;
	}	*/

	// public function check_missed_txn($acc_id,  $txn_date){
	// 	$this->class = AccountTxn::class;
	// 	$result = DB::selectOne("select 1 from account_txns where acc_id = ? and country_code = ? and txn_date >= ? limit 1", [$acc_id, $this->country_code, $txn_date]);
		
	// 	return $result ? true : false;
		

	// }	

	public function get_previous_txn_bal($acc_id,  $txn_date, $is_missed_txn){
		$txn_bal = $this->get_latest_txn_bal($acc_id, $txn_date);
		//$this->class = AccountTxn::class;
		//$result = DB::selectOne("select balance from account_txns where acc_id = ? and country_code = ? and txn_date <= ? order by txn_date desc, id desc limit 1", [$acc_id, $this->country_code, $txn_date]);
		
		//$txn_bal = $result ? $result['balance'] : 0;
		//Log::warning($txn_bal);

		if($is_missed_txn){
			return $txn_bal;
		}else{
			$this->class = Account::class;
			$result = parent::get_record_by('id', $acc_id, ['balance']);

			$current_bal = $result ? $result->balance : 0;
   
			if($current_bal == $txn_bal){
				return $txn_bal;
			}else{
				//return $txn_bal;
				thrw("A/C Balance not in sync for Account ID : " . $acc_id);
			}
		}
		
	}

	// public function get_latest_txn_bal($acc_id, $txn_date){
		
	// 	$this->class = AccountTxn::class;
	// 	$result = DB::selectOne("select balance from account_txns where acc_id = ? and country_code = ? and txn_date <= ? order by txn_date desc, id desc limit 1", [$acc_id, $this->country_code, $txn_date]);
		
	// 	return $result? $result->balance : 0;
		
	// }

	// public function get_last_txn_bal($acc_id){
		
	// 	$this->class = AccountTxn::class;
	// 	$result = DB::selectOne("select balance from account_txns where acc_id = ? and country_code = ? order by txn_date desc, id desc limit 1", [$acc_id, $this->country_code]);
		
	// 	return $result? $result->balance : 0;
		
	// }
	
    // public function get_acc_txns($data)
    // {
	
    //        if(isset($data['acc_txn_type']) && $data['acc_txn_type'])
    //        {
    //        	   return DB::table('account_txns')->where('txn_date','>=',$data['start_date'])->where('txn_date','<=',$data['end_date'])->where('acc_txn_type',$data['acc_txn_type'])->where('country_code',$this->country_code)->where('acc_id',$data['acc_id'])->get();
    //        } 
    //        else
    //        {
    //        	 return DB::table('account_txns')->where('txn_date','>=',$data['start_date'])->where('txn_date','<=',$data['end_date'])->where('country_code',$this->country_code)->where('acc_id',$data['acc_id'])->get();
    //        }  
  	// }
    public function getCustomerAccount($cust_id)
    {
    	   return DB::selectOne("/*$this->api_req_id*/ select * from accounts where cust_id = ? limit 1",[$cust_id]);
    }


    public function get_recon_accounts($fields){
           return $this->get_records_by_many(['to_recon','status'], [true,'enabled'], $fields);
    }

	public function get_accts_no_require_approval($cust_id){
		$acc_prvdrs_allow_approval = config('app.acc_prvdrs_allow_approval'); // UMTN
		$acc_prvdrs_csv = csv($acc_prvdrs_allow_approval);
		$exist_accounts = DB::select("select id from accounts where cust_id = ? and acc_prvdr_code not in ($acc_prvdrs_csv)", [$cust_id]);
		return $exist_accounts;
	}

	public function get_fa_accounts($cust_id){
        return $this->get_accounts_by(['cust_id','acc_purpose','status'],[$cust_id, 'float_advance', 'enabled'], ['*']);
	}

	public function get_cust_id_by_account($acc_number, $acc_prvdr_code, $status='enabled') {
		$field_names = ['acc_number', 'acc_prvdr_code'];
		$field_values = [$acc_number, $acc_prvdr_code];
		if($status != '*') {
			$field_names[] = 'status';
			$field_values[] = $status;
		}

		$account = $this->get_account_by($field_names, $field_values, ['cust_id']);
		return $account->cust_id;
	}
	

	public function get_cust_id_by_tp_acc($check){

		if($check == 'with_tp_acc'){
			$add_cond = "tp_acc_owner_id is not null";
		}
		elseif($check == 'without_tp_acc'){
			$add_cond = "tp_acc_owner_id is null and cust_id is not null";
		}
		$cust_ids = DB::select("select cust_id from accounts where {$add_cond} and status = 'enabled'");

		if (!sizeof($cust_ids) > 0){
			return 'id is null';
		}
		$cust_id = 'cust_id in (';
		foreach ($cust_ids as $value) {
		$customer_id = $value->cust_id;
		$cust_id = $cust_id."'$customer_id'".',';
		}

		$condition =rtrim($cust_id, ',');

		$cust_id = $condition.')';
		return $cust_id;
	}


}


