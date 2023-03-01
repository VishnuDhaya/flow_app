<?php

namespace App\Services;
use App\Http\Controllers\FlowApp\AppUserAuthController;
use App\Http\Controllers\InvApp\InvAuthController;
use App\Models\FlowApp\AppUser;
use App\Repositories\SQL\LenderRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\AgreementRepositorySQL;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\RecordAuditRepositorySQL;
use Illuminate\Support\Facades\DB;
use App\Repositories\SQL\RelationshipManagerRepositorySQL;
use App\Models\StatementImport;
use App\Consts;
use App\Models\RmTarget;
use App\Repositories\SQL\AccountRepositorySQL;
use Carbon\Carbon;
use Log;

class CommonService{

	public function get_loan_search_criteria($data){
		
		$country_code = session('country_code');
		$screen = $data['screen'];
		
		unset( $data['screen']);
		//$data_prvdr_name_list  = (new DataProviderRepositorySQL())->get_name_list(["data_prvdr_code", "name"]);
		$lender_name_list = (new LenderRepositorySQL())->get_name_list(["lender_code", "name"]);

		if ($screen == "loan_search"){
			$product_name_list = (new LoanProductRepositorySQL())->get_product_with_ap($data);
		}else{
			$product_name_list = (new LoanProductRepositorySQL())->get_name_list(['id', "product_name"]);	
		}

		
		
		$name_list = array();
		//$name_list['data_prvdr_name_list'] = $data_prvdr_name_list;
		$name_list['lender_name_list'] = $lender_name_list;
		$name_list['product_name_list'] = $product_name_list;
		$common_repo = new CommonRepositorySQL ($country_code);
		
        $loan_appl_service = new LoanApplicationService();
         
        if($screen == "loan_appl_search") {	
        	$name_list['loan_appl_status'] = $common_repo->get_master_data(['data_key' => "loan_appl_status"], true);		
			$name_list['applier_list'] = $this->list_users_by_priv($data, Consts::PRIV_CODE_APPLIER);
			$name_list['approver_list']  = $loan_appl_service->list_approvers($data);
         	
        }else if($screen == "loan_search") {	
			$name_list['loan_status'] = $common_repo->get_master_data(['data_key' => "loan_status"], true);
			$name_list['approver_list']  = $loan_appl_service->list_approvers($data);
			$name_list['payer_list']  = $this->list_users_by_priv($data, Consts::PRIV_CODE_REPAY);
            $name_list['write_off_status_list'] = $common_repo->get_master_data(['data_key' => "write_off_status"], true);
		}
		else if($screen == "borrower_search")
		{
          // $name_list['borrower_status'] = $common_repo->get_master_data($data, true);
         $name_list['borrower_status'] = $common_repo->get_master_data(['data_key' => "status"], true);
         $name_list['profile_status'] = $common_repo->get_master_data(['data_key' => "profile_status"], true);
         $name_list['activity_status'] = $common_repo->get_master_data(['data_key' => "activity_status"], true);
         $name_list['risk_category'] = $common_repo->get_master_data(['data_key' => "risk_category"], true);
         $name_list['location'] = $common_repo->get_master_data(['data_key' => "location", 'country_code' => session('country_code')]);

          // foreach ($borrower_status as $status) {
          //   $status->selected = $status->data_code == "enabled" ?  true : false;
          // }
          // $name_list['borrower_status']= $borrower_status;
        //   $data['data_key'] = 'territory';
        //   $name_list['territory_list'] = $common_repo->get_master_data($data);

        //   $data['data_key'] = 'location';
        //   #$name_list['region_list'] = $common_repo->get_master_data($data);
  		//   $name_list['location_list'] = $common_repo->get_master_data($data);

  			
          $rel_manger_repo = new RelationshipManagerRepositorySQL(); 
          $associated_with = "Flow";
          $name_list['flow_rel_list'] = $rel_manger_repo->get_flow_rel_name($country_code,$associated_with);
        }

		return $name_list;

	}

	public function list_users_by_priv($data, $priv_code , $select_login_user = false)
	{
		$data['priv_code'] = $priv_code ;;

       	$data['select_login_user'] = $select_login_user;
        $common_serv = new CommonService();
        return $common_serv->get_users($data);

	}


	public function get_users($data){
		
		$common_repo = new CommonRepositorySQL();
        if(array_key_exists('role_code', $data)){
             $persons_list =  $common_repo->get_users_by_role($data['role_code'],$data['status']);
        }else if(isset($data['role_codes'])){
        	$persons_list =  $common_repo->get_users_by_role_codes($data['role_codes'],$data['status']);
        }else if(isset($data['priv_code'])){
            $persons_list = $common_repo->get_users_by_priv($data['priv_code'],session('country_code'), $data['status']);
        }else{
        	
            $persons_list = $common_repo->get_all_users();  
        }

        $user_id = null;
        if(array_key_exists('select_login_user', $data) && $data['select_login_user'] == true){
            $user_id = session('user_id');    
        }
        
        return  $common_repo->get_person_full_names($persons_list, $user_id);

	}

