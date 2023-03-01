<?php

namespace App\Services;
use App\Models\CSResultConfig;
use App\Models\CSModelWeightages;
use App\Models\ScoreModel;
use App\Models\CustCSFValues;
use App\Models\ScoreRun;
use App\Repositories\SQL\MasterDataRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Scripts\php\ScoreEligibilityScript;
use Illuminate\Support\Facades\DB;
use Log;
use Exception;
use Illuminate\Database\QueryException;
use App\Exceptions\FlowCustomException;
use App\Models\Account;
use App\Models\CsFactorValues;
use App\Models\CustCommission;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\CustCSFValuesRepositorySQL;
use Carbon\Carbon;

class DataScoreModelService {

    /*public function __construct()
    {
    	 
    	  Log::warning(session('country_code'));
          $this->country_code = "UGA";
          $this->country_code = session('country_code');
    }*/

    public function create_score_model(array $data){

    	if(array_key_exists('model_name', $data) && $data['model_name'] != null){
    		$score_model = new ScoreModel();
			return $score_model->insert_model($data);
    	}else{
    		thrw("Model Name is a required field");
    	}
    	
    }

    public function list(array $data){
		$score_model = new ScoreModel();
		return $score_model->get_records_by_country_code(['model_code', 'model_name']);
    }

    public function create_cs_results(array $data){
		$result_code_size = sizeof($data['score_result_codes']);
		try
        {
        	DB::beginTransaction();
        	DB::table('cs_result_config')->where([['csf_model',$data['csf_model']],['country_code', session('country_code')]])->delete();
			foreach ($data['score_result_codes'] as $i => $scr_res_code) {
				$scr_res_record['country_code'] = $data['country_code'];
				$scr_res_record['csf_model'] = $data['csf_model'];
				$scr_res_record['score_result_code'] = $scr_res_code['code'];

				if($i == 1){
					$scr_res_record['score_from'] = 0;
				}else {
					$prev_row = $i-1;
					$prev_row_scr_to_value = $data['score_details']['score_to_'.$prev_row];
					$scr_res_record['score_from'] = $prev_row_scr_to_value+1;
				}

				if($i == $result_code_size){
					$scr_res_record['score_to'] = 1000;
				}else {
					$scr_res_record['score_to'] = $data['score_details']['score_to_'.$i];
				}

				$result_config_model = new CSResultConfig();
				
				$result_config_model->insert_model($scr_res_record);
			}
			DB::commit();

		}catch (\Exception $e) {

            DB::rollback();  
            Log::warning($e->getTraceAsString());
            if ($e instanceof QueryException){
					throw $e;
				}else{
				thrw($e->getMessage());
				}
        }
	
	}

	public function get_cs_result_config(array $data){
		$result_config_model = new CSResultConfig();
		$result = $result_config_model->get_records_by('csf_model', $data['csf_model'], ['score_result_code','score_from', 'score_to']);
		return $result;
		
	}

	public function create_cs_factor(array $data){

    	if(array_key_exists('csf_type', $data) && $data['csf_type'] != null){
    		$cs_weightage_model = new CSModelWeightages();
			return $cs_weightage_model->insert_model($data);
    	}else{
    		thrw("Factor is a required field");
    	}
    	
    }

    public function list_cs_weightages(array $data, array $select = ['csf_type', 'new_cust_weightage', 'repeat_cust_weightage']){
    	$cs_weightage_model = new CSModelWeightages();
    	return $cs_weightage_model->get_records_by("cs_model_code", $data['cs_model_code'],$select);
    }

    public function update_cs_weightages(array $cs_weightages){
    	    	
    	try
        {
        	DB::beginTransaction();
            
            $score_model = $cs_weightages['score_model'];
	    	foreach ($cs_weightages as $key => $value) {
                
	    		$split_key = explode("_", $key);
	    		$last_str = end($split_key);
	    		$remove_last_el = array_pop($split_key);	
	    		$field_str = implode("_", $split_key);
	    		
	    		$field_name = "csf_type";
	    		$field_value = $field_str;
	    		$update_col_val = $value;

	    		if($last_str == "new"){
	    			$update_col_name = "new_cust_weightage";
	    			
	    		}else if($last_str == "repeat"){
	    			$update_col_name = "repeat_cust_weightage";
	    		}   		

	    		DB::table('cs_model_weightages')
	    					->where($field_name , $field_value)
                            ->where("cs_model_code" , $score_model)
	    					->update([$update_col_name => $update_col_val]);
	    	
	    	}
	    	DB::commit();

		}catch (\Exception $e) {

                DB::rollback();      
                if ($e instanceof QueryException){
					throw $e;
				}else{
				thrw($e->getMessage());
				}
        }

    }

