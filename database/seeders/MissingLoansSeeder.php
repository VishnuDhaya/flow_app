<?php ini_set('memory_limit','1000M'); ?>
<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use \PhpOffice\PhpSpreadsheet\Shared;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Services\LoanApplicationService;
use App\Services\BorrowerService;
use App\Services\LoanService;
use App\Repositories\SQL\CommonRepositorySQL;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Repositories\SQL\BorrowerRepositorySQL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class ExcelImport implements ToCollection, WithCalculatedFormulas{

	public function setRowIds($row_ids){
		$this->row_ids = $row_ids;
	}
	public function collection(Collection $rows){


		$row_ids = $this->row_ids;
		$this->missing_custs = [];
		$this->skipped_row_ids = [];
		$this->missing_acc_custs = [];
		$this->outstanding_custs = [];
		$this->loan_list = [];
	    $record_arr = array();
	    $prods_data = $this->get_prods_data();
	    $this->lender_accounts = $this->get_lender_accounts();

		foreach ($rows as $index => $row) {
			$key = $index + 1;
    		if(in_array($key, $row_ids)){
    			$record_arr[$key] = $row;
    		}
    	}
	
    	foreach ($record_arr as $row_id => $record) {
    		$this->import_record($row_id, $record,$prods_data);
    		
    		
    	}	

    	Log::warning("Missing Customers");
    	Log::warning(array_unique($this->missing_custs));

		Log::warning("Missing Account Customers");
        Log::warning(array_unique($this->missing_acc_custs));

        Log::warning("Imported Outstanding Customers");
        Log::warning($this->outstanding_custs);

		Log::warning("Skipped Row IDs");
    	Log::warning($this->skipped_row_ids);

    	Log::warning("Imported Row IDs");
     	Log::warning($this->loan_list);
    }	

	private function import_record($row_id, $xl_record,$prods_data){
		
		$data = array();
		session(['user_id' => 6]);
		$data_prvdr_cust_id = $xl_record[1];
		$status = $xl_record[20];
		
		try{

			DB::beginTransaction();
			session()->put('data_prvdr_code', 'UEZM');
			$data = (new BorrowerService())->get_borrower($data_prvdr_cust_id, false);
			
			if($data == null){
				$this->missing_custs[] = $data_prvdr_cust_id;	
				thrw("Customer Missing : $data_prvdr_cust_id");
			}

			$record_obj = (array) json_decode(json_encode($data));
			if(!array_key_exists('account_id', $record_obj)){
				$this->missing_acc_custs[] = $data_prvdr_cust_id;
				thrw("Account not configured");
			}
	
			$max_loan_amount = $xl_record[7];
			$flow_fee = $xl_record[8];
			$comments = $xl_record[26];
			$txn_id = $xl_record[21];
			// $xl_duration = $this->get_dates_duration($xl_record[10],$xl_record[12]);
			$xl_duration = $xl_record[6];

			$prod_identified = false;
			foreach($prods_data as $prod_data){
				
				if($prod_data->max_loan_amount == $max_loan_amount && $prod_data->flow_fee == $flow_fee && $prod_data->duration == $xl_duration){
					$prod_identified = true;
					$record_obj['product_name'] = $prod_data->product_code;
					$record_obj['flow_fee'] = $prod_data->flow_fee;
					$record_obj['product_id'] = $prod_data->id;
					$record_obj['product_type'] =  $prod_data->product_type;
					$record_obj['loan_principal'] = $prod_data->max_loan_amount;
					$record_obj['due_amount'] = $prod_data->max_loan_amount + $prod_data->flow_fee;
					$record_obj['duration'] = $prod_data->duration;
					$record_obj['flow_fee_type'] = $prod_data->flow_fee_type;
					$record_obj['flow_fee_duration'] = $record_obj['duration']; 

				}
				
			}
			if(!$prod_identified){
					$record_obj['product_name'] = "EM00";
					$record_obj['flow_fee'] = $flow_fee;
					$record_obj['product_id'] = 999;
					$record_obj['product_type'] =  "Flat";
					$record_obj['loan_principal'] = $max_loan_amount;
					$record_obj['due_amount'] = $max_loan_amount + $flow_fee;
					$record_obj['duration'] = $xl_duration;
					$record_obj['flow_fee_type'] = NULL;
					$record_obj['flow_fee_duration'] = $record_obj['duration'];
			}

			$record_obj['loan_approver_id'] = $record_obj['flow_rel_mgr_id'];

			$appl_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($xl_record[10]);
			$appl_date = $appl_date->format('Y-m-d');
			$record_obj['loan_appl_date'] = $appl_date;
			$record_obj['loan_applied_by'] = 6;
			$full_comments = "$status | $comments | $txn_id";
			$comments = substr($full_comments,0,246);
			$cust_acc_id = $this->get_cust_acc_id($record_obj['cust_id']);
			$record_obj['cust_acc_id'] = $cust_acc_id->id;
			$record_obj['currency_code'] = "UGX";
			$record_obj['customer_consent_rcvd'] = 1;
			$record_obj['created_at'] = date('Y-m-d');
			$record_obj['loan_appl_doc_id'] = "APPL-".$xl_record[0];

			Log::warning($record_obj);
			$loan_appl = (new LoanApplicationService())->apply_loan($record_obj, false);

		
			Log::warning("$$$$$$$$$$$$$$ MISSING TXNS : APPLIED");
			$loan = $this->approve_record($record_obj,$loan_appl);

			Log::warning("$$$$$$$$$$$$$$ MISSING TXNS : APPROVED");
			$disburse = $this->disburse_record($record_obj,$loan, $xl_record);

			if($status != "Outstanding" && $status != "Overdue"){
				Log::warning("$$$$$$$$$$$$$$ MISSING TXNS : DISBURSED");
			 	$disburse = $this->repay_record($record_obj,$loan, $xl_record,$row_id,$comments);
			}
			Log::warning("$$$$$$$$$$$$$$ MISSING TXNS : REPAID");
			DB::commit();
			$this->loan_list[$row_id] =  $loan['loan']->loan_doc_id;

		}
		catch(Exception $ex){
			DB::rollback();
			$this->skipped_row_ids[$row_id] = $data_prvdr_cust_id . ' : '. $ex->getMessage();
			Log::warning($ex->getMessage());
			Log::warning($ex->getTraceAsString());
		}
	}

	private function get_prods_data(){

		$prods_data = DB::select("select id,product_code,duration,product_type,max_loan_amount,flow_fee,flow_fee_type from loan_products where data_prvdr_code='UEZM'");
		return $prods_data;
	}


	private function get_dates_duration($disbursal_date,$due_date){

		$disbursal_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($disbursal_date);
		$disbursal_date = $disbursal_date->format('Y-m-d');
		$due_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($due_date);
		$due_date = $due_date->format('Y-m-d');
		$disbursal_date = Carbon::parse($disbursal_date);
		$due_date = Carbon::parse($due_date);
		$duration = $disbursal_date->diffInDays($due_date);
		return $duration;

	}

	public function get_cust_acc_id($cust_id){
		return $cust_acc_id = DB::selectone("select id from accounts where cust_id='$cust_id' and is_primary_acc=true");
	}

	public function get_lender_accounts(){
		$bank_accounts = DB::select("select id,acc_number from accounts where lender_code='UFLW' and acc_purpose ='repayment'");
		$bank_acc_arr = [];
		foreach ($bank_accounts as $account) {

			if($account->acc_number == "1063626247612"){
				$bank_acc_arr['DFCU Operational Account'] = $account->id;
				

			}
			if($account->acc_number == "01063616833446"){
				$bank_acc_arr['DFCU Float Account'] = $account->id;
			}
			if($account->acc_number == "215010"){
				$bank_acc_arr['momo pay'] = $account->id;
			}

		}
		$bank_acc_arr['EzeeMoney Wallet'] = 3;

		return $bank_acc_arr;
	} 

	private function approve_record($record_obj,$loan_appl){


		$record_obj['loan_appl_doc_id'] = $loan_appl->loan_appl_doc_id;
		$record_obj['action'] = "approve";
		$record_obj['credit_score'] = 1000 ;
		$record_obj['status'] = null;
		$record_obj['loan_apprvd_date'] = $record_obj['loan_appl_date'];
		$loan = (new LoanApplicationService())->approval($record_obj, false);
		return $loan;	
	}

	private function disburse_record($record_obj,$loan, $xl_record){

		$loan = $loan['loan'];
		$disbursal_req = array();
		$disbursal_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($xl_record[10]);
		$due_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($xl_record[12]);
		$disbursal_date = $disbursal_date->format('Y-m-d');
		$disbursal_req['due_date'] = $due_date->format('Y-m-d');
		$disbursal_req['amount'] = $record_obj['loan_principal'];
		$disbursal_req['loan_doc_id'] = $loan->loan_doc_id;
		$disbursal_req['from_ac_id'] = 3 ;
		$disbursal_req['to_ac_id'] = $record_obj['account_id'];
		$disbursal_req['txn_date'] = $disbursal_date; 
		$disbursal_req['txn_exec_by'] = $record_obj['flow_rel_mgr_id'];
		$disbursal_req['send_sms'] = false;
		$disbursal_req['txn_mode'] = 'data_provider_portal';
		$disbursal_req['created_by'] = 6;
		$disbursal_req['cust_comm'] = 25000;	

		
		$loan = (new LoanService())->disburse($disbursal_req, false, true);

	}

	private function repay_record($record_obj,$loan, $xl_record,$row_id,$comments){

		$loan = $loan['loan'];
		$payment_req = array();
		$paid_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($xl_record[16]);
		$paid_date = $paid_date->format('Y-m-d');
		$payment_req['amount'] = $xl_record[9]; 
		$payment_req['loan_doc_id'] = $loan->loan_doc_id;
		$bank_name = trim($xl_record[25]);
		if($bank_name == 'EzeeMoney Wallet 422k, 600k momo pay' || $bank_name == 'Multiple accounts'){
			$bank_name = 'DFCU Float Account';
		}
		$to_ac_id = $this->lender_accounts[$bank_name];
		$payment_req['to_ac_id'] = $to_ac_id;
		$payment_req['txn_date'] = $paid_date;
		$payment_req['txn_exec_by'] = $record_obj['flow_rel_mgr_id'];
		$payment_req['send_sms'] = false;
		$payment_req['is_part_payment'] = false;
		$payment_req['waive_penalty'] = false;
		$payment_req['txn_mode'] = 'data_provider_portal';
		$payment_req['ref_row_id'] = $row_id;
		$payment_req['gs_comments'] = $comments;
		$payment_req['updated_by'] = 6;
		$payment_req['paid_date'] = $paid_date;
		$status = $xl_record[20];
		
		if ($status == 'Paid back late'){
			$payment_req['penalty_collected'] = $xl_record[18];
		}else if ($status == 'Paid back on time'){
			$total_amount = (int)$payment_req['amount'];
			if ($xl_record[18] ==''){
				$extra_amount = 0;				
			}else{ 
				$extra_amount = (int)$xl_record[18];
			}
			$payment_req['amount'] = $total_amount + $extra_amount;
		}

		if ($paid_date <= "2019-10-31"){
			$payment_req['repay_comm'] = 500;

		}else{
			$payment_req['repay_comm'] = 1750;			
		}

		//if($to_ac_id == "1361" || $to_ac_id == "1362"){
		if($to_ac_id != "3"){
			//$payment_req['fwd_to_float_ac'] = true;
			$fwd_to_float_ac = true;
			$payment_req['fwd_to_float_ac_id'] = 3;	
		}else{
			//$payment_req['fwd_to_float_ac'] = false;
			$fwd_to_float_ac = false;
		}
		

		$loan = (new LoanService())->capture_repayment($payment_req, false,$fwd_to_float_ac);

	}
}

class MissingLoansSeeder extends Seeder
{
    public function run(){ 
    	DB::statement("SET SESSION wait_timeout = 75");
    	session()->put('country_code', 'UGA');
    	session()->put('google_sheet_import',true);
		$path = 'ezee_money_22.xlsx';

		$row_ids = array();
		foreach (range(1,7880) as $number) {
      		array_push($row_ids,$number);
   		}

	   $import = new ExcelImport();
	   $import->setRowIds($row_ids);
	   $data = Excel::import($import, $path);


	}
}