	/*public function get_acc_txn_type($data)
	{  
		$data['data_key'] = "account_transaction_type";
		$common_repo = new CommonRepositorySQL();
		$account_txn_type = $common_repo->get_master_data($data);
		return $account_txn_type;
 	}*/

 	public function get_audit_changes($data){
    $record_repo = new RecordAuditRepositorySQL();
    $fields_arr = ['data_before','data_after','created_at','remarks'];
    $field_names = ["audit_type","record_code"];
    $field_values = [$data['audit_type'], $data['record_code']];
    $addl_sql_condition = "order by created_at desc";

    $result = $record_repo->get_records_by_many( $field_names,$field_values, $fields_arr, "and" ,$addl_sql_condition);

   
    
    return $result;

  }

  public function create_person($person){
        $person_repo = new PersonRepositorySQL;
        $person['id'] = $person_repo->create($person);

        if(array_key_exists('create_user', $person) && $person['create_user']) {
            $this->create_user($person);
        }
      return $person['id'];

  }

    public function list_stmt_imports($data){
      
        $date = isset($data['date']) ? $data['date'] : date_db();

        $end = isset($data['end_date']) ?? null;
        $addl = "";
        if($end) {
            $addl = " date(s.end_time) <= '{$end}' ";
        }

        $limit = isset($data['last_limit']) ? $data['last_limit'] : 10;
      
        $imports = DB::select(" select a.acc_number, a.network_prvdr_code, s.acc_prvdr_code,s.account_id,count(1) as total_imports, 
                                count(IF (s.status IN ( 'stmt_requested') , 1, null)) as total_requested,
                                count(IF(s.status not in ( 'imported', 'stmt_requested' ), 1, null)) as total_failures, count(IF(s.status = 'imported', 1, null)) as total_success, 
                                round(avg(IF(s.status = 'imported', TIMESTAMPDIFF(SECOND,s.start_time,s.end_time), null ))) AS avg_time
                                from float_acc_stmt_imports s, accounts a where s.account_id = a.id and date(s.start_time) >= '{$date}' {$addl} and s.country_code = '{$data['country_code']}' group by s.acc_prvdr_code, s.account_id;");

        foreach ($imports as $import){

            
            $import->success_perc =  ($import->total_success && $import->total_imports) ? round(($import->total_success/$import->total_imports) * 100) : 0;
            $last_n_import = DB::selectOne( "select max(start_time) as start_time, count(if(status = 'imported', 1 ,null)) as last_import from (select  f.*  from float_acc_stmt_imports f  where account_id = {$import->account_id}  and date(start_time) >= '{$date}'  order by id desc limit $limit) f");
            $import->last_import = $last_n_import->last_import;
            $import->start_time = $last_n_import->start_time;
        }

     return $imports;

    }

    public function search_stmt_imports($data){
        
        $data = $data['stmt_search_criteria'];

        $addl_sql = "and start_time >= '{$data['start_time']}' and start_time <= '{$data['end_time']}'";

        $field_arr = ['account_id'];
        $field_values = [$data['account_id']];

        if(isset($data['status'])){
            array_push($field_arr,'status');
            array_push($field_values,$data['status']);
        }
        
        $imports = (new StatementImport)->get_records_by_many($field_arr, $field_values, ["*"], "and", $addl_sql);

        $accounts = (new AccountRepositorySQL)->find($data['account_id'], ['acc_number', 'network_prvdr_code']);


        foreach($imports as $import){
            
            $import->acc_number =  $accounts->acc_number;
            $import->network_prvdr_code =  $accounts->network_prvdr_code;
            $time_diff = time_diff_between($import->start_time, $import->end_time);
            $import->dur_secs = $time_diff['dur_secs'];
        }

        return $imports;

    }

  public function create_user($person){

            if($person['role'] == 'investor'){
                $auth_controller = new InvAuthController();
                $auth_controller->createInvestorUser($person['email_id'],null,$person['id'],$person['first_name']);
            }
            else{
                $is_new_user = false;
                if($person['role'] == 'relationship_manager'){
                    $is_new_user = true;
                }

                AppUser::create([
                    'person_id' => $person['id'],
                    'email'    => $person['email_id'],
                    'password' => bcrypt(null),
                    'role_codes' => $person['role'],
                    'belongs_to' => "FLOW",
                    'belongs_to_code' => "FLOW",
                    'country_code' => session('country_code'),
                    'status' => 'enabled',
                    'is_new_user' => $is_new_user
                    ]);
               
                if($is_new_user){
                    $this->create_new_rm_target($person);
                }

                $auth_controller = new AppUserAuthController();
                $auth_controller->sendSetPasswordEmail($person);


            }
  }

  private function create_new_rm_target($person){
    
        $person_repo = new PersonRepositorySQL;
        $rm_target = new RmTarget;
        $rm_targets = (object)[];
        $rm_data['country_code'] = $person['country_code'];
        $rm_data['rel_mgr_id'] = $person['id'];
        $rm_data['rm_name'] =  $person_repo->full_name($person['id']);
        $rm_data['year'] = Carbon::now()->format('Y');
        $rm_data['targets'] = json_encode([Carbon::now()->format('M') => config('app.new_rm_target')]);
        $rm_target->insert_model($rm_data);
  }
}