    public function get_filtered_csfs($data){
    	try
        {
        	DB::beginTransaction();
	    	$cs_weightages = $this->list_cs_weightages($data, ['csf_type']);
		    $factor_arr = pluck($cs_weightages, 'csf_type');
	        $master_data_repo = new MasterDataRepositorySQL();
	        $csfs = $master_data_repo->get_filtered_csf_types($factor_arr, $data['country_code']);
	        DB::commit();
	    }catch (\Exception $e) {

            DB::rollback();      
           if ($e instanceof QueryException){
					throw $e;
				}else{
				thrw($e->getMessage());
				}
        }
	        return $csfs;
    }

    public function upload_cust_txns(array $data, $data_prvdr_code)
    {
        $data['data_prvdr_code'] = $data_prvdr_code;
        if(!array_key_exists('cs_model_code', $data)){
            thrw("Credit Score Model is a required field.");
        }
        if(!array_key_exists('cust_txn_file', $data)){
            thrw("Please upload the Transaction file.");
        }
    	
        $existing_customer = false;
    	$cust_id = get_arr_val($data, 'cust_id');
    	$dp_cust_id = get_arr_val($data, 'data_prvdr_cust_id');
        $req_data_prvdr_code = get_arr_val($data, 'data_prvdr_code');

    	$brwr_repo = new BorrowerRepositorySQL();
    	if($cust_id){
            $borrower = $brwr_repo->get_record_by('cust_id', $cust_id, ['data_prvdr_cust_id', 'data_prvdr_code']);
    		if($borrower){
                $existing_customer = true;
                $data['data_prvdr_cust_id'] = $borrower->data_prvdr_cust_id;
                $data['data_prvdr_code'] = $borrower->data_prvdr_code;
            }
    	}else if($dp_cust_id){
            $borrower = $brwr_repo->get_record_by('data_prvdr_cust_id', $dp_cust_id, ['data_prvdr_code','cust_id']);
            
    		if($borrower){
                $existing_customer = true;
                $cust_id = $borrower->cust_id;
                $data['data_prvdr_code'] = $borrower->data_prvdr_code;
            }   		
    	}else{
    		thrw("Please enter any customer ID");
    	}

        if($existing_customer){
            if ($req_data_prvdr_code && $req_data_prvdr_code != $data['data_prvdr_code']){
                thrw("Customer does not belong to $req_data_prvdr_code");
            } 
        }else if($req_data_prvdr_code == null){
            thrw('Please choose a data provider');
        }

    	$file_path = separate([flow_file_path(), get_file_rel_path(null, "cust_txn_file", null),$data['cust_txn_file']]);    	
    	$result = $this->get_cust_factor_values($data['country_code'], $file_path, $data['data_prvdr_cust_id'], $data['cs_model_code'], $data['data_prvdr_code']);
        $result['existing_customer'] = $existing_customer;
        if($cust_id){
            $result['cust_id'] = $cust_id;
        }

        return $result;

    	
    }



    public function get_cust_factor_values($country_code, $txn_file_path, $dp_cust_id, $model_code, $data_prvdr_code, $purge_old_txns = 'false'){
    	
    	$load_result = $this->load_cust_txns($country_code, $txn_file_path, $dp_cust_id, $data_prvdr_code, $purge_old_txns);
    	$run_id = $load_result[1];
    	return $this->process_cust_txns($country_code, $run_id, $dp_cust_id, $model_code, $data_prvdr_code);
    }

    public function load_cust_txns($country_code, $txn_file_path, $dp_cust_id, $data_prvdr_code, $purge_old_txns){
    	
    	$filename = '../app/Scripts/python/etl_script.py';
     
    	$txn_file_path = "'". $txn_file_path. "'";
    	$args = [$txn_file_path, $country_code, $dp_cust_id, $data_prvdr_code, $purge_old_txns];
    	return $this->run_script($filename, $args);
	
    }	

    public function process_cust_txns($country_code, $run_id, $dp_cust_id, $model_code, $data_prvdr_code){

    	$filename = '../app/Scripts/python/score_calc_app.py';
    	$args = [$country_code, $run_id, $dp_cust_id, $data_prvdr_code, 90];
    	$output = $this->run_script($filename, $args);
	
		$keys = explode(",", $output[1]);
		$values = explode(",", $output[2]);
		$factors_arr = array_combine($keys, $values);

		$field_name =  ['csf_type','new_cust_weightage as weightage'];
		$common_repo = new CommonRepositorySQL(CSModelWeightages::class);
    	$weightages = $common_repo->get_records_by('cs_model_code', $model_code, $field_name);


		$factors_obj_arr = $this->format_csfs($weightages, $factors_arr);
		
		return $this->get_result($run_id, $model_code , $factors_arr, $factors_obj_arr, $weightages);
    }

    private function format_csfs($weightages, $factors_arr){
    	$factors = array();
    	$meta_obj = array();
    	$meta_data = array_filter($factors_arr, 'meta', ARRAY_FILTER_USE_KEY);

    	foreach ($weightages as $weightage) {
    		if ($weightage->weightage > 0){
	    		$normal_value = get_arr_val($factors_arr, $weightage->csf_type);
				$gross_value =  get_arr_val($factors_arr, "__".$weightage->csf_type);
				/*$factor['factor'] = $weightage->csf_type;
	    		$factor['normal_value'] = $normal_value;
	    		$factor['gross_value'] = $gross_value;*/
                $factor['csf_type'] = $weightage->csf_type;
                $factor['csf_normal_value'] = $normal_value;
                $factor['csf_gross_value'] = round($gross_value, 2);
			
	    		$factors[] = $factor;
    		}
    	}

    	foreach ($meta_data as $key => $value) {
	    	$meta['key'] = $key;
    		$meta['value'] = $value;
    		$meta_obj[] = $meta;
    	}

    	return [$factors, $meta_obj];
    }

    private function run_script($file_name, $args = null, $raw_response = false){

    	ob_start();
    	if($args){
    	    $args = implode(' ', $args);    
    	}else{
    	    $args = '';
    	}
    	
        #$file_name = separate([base_path(),$file_name]);
    	Log::warning("python $file_name $args");
    	$resp = passthru("python $file_name $args");
    	$output = ob_get_clean();
    	Log::warning("RESPONSE : " . $output);
    	if($raw_response){
    	    return $output;
    	}
    	$output = explode("\n", $output);
        Log::warning($output);        

    	if(!empty($output) && $output[0] == "#####"){
			return $output;

		/*if(!empty($output) && $output[0] == "#####"){
			$keys = explode(",", $output[1]);
			$values = explode(",", $output[2]);
			$csfs = array_combine($keys, $values);
			Log::warning($csfs);
			return $this->get_result($model_code ,$csfs );*/

			
		}else if(!empty($output) && $output[0] == '$$$$$'){
			thrw($output[1]);
		}else{
			thrw("Unknown error");
		}

    }
    
    //['772656752', 100, "759943918","flowchap"]
    public function transfer($arr){
        return $this->run_script('../app/Scripts/python/vendors/payment/chap_chap.py', $arr, true);
        
    }
    
    public function get_result($run_id, $model_code , $factors_arr, $factors_obj_arr, $weightages){
    		
    		
	 		$serv = new LoanApplicationService();
 			if(sizeof($weightages) > 0){
             
 				// Sending $weightages as "Pass by Ref" and put the csf_values
	 			$cust_score = $serv->calc_score($factors_arr, $weightages);
	 			 //$loan_product->cs_model_code, $loan_product->product_code);
	 			if(is_array($cust_score)){
	 			
	 				thrw("Unable to compute factor $cust_score[0] in the model weightage");
	 			}
	 			else
	 			{	
                    
	 				$result = $serv->decide_result($cust_score, $model_code);
	 				$result_code_return['run_id'] = $run_id;
		 			$result_code_return['score'] = $cust_score;
		 			$result_code_return['result'] = $result[0];
		 			$result_code_return['result_config'] = $result[1];
		 			$result_code_return['meta_data'] = $factors_obj_arr[1];
		 			$result_code_return['score_values'] = $factors_obj_arr[0];
		 			
		 			return $result_code_return;
				}
			}
	}	

    public function create_cust_csf_values($data){
        try
        {
            DB::beginTransaction();
            $data['result_code'] = $data['result'];
            $data['cust_txn_file_name'] = $data['customer_factor']['orig_cust_txn_file'];
            $dp_cust_id = get_arr_val($data['customer_factor'], 'data_prvdr_cust_id');
            $cust_id = get_arr_val($data['customer_factor'], 'cust_id');
            if($dp_cust_id){
                
                $data['data_prvdr_cust_id'] = $dp_cust_id;
                $result = DB::selectOne("select cust_id from borrowers where data_prvdr_cust_id = ?",[$dp_cust_id]);
                if($result){
                    $cust_id = $result->cust_id;
                }

            }
                
           

            if($cust_id){
                DB::update("update borrowers set csf_run_id = ? where cust_id = ?", [$data['run_id'], $data['cust_id']]);
                $brwr_repo = new BorrowerRepositorySQL();
                $result = $brwr_repo->find_by_code($cust_id, ['data_prvdr_cust_id']);
                $data['customer_factor']['data_prvdr_cust_id'] = $result->data_prvdr_cust_id;
                $data['data_prvdr_cust_id'] = $result->data_prvdr_cust_id;
            }

            /*$cust_id = get_arr_val($data['customer_factor'], 'cust_id');
            if($cust_id){
                $brwr_repo = new BorrowerRepositorySQL();
                $result = $brwr_repo->find_by_code($cust_id, ['data_prvdr_cust_id']);
                $data['customer_factor']['data_prvdr_cust_id'] = $result->data_prvdr_cust_id;
               
            }*/
            DB::delete("delete from score_runs where data_prvdr_cust_id = ?" , [$data['data_prvdr_cust_id']]);
            $common_repo = new CommonRepositorySQL(ScoreRun::class);
            $score_run_id = $common_repo->insert_model($data);

            $score_values = $data['score_values'];
            $common_repo = new CommonRepositorySQL(CustCSFValues::class);
            foreach($score_values as $score_value){
                $score_value['data_prvdr_cust_id'] = $data['customer_factor']['data_prvdr_cust_id'];
                $score_value['run_id'] = $data['run_id'];
                $score_value['country_code'] = $data['country_code'];
                $common_repo->insert_model($score_value);
                //$common_repo = new CommonRepositorySQL(ScoreRun::class);
                //$common_repo->insert_model($score_value);
            }

            if(array_key_exists('data_prvdr_cust_id', $data['customer_factor'])){
                $req_param = $data['customer_factor']['data_prvdr_cust_id'];
            }else if(array_key_exists('cust_id', $data)){
                
                $req_param = $data['cust_id'];
            }
            
            //DB::update("update borrowers set csf_run_id = ? where cust_id = ? or data_prvdr_cust_id = ?", [$data['run_id'], $req_param, $req_param]);
            DB::commit();
            return $score_run_id;          
            
        }catch (\Exception $e) {

            DB::rollback();  
            Log::warning($e->getTraceAsString());
            thrw($e->getMessage());
        }
      
    }

	public function get_score_eligibility($data){

		$brwr_repo = new BorrowerRepositorySQL();
		$loan_appl_serv = new LoanApplicationService();
		$scr_eligibility = new ScoreEligibilityScript();
		$ap_code = session('acc_prvdr_code');
		$not_calc_score  = config('app.first_n_prob_fas_wo_score')[$ap_code];

		$field_name = ['csf_type','new_cust_weightage as weightage'];

		$borrower = null;
		if ($data['cust_id']) {
			$borrower = $brwr_repo->find_by_code($data['cust_id'], ['csf_run_id', 'prob_fas', 'biz_type','perf_eff_date', 'cust_id', 'acc_number', 'country_code', 'category']);
		}

		if($borrower){
			$csf_values = $loan_appl_serv->get_csf_values($borrower);
		
			$csf_values_from_eff_date = $csf_values['perf_eff_result'];
			
			$csf_values_from_true_eff_date = $csf_values['true_perf_eff_result'];
			
			if($data['score_model'] =='loyalty_products'){

				$csf_values_arr = $csf_values_from_true_eff_date[0];
			}else{
				$csf_values_arr = $csf_values_from_eff_date[0];
			}
			if($borrower->category == 'Probation' || $borrower->category == 'Condonation'){
				[$current_prob_fa, $not_calc_score] = $loan_appl_serv->get_current_prob_fa_count($borrower->category, $borrower->prob_fas, $ap_code);
				
				if($current_prob_fa > $not_calc_score ){
					$field_name = ['csf_type','repeat_cust_weightage as weightage'];
				}
			}
			else{
				$field_name = ['csf_type','repeat_cust_weightage as weightage'];
			}

			
		}
		else{
			if (array_key_exists('run_id', $data)) {
				$run_id = $data['run_id'];
			}
			else {
				$run_id = (new CustCSFValuesRepositorySQL())->get_run_id($data['acc_number'], session('acc_prvdr_code'));
			}

			if (is_null($run_id)) {
				thrw("Unable to calculate score because this customer has no run ID");
			}

			$csf_values_arr = $this->get_csf_values_arr($data['run_id']);
		}

		$result = $scr_eligibility->check_model_eligibility($data['score_model'], $csf_values_arr, $field_name);

		return $result;
	}

	public function get_csf_values_arr($run_id){
		$loan_appl_serv = new LoanApplicationService();
		$csf_values = $loan_appl_serv->get_run_csf_values($run_id);
		
		foreach($csf_values as $csf_value){	
			$csf_values_arr[$csf_value->csf_type] = $csf_value->n_val;
			$csf_values_arr['gross_'.$csf_value->csf_type] = $csf_value->g_val;
		}
		return $csf_values_arr;
	}

	public function get_cs_model_factor_info($cs_model_code){
		$country_code = session('country_code');

		$required_fields = ["cs_model_code","csf_type","new_cust_weightage","repeat_cust_weightage"];
		$resp['cs_model_weightages'] = $this->list_cs_weightages(['cs_model_code' => $cs_model_code], $required_fields);
		if(empty($resp['cs_model_weightages'])) {
			thrw("No weightages in cs_model_weightages for given cs_model_code: '$cs_model_code'");
		}
		$required_fields = ["csf_group","csf_type","value_from","value_to","normal_value"];
		$required_csf_types = collect($resp['cs_model_weightages'])->pluck('csf_type')->toArray();
		$addtn_condtn = " AND country_code = '$country_code'";

		$resp['cs_factor_values'] = (new CsFactorValues)->get_records_by_in('csf_type', $required_csf_types, $required_fields, 'enabled', $addtn_condtn);
		return $resp;
	}

	private function insert_cust_csf_values($cust_csf_values) {
		foreach ($cust_csf_values as $cust_csf_value) {
			$cust_csf_values_repo = new CustCSFValuesRepositorySQL();
			$cust_csf_values_repo->insert_model($cust_csf_value);
		}
	}

	public function handle_duplicate_accounts($acc_number, $acc_prvdr_code) {

		$dup_accounts = [ 
			'UMTN' => [
				'806781' => 3593,
				'950428' => 3794,
				'994780' => 4610
			]
		];

		if (isset($dup_accounts[$acc_prvdr_code][$acc_number])) {

			$id = $dup_accounts[$acc_prvdr_code][$acc_number];
			return (new AccountRepositorySQL())->find($id, ['cust_id', 'status']);
		}
		return [];
	}

	public function get_scoring_model($acc_number) {
		
		$country_code = session('country_code');
		$acc_prvdr_code = session('acc_prvdr_code');

		$account_id = NULL;
		$borrower_id = NULL;

		$account = $this->handle_duplicate_accounts($acc_number, $acc_prvdr_code);
		if(empty($account)) {
			$account = (new AccountRepositorySQL())->get_account_by(['acc_number', 'acc_prvdr_code'], [$acc_number, $acc_prvdr_code], ['cust_id', 'status']);
		}

		if (isset($account)) {
			$account_id = $account->id;
			$borrower = (new BorrowerRepositorySQL())->find_by_code($account->cust_id, ['status']);
			if (isset($borrower)) {
				$borrower_id = $borrower->id;
				if ($borrower->status == 'enabled' && $account->status == 'enabled') {
					$customer_status = 'enabled';
					$cs_model_code = 'new_customers_with_data';
				}
				else {
					$customer_status = 'disabled';
					$cs_model_code = 'reassessment_model';
				}
			}
		}	
		else {
			$customer_status = 'potential';
			$cs_model_code = 'new_customers_with_data';
		}
			
		if ($acc_prvdr_code == 'UMTN' || $country_code == 'RWA') {
			$cs_model_code = 'comm_only_model';
		}

		$customer_details = [
							'status' => $customer_status,
							'account_id' => $account_id,
							'borrower_id' => $borrower_id,
							'cs_model_code' => $cs_model_code
						];

		return $customer_details;	
	}

	public function check_monthly_comms_and_insert_limit($data, $acc_elig_reason, $cust_csf_values) {
		
		$limit_csf_record = [];
		$acc_prvdr_code = $data['acc_prvdr_code'];
		$acc_elig_conditions = config('app.account_elig_conditions');

		if (isset($acc_elig_conditions[$acc_prvdr_code][$acc_elig_reason])) {

			$comms_record = collect($cust_csf_values)->where('csf_type', 'monthly_comms')->toArray();
			$comms_record = array_shift($comms_record);

			if(!is_null($comms_record)) {

				$limit_conditions = $acc_elig_conditions[$acc_prvdr_code][$acc_elig_reason];
				foreach ( $limit_conditions as $limit_condition ) {
					if (
						$comms_record['g_val'] < $limit_condition['commission_to'] &&
						$comms_record['g_val'] >= $limit_condition['commission_from']
						)
					{
						$data['condition'] = $limit_condition;
						$data['acc_elig_reason'] = $acc_elig_reason;

						$limit_csf_record = [(new AccountService)->upsert_cust_csf_approval_conditions($data)];
						return $limit_csf_record;
					}
				}
			}
		}
		return $limit_csf_record;
	}

	public function calculate_score_and_insert_csf_values($data) {
		try{
			DB::beginTransaction();
			$cust_csf_values_repo = new CustCSFValuesRepositorySQL();
			$borrower_repo = new BorrowerRepositorySQL();
			$data['country_code'] = session("country_code");
			$data['acc_prvdr_code'] = session("acc_prvdr_code");
			$acc_number = $data["acc_number"];
			$cust_score_factors = $data["cust_score_factors"];
			
			$borrower_id = $data['customer_details']['borrower_id'];
			$borrower_info = null;
			if ($borrower_id) {
				$borrower_info = $borrower_repo->find($borrower_id, ['cust_id', 'status']);
				$update_borrower = [ 'csf_run_id' => $data["run_id"], 'id' => $borrower_id ];
				($borrower_repo)->update_model($update_borrower);
			}
			
			$cust_csf_values_repo->delete_cust_csf_values($acc_number, $data['acc_prvdr_code']);
			$cust_csf_values_id = $cust_csf_values_repo->insert_model($data);
			
			$data['cust_id'] = ($borrower_info) ? $borrower_info->cust_id : null;
			$score_result_arr = $this->get_score_eligibility($data);
			$csf_data = [ 	
				'id' => $cust_csf_values_id,
				'score' => $score_result_arr[1],
				'result' => $score_result_arr[2]
			];
			$cust_csf_values_repo->update_model($csf_data);

			$acc_elig_reason = 'commission_based_limits';
			$limit_csf_record = $this->check_monthly_comms_and_insert_limit($data, $acc_elig_reason, $cust_score_factors);
			if( $data['customer_details']['account_id'] ) {
				$reason = empty($limit_csf_record) ? NULL : $acc_elig_reason;
				$update_account = [ 'acc_elig_reason' => $reason, 'id' => $data['customer_details']['account_id'] ];
				(new AccountRepositorySQL())->update_model($update_account);
			}

			if( $borrower_info && $borrower_info->status == 'enabled' && 
				$csf_data['result'] == 'ineligible' ) {
					
				$update_borrower = ['id' => $borrower_id, 'cust_id' => $borrower_info->cust_id, 'status' => 'disabled', 
									'status_reason' => 'others', 'remarks' => 'Ineligible based on score'];
				(new BorrowerService)->update_status(['borrowers' => $update_borrower]);
			}

			$resp = $csf_data;
			DB::commit();
			return $resp;
		}
		catch (Exception $e) {
			DB::rollback();
			thrw($e->getMessage());
		}
	}

	public function get_last_N_comms($acc_number, $acc_prvdr_code, $months) {

		$cust_comms_repo = new CustCommission;
		$comms_records = $cust_comms_repo->get_records_by_many(['acc_number', 'acc_prvdr_code'], [$acc_number, $acc_prvdr_code], ['year', 'commissions']);
		if (empty($comms_records)) return $comms_records;
		else {
			$comms_collection = collect($comms_records);
			$years = $comms_collection->pluck('year')->unique()->toArray();
        	sort($years);
			$amounts = [];

			for ($i=1; $i<=2; $i++) { // Consider current and previous year
				$latest_year = array_pop($years);
				if($latest_year) {
					$comms_record = $comms_collection->where('year', $latest_year)->toArray();
					$comms_record = array_shift($comms_record);
					$comms_amounts = (array)$comms_record->commissions;
					$comms_amounts = array_filter($comms_amounts, function($x) { return $x !== null; }); // Remove null from commissions

					$no_of_comms = count($comms_amounts);
					for ($j=1; $j <= $no_of_comms; $j++) {
						$amounts[] = array_pop($comms_amounts);
						if (count($amounts) == $months) return $amounts;
					}	
				}
			}	
			return $amounts;
		}
	}

	public function reassess_comms_customers($acc_prvdr_code) {
		
		try {
            DB::beginTransaction();

			session(['acc_prvdr_code' => $acc_prvdr_code]);
			$accounts = (new Account)->get_records_by_many(['acc_prvdr_code', 'status'], [session('acc_prvdr_code'), 'enabled'], ['acc_number', 'cust_id', 'acc_prvdr_code', 'alt_acc_num', 'holder_name'], "and", "AND cust_id IS NOT NULL");
			$accs_wo_commission = [];
		
			foreach ( $accounts as $account) {
				$acc_number = $account->acc_number;
				$customer_details = $this->get_scoring_model($acc_number);

				$comms = $this->get_last_N_comms($acc_number, session('acc_prvdr_code'), 3);
				if(empty($comms)) {
					$accs_wo_commission[] = $account;
				}
				else {
					$avg_comms = array_sum($comms) / count($comms);
					$avg_comms = round($avg_comms, 0, PHP_ROUND_HALF_UP);
				
					$factors = Factors::normalize($acc_number, session('country_code'), ['monthly_comms' => $avg_comms]);
					$factors = json_decode(json_encode($factors), true);

					$customer_data = [
						"country_code" => session('country_code'),
						"acc_number" => $acc_number,
						"acc_prvdr_code" => session('acc_prvdr_code'),
						"run_id" => uniqid(),
						"score_model" => "comm_only_model",
						"customer_details" => $customer_details,
						"cust_score_factors" => $factors
					];
					$this->calculate_score_and_insert_csf_values($customer_data);
				}
			}
			if ( count($accs_wo_commission) > 0) {
				$mail_data = ['country_code' => session('country_code'), 'accounts' => $accs_wo_commission, 'acc_prvdr_code' => session('acc_prvdr_code')];
				send_email('missing_comms_notification', config('app.ops_auditor_email'), $mail_data);
			}
			DB::commit();
		}
		catch (Exception $e) {
			DB::rollback();
			thrw($e->getMessage());
		}	
	}
	
	public function commission_based_limit($limit_conditions, $commission) {
		
		if($commission < $limit_conditions[0]['commission_from']) return 0;
		foreach ( $limit_conditions as $limit_condition ) {
			if (
				$commission < $limit_condition['commission_to'] &&
				$commission >= $limit_condition['commission_from']
				)
			{
				return $limit_condition['limit'];
			}
		}
		return null;
	}
}
