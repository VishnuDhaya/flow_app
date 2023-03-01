<?php

namespace App\Services;

use App\Mail\FlowCustomMail;
use App\Models\Loan;
use App\Repositories\SQL\MasterDataRepositorySQL;
use App\Services\LoanService;
use Exception;
use App\Exceptions\FlowCustomException;
use App\Consts;
use App\Services\BorrowerService;
use App\Services\AgreementService;
use App\Services\AccountService;
use App\Services\Mobile\RMService;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\CapitalFundRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\LenderRepositorySQL;
use App\Repositories\SQL\LoanApproversRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\AppUserRepositorySQL;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\ProbationPeriodRepositorySQL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CustCSFValues;
use App\Models\CSModelWeightages;
use App\Models\CSResultConfig;
use App\Models\PreApproval;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use PDF;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use App\Services\Support\SMSNotificationService;
use App\Services\Support\SMSService;
use App\Services\Support\FireBaseService;
use App\Models\FlowApp\AppUser;
use App\Jobs\SendDelayedDisbursalNotificationJob;
use App\Repositories\SQL\BaseRepositorySQL;
use App\Services\Vendors\Whatsapp\WhatsappWebService;


class LoanApplicationService{

	public function __construct()
	{
	      $this->country_code = session('country_code');
	      $this->api_req_id = session('api_req_id');
		  #$this->acc_prvdr_code = session('acc_prvdr_code');
	}

	public function check_eligibility($cust_id, $prod_id = null){
		$borrower_repo = new BorrowerRepositorySQL();
		$acc_repo = new AccountRepositorySQL();

		$borrower = $borrower_repo->find_by_code($cust_id, ["id","status","owner_address_id","fund_code",
						"late_loans","tot_loans","first_loan_date","current_aggr_doc_id","biz_type","csf_run_id",
						"prob_fas","acc_number","biz_name","cust_id","lender_code",
						"flow_rel_mgr_id","dp_rel_mgr_id","country_code","last_loan_doc_id","category",
						"pending_loan_appl_doc_id","ongoing_loan_doc_id","perf_eff_date","kyc_status",'owner_person_id',
						"acc_prvdr_code",'acc_purpose', "biz_address_id"]); // borrower



		$loan_appl_date = datetime_db();
		if($borrower->status != 'enabled'){
			thrw("Customer is in {$borrower->status} status", 1001, 'disabled_cust');
		}
		if($prod_id){
			$product_repo = new LoanProductRepositorySQL();

			$all_products = $this->get_products_result($borrower, true);
			$product = $product_repo->get_loan_product($prod_id);

			#$this->check_prod_eligibility_by_last_fa($product->max_loan_amount, $borrower->last_loan_doc_id);

			if($product->status != 'enabled'){
				thrw("FA Product is in {$product->status} status", 2001);
			}

			if($product->product_type === "topup"){
				$this->check_loan_for_topup_prod($cust_id, $master_loan_doc_id);
			}else{
				$this->check_loan_for_regular_prod($cust_id, $loan_appl_date);
			}

			$csf_values_arr = $this->get_cust_csf_values($borrower);

			if($product->cs_model_code == 'loyalty_products'){
				$csf_values_arr = $csf_values_arr['true_perf_eff_result'][0];
			}
			else{
				$csf_values_arr = $csf_values_arr['perf_eff_result'][0];
			}

			$calc_csf_values_arr = $this->calc_csf_value($product->cs_model_code, $csf_values_arr);
			$this->check_product_eligibility_1($borrower, $product, $calc_csf_values_arr);

			foreach($all_products as $prod){
				if($product->id == $prod->id){
					$product->result_code = $prod->result_code;
					$product->result_msg = $prod->result_msg;
					$product->is_eligible = $prod->is_eligible;
				}
			}
			// $this->check_prod_eligibility_by_last_fa($product->max_loan_amount, $borrower->last_loan_doc_id, $product);

		}else{
			$this->check_loan_for_regular_prod($cust_id, $loan_appl_date);

		}


		#$cs_result = 'eligible';

		return [$product, $borrower, $product->result_code, $product->is_eligible];

	}
	//temporary function
	private function check_prod_eligibility_by_last_fa($current_fa_amount, $last_loan_doc_id, &$product){

		$loan_repo = new LoanRepositorySQL();
		$this->currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;
		if($last_loan_doc_id){
			$last_loan = $loan_repo->get_record_by('loan_doc_id', $last_loan_doc_id, ['loan_principal']);
			if($last_loan->loan_principal < $current_fa_amount && $product->result_code == 'eligible'){
				$product->result_code = 'requires_flow_rm_approval';
				$product->result_msg = 'requires_flow_rm_approval';
				$product->is_eligible = false;
				// thrw("The last FA amount taken by this customer is {$last_loan->loan_principal} {$this->currency_code}. You can not apply a new FA of a higher value than this.");

			}
		}

	}


	public function apply_fa_by_product($cust_id, $prod_id, $account_id = null, $gps = null)
	{
		[$product, $borrower, $cs_result, $is_eligible] = $this->check_eligibility($cust_id, $prod_id);

//		if($borrower->kyc_status != 'completed'){
//			thrw("Could not allow new application. KYC status is ".$borrower->kyc_status);
//		}

		if(!in_array($cs_result, ['requires_flow_rm_approval', 'eligible'])){
			$result_msg = $this->get_result_msg($cs_result, $product->cs_model_code, $product->product_type, $borrower->category);
			thrw("Can not apply FA. {$result_msg}");
		}

		if($account_id == null){
			$account_repo = new AccountRepositorySQL();
			$account = $account_repo->getCustomerAccount($cust_id); // acc_type, acc_prvdr_name, acc_number
			$account_id = $account->id;
		}

		$this->validate_perf_eff_data($borrower->perf_eff_date);


		// $loan_repo = new LoanRepositorySQL();
		// $loan = $loan_repo->find_by_code($data['loan_doc_id'],['product_id','cust_name','cust_addr_text','cust_mobile_num']); // product_id

		$lender_repo = new LenderRepositorySQL();

		$person_repo = new PersonRepositorySQL();
		$common_repo = new CommonRepositorySQL();
		$addr_repo = new AddressInfoRepositorySQL();

		$borrower->product_id = $product->id;

		$person = $person_repo->get_person_name($borrower->owner_person_id);

		$borrower->cust_name = $person->first_name." ".$person->middle_name." ".$person->last_name;
		$borrower->cust_mobile_num = $person->mobile_num;

		// $person_addr = $addr_repo->find($borrower->owner_address_id);
		// $borrower->cust_addr_text = $person_addr['region'].",".$person_addr['district'].",".$person_addr['county'].",".$person_addr['sub_county'].",".$person_addr['parish'].",".$person_addr['village'].",".$person_addr['location'].",".$person_addr['landmark'].",".$person_addr['country_code'];
		$borrower->cust_addr_text = $addr_repo->find_addr_text($borrower->biz_address_id);
		// TODO take these data from person table using $borrower->contact_person_id
		//$borrower->cust_name = $loan->cust_name;
		//$borrower->cust_addr_text = $loan->cust_addr_text;
		//$borrower->cust_mobile_num = $loan->cust_mobile_num;


		$borrower->cust_acc_id =  $account_id; # CHECK


		$borrower->product_name = $product->product_name;
		$borrower->loan_principal = $product->max_loan_amount;
		$borrower->duration = $product->duration;
		$borrower->flow_fee = $product->flow_fee;
		$borrower->flow_fee_type = $product->flow_fee_type;
		$borrower->flow_fee_duration = $product->flow_fee_duration;
		$borrower->due_amount = $product->max_loan_amount + $product->flow_fee;
		$borrower->product_type = $product->product_type;
        $borrower->loan_purpose = $product->loan_purpose;


		$currency_code = $common_repo->get_currency();
		$curr_code = (array)$currency_code;
		$borrower->currency_code = $curr_code['currency_code'];

		$borrower->loan_approver_id = '3';
		$borrower->customer_consent_rcvd = true;
		$borrower->credit_score = 0;

		$borrower->cs_result_code = $cs_result; # take from $cs_result
		$borrower->applied_location = $gps;

		$borrower_arr = (array)$borrower;
		

		$result = $this->apply_loan($borrower_arr);
		$result['resp_msg'] = $this->get_response_msg($result);
		return $result;

	}
	private function get_approver($cust_id){
		$brwr_repo = new BorrowerRepositorySQL();
		$person_repo = new PersonRepositorySQL();

		$rm_person_id = $brwr_repo->get_flow_rel_mgr_id($cust_id);
		$is_new_user = $person_repo->is_new_user($rm_person_id);

		if($is_new_user){
			//$approver_id = $person_repo->get_op_mgr($cust_id);
			$approver_id = config('app.operations_manager')[session('acc_prvdr_code')];
			
		}else{
			$approver_id = $rm_person_id;
		}

		return [$approver_id, $is_new_user];

	}
	private function get_approval_details($cust_id, $product_type, $cs_result_code, $perf_eff_date,$acc_prvdr_code){

		$person_repo = new PersonRepositorySQL();
		$brwr_repo = new BorrowerRepositorySQL();
		$loan_repo = new LoanRepositorySQL();

		$approver_id = $approver_name = $auto_approve = $approver_role = null;

		// if($product_type != 'probation' && $cs_result_code == 'eligible'){ #temporily approve eligible products also
		// 	$auto_approve = true;
		// }
		// else if($product_type == 'probation' && $cs_result_code == 'eligible'){
		// 		$approver_id = $brwr_repo->get_flow_rel_mgr_id($cust_id);

		// }
    	if($product_type == 'probation' && $cs_result_code == "first_n_fas_wo_score"){
				//$approver_id = $brwr_repo->get_flow_rel_mgr_id($cust_id);
			$factors = new Factors($cust_id, $perf_eff_date);
			$avg_days_delayed = $factors-> _avg_days_delayed_per_FA();
			$cs_result_code = 'requires_flow_rm_approval';
			if($avg_days_delayed < 2){
				// $approver_id = $rm_person_id;
				[$approver_id, $is_new_user] = $this->get_approver($cust_id);
				$approver_role = "relationship_manager";
			}
			else if($avg_days_delayed >= 2 && $avg_days_delayed <= 5){
				$approver_id = config('app.operations_manager')[$acc_prvdr_code];
				$approver_role = "operations_manager";
			}
			else {
				$cs_result_code = 'ineligible';
			}

    	}
		else if($product_type != 'probation' && $cs_result_code == 'eligible'){ #temporily approve eligible products also
			[$approver_id, $is_new_user] = $this->get_approver($cust_id);
		}
		else if($product_type != 'probation' && $cs_result_code == 'requires_flow_rm_approval'){
			[$approver_id, $is_new_user] = $this->get_approver($cust_id);
			$rm_approval_limit = config('app.rm_approval_limit');
			$opm_approval_limit = config('app.opm_approval_limit');
			$total_limit = $rm_approval_limit + $opm_approval_limit;

			$perf_eff_date = $brwr_repo->get_record_by('cust_id',$cust_id,['perf_eff_date']);
			$new_perf_eff_date = calc_perf_eff_date($perf_eff_date->perf_eff_date);

			$last_n_fas = $loan_repo->get_last_n_loans($cust_id, $new_perf_eff_date, $total_limit);

			$rm_approved_fas = 0;
			$opm_approved_fas = 0;
			foreach($last_n_fas as $fa){

				if($fa->loan_approved_date){
					if(in_array($fa->approver_role,['relationship_manager', 'proxy_rm'])){
						$rm_approved_fas++;
					}else if($fa->approver_role == 'operations_manager'){
						$opm_approved_fas++;
					}
				}

			}

			if($rm_approved_fas >= $rm_approval_limit && $opm_approved_fas >= $opm_approval_limit){
				$cs_result_code = 'rm_approvals_exhausted';

			}
			else if($rm_approved_fas >= $rm_approval_limit){
				//$approver_id = $person_repo->get_op_mgr($cust_id);
				$approver_id = config('app.operations_manager')[$acc_prvdr_code];
				$approver_role = "operations_manager";

			}
			else if($is_new_user){
				//$approver_id = $person_repo->get_op_mgr($cust_id);
				$approver_id = config('app.operations_manager')[$acc_prvdr_code];
				$approver_role = "proxy_rm";

			}
			else{
				$approver_role = "relationship_manager";
			}

		}
		if($approver_id){
			$approver_name = $person_repo->full_name($approver_id);
		}
		return [$approver_id, $approver_name, $auto_approve, $cs_result_code, $approver_role ];
	}

	private function send_approval_notification($approver_id, $data, $loan_appl = null){

        try{
            $rm_serv = new RMService();
            $serv = new FireBaseService();
			[$email , $messenger_token]  = (new PersonRepositorySQL())->get_email_n_msgr_token($approver_id);

			if($messenger_token){
				$serv($data, $messenger_token, false);
			}
			
			if($loan_appl){
				$appr_mail_data = ['country_code' => session('country_code'), 'cust_name' => $loan_appl['cust_name'], 'loan_principal' => "{$loan_appl['loan_principal']} {$loan_appl['currency_code']}", 'cust_id' => $loan_appl['cust_id'],
				'duration' => $loan_appl['duration'], 'flow_fee' => "{$loan_appl['flow_fee']} {$loan_appl['currency_code']}", 'loan_appl_date' => format_date($loan_appl['loan_appl_date']), 'date' => date_ui()];
				Mail::to($email)->queue((new FlowCustomMail('approval_notification', $appr_mail_data))->onQueue('emails'));
			}

			if($data['notify_type'] == 'pre_approved'){
				$flow_rel_mgr_info = (new PersonRepositorySQL())->find($approver_id, ["whatsapp", "country_code"]);
				$country_code = DB::selectOne("select isd_code from markets where country_code = '$flow_rel_mgr_info->country_code'");
				$whatsapp = new WhatsappWebService();
				$notification_data = json_decode($data['loan'], true);
				LOG::warning($notification_data);
				$notification = "An FA of {$notification_data['loan_principal']} {$notification_data['currency_code']} has been approved for {$notification_data['cust_name']} against the pre-approval given to him. FA ID: {$notification_data['loan_doc_id']}.";
				LOG::warning($notification);
				$whatsapp->send_message(["body" => $notification, "to" => $flow_rel_mgr_info->whatsapp, "isd_code" => $country_code->isd_code, "session" => config('app.whatsapp_notification_number')]);
			}
			

		}catch(\GuzzleHttp\Exception\RequestException $e){
            $exp_msg = $e->getMessage();
			$trace = $e->getTraceAsString();
			$responseBody = $e->getResponse()->getBody(true);
			Log::error($exp_msg);
			Log::error($responseBody);
            $mail_data = ['country_code' => session('country_code'), 'exp_msg' => $exp_msg, 'exp_trace' => $responseBody, 'notify_type' => $data['notify_type']];
            $mail_data['recipient_name'] = (new PersonRepositorySQL())->full_name_by_sql($approver_id);
			Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('notification_failed', $mail_data))->onQueue('emails'));
		}catch(\Exception $e){
			send_notification_failed_mail($e, $approver_id, $data['notify_type']);
		}
	}

	

	private function check_recent_late_payments($cust_id, $return = false){
		$last_n_fas = config('app.pre_approval_fa_count');
		$loan_repo = new LoanRepositorySQL;
		$addl_sql = "and status = 'settled' order by status, disbursal_date desc  limit $last_n_fas";
		$recent_loans = $loan_repo->get_records_by('cust_id', $cust_id, ['due_date', 'paid_date'], null, $addl_sql );
		$late = false;
		$tot_late_days = 0;
		$err_msg = "You can not pre-approve because the customer has been paying late their recent FAs";
		foreach($recent_loans as $recent_loan){
			if($recent_loan->paid_date){
				$late_days = getPenaltyDate($recent_loan->due_date, $recent_loan->paid_date);
				if($late_days >= 3){
					return  $return ? "repaid_late_recently" : thrw($err_msg);
				}
				$tot_late_days += $late_days;
			}
			

		}


		if($tot_late_days > $last_n_fas){
			return  $return ? "repaid_late_recently" : thrw($err_msg );
		}
	}

	
	private function check_recent_visits($cust_id , $return = false){
		$borr_repo = new BorrowerRepositorySQL;
		$flow_rel_mgr_id = session('user_person_id');
		$borrower = $borr_repo->find_by_code($cust_id,['next_visit_date', 'last_visit_date']);
		if(isset($borrower->next_visit_date)){
			$date = Carbon::parse($borrower->last_visit_date);
			$now = Carbon::now();
			$diff = $date->diffInDays($now);
			$err_msg = "You can not pre-approved, because this customer was not visited recently and last visited {$diff} days ago.";
			return  $return ? "no_recent_visit" : thrw($err_msg);
		}
	}

	public function allow_cust_pre_approval($data){
		$cust_id = $data['cust_id'];
		$borr_repo = new BorrowerRepositorySQL;
		$flow_rel_mgr_id = session('user_person_id');
		$borrower = $borr_repo->find_by_code($cust_id,['flow_rel_mgr_id']);

		if($flow_rel_mgr_id != $borrower->flow_rel_mgr_id){
			thrw("You're not allowed to do pre-approval for this customer");
		}
		$this->check_recent_late_payments($cust_id);

		$this->check_recent_visits($cust_id);
		
		$pre_appr_id = $this->insert_preapproval($cust_id, $flow_rel_mgr_id, true);

		return $pre_appr_id;
	
	}

	private function insert_preapproval($cust_id, $flow_rel_mgr_id, $with_txn = true){
		
		try{
			$with_txn ? DB::beginTransaction() : null;
			$borr_repo = new BorrowerRepositorySQL();
			$pre_appr_repo = new PreApproval;
			$pre_appr['appr_start_date'] = datetime_db();
			$pre_appr['appr_exp_date'] = Carbon::now()->addDays(config('app.pre_approval_validity_days'));
			$pre_appr['appr_count'] = config('app.pre_approval_fa_count');
			$pre_appr['cust_id'] = $cust_id;
			$pre_appr['country_code'] = session('country_code');
			$pre_appr['status'] = 'enabled';
			$pre_appr['flow_rel_mgr_id'] = $flow_rel_mgr_id;
			$pre_appr_id = $pre_appr_repo->insert_model($pre_appr);
			$borr_repo->update_model_by_code(['cust_id' => $cust_id,'pre_appr_count' => $pre_appr['appr_count'],
														'pre_appr_exp_date' => $pre_appr['appr_exp_date']]);
			$with_txn ? DB::commit() : null;
		} catch (Exception $e) {
			$with_txn ? DB::rollback() : null ;
            throw new Exception($e->getMessage());
        }
		

		return $pre_appr_id;

	}

	public function apply_loan(array $loan_appl, $with_txn = true, $send_delayed_notif_sms = true)

	{
		$loan_product_repo = new LoanProductRepositorySQL();
		$loan_appl_repo = new LoanApplicationRepositorySQL();



		$loan_appl['loan_appl_date'] = array_key_exists('loan_appl_date', $loan_appl)  ? $loan_appl['loan_appl_date'] : datetime_db();


		try
		{
			$with_txn ? DB::beginTransaction() : null;

				$loan_appl['status'] = Consts::LOAN_APPL_PNDNG_APPR; // pending_approval

				$person_repo = new PersonRepositorySQL();
				$loan_appl['cs_result_code'] = $loan_appl['product_type'] == 'probation' ? 'first_n_fas_wo_score' : $loan_appl['cs_result_code'];
				$loan_appl['loan_applied_by'] = session('user_id');
				[$approver_id, $approver_name, $auto_approve, $cs_result_code, $approver_role] = $this->get_approval_details($loan_appl['cust_id'], $loan_appl['product_type'], $loan_appl['cs_result_code'], $loan_appl['perf_eff_date'],$loan_appl['acc_prvdr_code']);

				if($cs_result_code == 'rm_approvals_exhausted'){
					thrw("Already customer has exhausted all the approval limits. If the customer is ineligible for all the products, consider allowing condonation");

				}

				$loan_appl['loan_approver_id'] = $approver_id;
				$loan_appl['loan_approver_name'] = 	$approver_name;
				$loan_appl['approver_role'] = 	$approver_role;
                $loan_appl['channel'] = session('channel');


				if (!array_key_exists('loan_appl_doc_id',$loan_appl)){
					$loan_appl['loan_appl_doc_id'] = $this->gen_new_loan_appl_id($loan_appl['country_code'], $loan_appl['cust_id']);
				}


				$brwr_repo = new BorrowerRepositorySQL();
				$borrower = $brwr_repo->find_by_code($loan_appl['cust_id'], ['tot_loan_appls','biz_name']);

				$account_repo = new AccountRepositorySQL();
				$acc = $account_repo->find($loan_appl['cust_acc_id'], ['acc_prvdr_code', 'acc_number']);
				$loan_appl['acc_prvdr_code'] = $acc->acc_prvdr_code;
				$loan_appl['acc_number'] = $acc->acc_number;
				
				$loan_appl_id = $loan_appl_repo->create($loan_appl);		
				
				$loan_appl['biz_name'] = $borrower->biz_name;
				
				if($send_delayed_notif_sms == true){
					SendDelayedDisbursalNotificationJob::dispatch($loan_appl)->delay(now()->addMinutes(config('app.fa_delay_notify_time')));
				}

				$brwr_repo->update_model_by_code([
													'cust_id' => $loan_appl['cust_id'],
													 'pending_loan_appl_doc_id' => $loan_appl['loan_appl_doc_id']

												]);
				$brwr_repo->increment_by_code("tot_loan_appls", $loan_appl['cust_id']);

				
				# TODO Check eligibility again
				$pre_appr_id = $this->get_pre_appr_id($loan_appl['cust_id'],$loan_appl);

				$loan_application = $this->get_application(["loan_appl_doc_id" => $loan_appl['loan_appl_doc_id']]);
				$resp['loan_application'] = $loan_application;			

				if($auto_approve || $pre_appr_id ){
					$master_loan_doc_id = array_key_exists('master_loan_doc_id', $loan_appl)  ? $loan_appl['master_loan_doc_id'] : null;
					
					$action = $pre_appr_id ? "pre_approve" : "auto_approve";
 					$approve_req = ["loan_appl_doc_id" => $loan_appl['loan_appl_doc_id'],
								"master_loan_doc_id" => $master_loan_doc_id,
								"action" => $action,
								"country_code" => $loan_appl['country_code'],
								"cust_id" => $loan_appl['cust_id'],
								"product_id" => $loan_appl['product_id'] ,
								"credit_score" => $loan_appl['credit_score'],
								"cs_result_code" => $loan_appl['cs_result_code'],
								"appr_reason" => "",
								'pre_appr_id' => $pre_appr_id,
								];

					$this->approval($approve_req, false);

					
				}else{
					$data['notify_type'] = 'new_loan_appl';
					if($loan_application->pre_appr_ignr_reason){
						$data['pre_appr_ignr_reason'] = $loan_application->pre_appr_ignr_reason;
					}

					if(config('app.env') != 'local') {
						$this->send_approval_notification($loan_appl['loan_approver_id'], $data, $loan_appl);
					}
				}

			$with_txn ? DB::commit() : null;
		}
	    catch (FlowCustomException $e) {
			$with_txn ? DB::rollback() : null;
			Log::warning($e->getTraceAsString());
			throw new FlowCustomException($e->getMessage());
		}
		catch (Exception $e) {
			$with_txn ? DB::rollback() : null;
			Log::warning($e->getTraceAsString());
			if ($e instanceof QueryException){
			    throw $e;
			}else{
				thrw($e->getMessage());
			}
		}

	   return $resp;


	}

	private function check_loan_for_preapproval($cust_id, $loan_appl, $return = false){
		
		$borr_repo = new BorrowerRepositorySQL();
		$loan = $borr_repo->get_last_loan($cust_id,['loan_principal']);
		$last_loan_principal = $loan->loan_principal;
		$loan_principal = $loan_appl['loan_principal'];
		
		if($loan_appl['cs_result_code'] == 'requires_flow_rm_approval' ){
			return  $return ? "low_score" : thrw("You can not pre-approve because the customer's score is low");
		}else if($loan_appl['loan_principal'] > $last_loan_principal ){
			return  $return ? "higher_fa" : thrw("You can not pre-approve because the current FA ($loan_principal) is more than previous FA ($last_loan_principal)");
		}
		//temporary code
		else if($loan_principal >= config('app.pre_approval_limit_amount')){
			return $return ? "pre_appr_limit_exceed" : thrw("You can not pre-approve because the current FA {$loan_principal} exceeds the pre-approval limit");
		}

	}

	private function get_pre_appr_id($cust_id, $loan_appl){
	
		$pre_appr_data = DB::selectOne("select id from pre_approvals where cust_id = ? and appr_count > 0 and date(appr_exp_date) >= ? and status = ? and country_code = ? ",[$loan_appl['cust_id'],date_db(),'enabled',session('country_code')]);
		$loan_appl_repo = new LoanApplicationRepositorySQL;
		if($pre_appr_data){
			$reason = $this->check_loan_for_preapproval($cust_id, $loan_appl, $return = true);
			
			if($reason == null){
				$reason = $this->check_recent_late_payments($cust_id, $return = true);
			}
			if($reason == null){
				$reason = $this->check_recent_visits($cust_id, $return = true);
			}

			if($reason == null){
				return $pre_appr_data->id;
			}else{
				if($reason == "higher_fa" || $reason == "repaid_late_recently" || $reason == "pre_appr_limit_exceed"){
					$rm_serv = new RMService();
					$data['cust_id'] = $cust_id;
					$rm_serv->remove_pre_approval($data);
					
					$this->send_pre_appr_remove_email_notification($loan_appl,$reason);
				}
				$loan_appl_repo->update_model_by_code(['loan_appl_doc_id' => $loan_appl['loan_appl_doc_id'],'pre_appr_ignr_reason' => $reason]);
				
				

			}
			
		}
		
		
	}

	public function send_pre_appr_remove_email_notification($loan_appl,$reason){
		$email = AppUser::where('person_id',$loan_appl['loan_approver_id'])->get(['email'])->pluck("email")[0];
		$mail_data = ['country_code' => session('country_code'), 'cust_name' => $loan_appl['cust_name'], 'loan_principal' => "{$loan_appl['loan_principal']} {$loan_appl['currency_code']}", 'cust_id' => $loan_appl['cust_id'],
					'duration' => $loan_appl['duration'], 'flow_fee' => "{$loan_appl['flow_fee']} {$loan_appl['currency_code']}", 'loan_appl_date' => format_date($loan_appl['loan_appl_date']), 'date' => date_ui(),'reason' => $reason];
		Mail::to($email)->queue((new FlowCustomMail('pre_appr_remove_notification',$mail_data))->onQueue('emails'));
	}


	public function repeat_fa($data)
	{
		
		// $borrower_service = new BorrowerService($this->country_code);
		// $borrower_info = $borrower_service->get_borrower($data['cust_id']);

		$loan_repo = new LoanRepositorySQL();
		$acc_repo = new AccountRepositorySQL();
		$prod_repo = new LoanProductRepositorySQL();
		$loan = $loan_repo->find_by_code($data['loan_doc_id'],['product_id','cust_acc_id','cust_id']); // product_id
		$cust_acc = $acc_repo->find($loan->cust_acc_id,['status','acc_number','acc_purpose']);
		$product = $prod_repo->find($loan->product_id,['status']);
		
		if($cust_acc->status != 'enabled' ){
			thrw("You can not repeat FA for this customer {$data['cust_id']} because the previous FA was disbursed to a different account {$cust_acc->acc_number}",null,'merchant_change');
		}
		if(!in_array('float_advance', $cust_acc->acc_purpose)){
			thrw(" You cannot repeat FA for this customer {$data['cust_id']} because the account purpose of this customer is not Float Advance ");
		}
		if($product->status != 'enabled'){
			thrw("The Previous FA product you have choosed is been disabled. please apply new product");
		}
		if($loan->cust_id == $data['cust_id']){
			$product = $this->get_equivalent_product_for_cust($data['cust_id'], $loan->product_id);
			return $this->apply_fa_by_product($data['cust_id'], $product->id, $loan->cust_acc_id);
		}else{
			thrw("The FA {$data['loan_doc_id']} does not belong to the customer {$data['cust_id']}");
		}
				
	}

    public function get_equivalent_product_for_cust($cust_id, $product_id){
            $product_repo = new LoanProductRepositorySQL();
			$product = $product_repo->find($product_id, ['max_loan_amount','flow_fee','product_type','acc_prvdr_code', 'duration']);
            $borrower_info = (new BorrowerRepositorySQL)->find_by_code($cust_id, ['current_aggr_doc_id', 'lender_code', 'category']);
            $aggr = (new CustAgreementRepositorySQL)->get_record_by_many(['aggr_doc_id','status'], [$borrower_info->current_aggr_doc_id, 'active'],['aggr_type']);
			
			if($aggr){
				$loan_products = $product_repo->get_products_by(['lender_code','acc_prvdr_code','max_loan_amount','flow_fee','duration','status','loan_purpose'], [$borrower_info->lender_code, $product->acc_prvdr_code, $product->max_loan_amount, $product->flow_fee, $product->duration, 'enabled', 'float_advance'], $aggr->aggr_type);
			}else{
				thrw("There is no active agreement for this customer.");
			}
			
            if(sizeof($loan_products) == 1){
                return $loan_products[0];
            }
			elseif(sizeof($loan_products) > 1){
				thrw("Multiple products found");
			}
            elseif(sizeof($loan_products) == 0){
                if($product->product_type == 'probation' && $aggr->aggr_type != 'probation'){
                    thrw("The product is not available for a {$aggr->aggr_type} customer");
                }
                else{
                    thrw("Product not available");
                }
            }
    }
	public function gen_new_loan_appl_id($country_code, $cust_id){
		$common_repo = new CommonRepositorySQL();
		$loan_appl_id = $common_repo->get_new_flow_id($country_code, 'loan_appl');
		$fl_date = float_date();
		//$loan_appl_doc_id = "APPL-{$loan_appl['country_code']}-{$loan_appl['acc_prvdr_code']}-{$fl_date}-{$loan_appl_id}";
		$loan_appl_doc_id = "APPL-{$cust_id}-{$loan_appl_id}";
		return $loan_appl_doc_id;
	}
	public function check_loan_for_regular_prod($cust_id, $loan_appl_date){
		$apply_loan = false;
		$acc_purpose = 'float_advance';
		$loan_appl_repo = new LoanApplicationRepositorySQL();

		/* $last_loan = (new LoanRepositorySQL())->get_last_loan_by_purpose($cust_id, $loan_appl_date, $acc_purpose);
        if($last_loan == null || in_array($last_loan->status, Consts::ALLOWED_LAST_LOAN_STATUS)){

			$last_loan_appl = $loan_appl_repo->get_last_loan_appl_by_purpose($cust_id, $loan_appl_date, $acc_purpose);

			if($last_loan_appl == null || $last_loan_appl->status !=Consts::LOAN_APPL_PNDNG_APPR){

					$apply_loan = true;
			}else{
				thrw("Could not allow new application. Last FA Appl {$last_loan_appl->loan_appl_doc_id} in {$last_loan_appl->status} status.", 3001, 'pending_fa_appl');
			}
		}else{

			if(session()->has('google_sheet_import')){
				$apply_loan = true;
			}else{
				thrw("Could not allow new application. Last FA {$last_loan->loan_doc_id} in {$last_loan->status} status.", 3002, 'ongoing_fa');
			}

		}*/

		// repeat queue functions
		$closed_loan_status = [Consts::LOAN_SETTLED, Consts::LOAN_CANCELLED]; 
		
		$os_loan = (new LoanRepositorySQL())->get_loan_by_not_in_status($cust_id, $closed_loan_status, $acc_purpose);
		if($os_loan == null){
			$loan_appl_pending_status = Consts::LOAN_APPL_PNDNG_APPR;
            $os_loan_appl = (new LoanApplicationRepositorySQL)->get_loan_appl_by_status($cust_id, $loan_appl_pending_status , 'float_advance');
			if($os_loan_appl == null){

				$apply_loan = true;
			}else{
				$last_loan_appl = $loan_appl_repo->get_last_loan_appl_by_purpose($cust_id, $loan_appl_date, $acc_purpose);
				thrw("Could not allow new application. Last FA Appl {$last_loan_appl->loan_appl_doc_id} in {$last_loan_appl->status} status.", 3001, 'pending_fa_appl');
			}
		}
		else{
			$last_loan = (new LoanRepositorySQL())->get_last_loan_by_purpose($cust_id, $loan_appl_date, $acc_purpose);
			thrw("Could not allow new application. Last FA {$last_loan->loan_doc_id} in {$last_loan->status} status.", 3002, 'ongoing_fa');
			
		}

		return $apply_loan;

	}

	
	public function validate_appl($data){

		$master_loan_doc_id = null;

		//$this->check_if_cust_acc_exist($data);
		$data['loan_appl_date'] = array_key_exists('loan_appl_date', $data)  ? null : datetime_db();

		$this->validate_perf_eff_data($data['perf_eff_data']);

		$this->check_last_loan($data, $master_loan_doc_id);
		return ['master_loan_doc_id' => $master_loan_doc_id];


    }

	private function validate_perf_eff_data($data){

		$perf_eff_date = date("Y-m-d", strtotime($data));
		$today_date = date("Y-m-d", strtotime(datetime_db()));


		if($today_date >= $perf_eff_date){
			return true;
		}
		else{
			thrw("Customer has been given a condonation on {$perf_eff_date}. You will have to wait until {$perf_eff_date} to apply an FA.");
		}
	}
    private function check_loan_for_topup_prod($cust_id , &$master_loan_doc_id){

	    	$master_loan = (new LoanRepositorySQL())->get_unsettled_loan($cust_id);

	    	if(!$master_loan){
	    		// thrw("Cannot apply loan for Topup Product. You don't have an unsettled Float Advance");
	    		if(!session()->has('google_sheet_import')){
	    			thrw("To apply for a Topup Product, you must have an ongoing Float Advance", 3003);
				}else{
					return true;
				}
	    	}else{
	    		$master_loan_doc_id = $master_loan->loan_doc_id;
	    		return true;
	    	}

    }

    private function check_last_loan($data, &$master_loan_doc_id = ''){
    	if($data['product_type'] === "topup"){
	    	 return $this->check_loan_for_topup_prod($data['cust_id'], $master_loan_doc_id);
		}else{
	    	return $this->check_loan_for_regular_prod($data['cust_id'], $data['loan_appl_date']);
	    }

    }

	public function product_search($data){

		$search_param = $data["req_parameter"];
		$borrower_service = new BorrowerService($this->country_code);
		$borrower_repo = new BorrowerRepositorySQL();
		$products_result = [];
		$borrower_info = $borrower_service->get_borrower($search_param);

		//Log::warning($borrower_info->aggr_file_rel_path);
		//$currency_code = $common_repo->get_currency_code($country_code);
		//$currency_code = $currency_code[0];

		if(is_array($borrower_info)){
			$products_result["borrowers"] = $borrower_info;
			//$products_result["is_borrower"] = false;
		}else if($borrower_info){


			if($borrower_info){
				$products_result = $this->get_products_result($borrower_info);

			}
		}

		
		return $products_result;
	}

	private function get_products_with_eligibility($borrower_info){

		$loan_prod_repo = new LoanProductRepositorySQL();

		$get_cust_csf_values_return = $this->get_cust_csf_values($borrower_info);
		$csf_values_return_from_eff_date = 	$get_cust_csf_values_return['perf_eff_result'];

		$csf_values_return_from_true_eff_date = $get_cust_csf_values_return['true_perf_eff_result'];
		// true_eff_date is the date as in the borrower table
		$limit_amt = $this->get_elig_appr_limit_amt($borrower_info->cust_id);
		$loan_products = $loan_prod_repo->get_products_by(['lender_code','acc_prvdr_code','status','loan_purpose'],
                                [$borrower_info->lender_code, $borrower_info->acc_prvdr_code, 'enabled', 'float_advance'],
                                $borrower_info->agrmt_for, $limit_amt);

		$all_cs_model_codes = array();
		foreach ($loan_products as $loan_product) {

			$all_cs_model_codes[$loan_product->cs_model_code] = null ;
		}

		foreach($all_cs_model_codes as $cs_model_code_key => $value){

			if($cs_model_code_key == 'loyalty_products'){
				$csf_values_arr = $csf_values_return_from_true_eff_date[0];
			}else{
				$csf_values_arr = $csf_values_return_from_eff_date[0];
			}

			$all_cs_model_codes[$cs_model_code_key] = $this->calc_csf_value($cs_model_code_key, $csf_values_arr);

		}

		foreach ($loan_products as $loan_product) {
			$calc_csf_values_arr = $all_cs_model_codes[$loan_product->cs_model_code];
			$this->check_product_eligibility_1($borrower_info, $loan_product, $calc_csf_values_arr, true);

		}

		$all_csf_values = $csf_values_return_from_eff_date[1];
		return [$loan_products, $all_csf_values];
	}

	private function is_all_ineligible($loan_products){
		$all_ineligible = false;
		$ineligi_count = 0;
		$all_eligible = false;
		$eligi_count = 0;
		foreach($loan_products as $loan_product){
			if($loan_product->result_code == "ineligible" || $loan_product->result_code == "rm_approvals_exhausted"){
				$ineligi_count++;
			}
			else if($loan_product->result_code == "eligible"){
				$eligi_count++;
			}
		}

		$loan_products_length = sizeof($loan_products);
		if(sizeof($loan_products) > 0 && $loan_products_length == $ineligi_count)
		{
			$all_ineligible = true;
		}
		if(sizeof($loan_products) > 0 && $loan_products_length == $eligi_count)
		{
			$all_eligible = true;
		}
		//return $all_ineligible;
		return [$all_ineligible, $all_eligible];
	}

	public function check_if_all_ineligible($borrower_info){
		[$loan_products, $all_csf_values] = $this->get_products_with_eligibility($borrower_info);
		//$all_ineligible = $this->is_all_ineligible($loan_products);
		$this->apply_eligibility_across_products($loan_products);
		[$all_ineligible, $all_eligible] = $this->is_all_ineligible($loan_products);
		//return $all_ineligible;
		return [$all_ineligible, $all_eligible];

	}
	public function get_products_result($borrower_info, $get_products = false){

		$products_result = [];
		$common_repo = new CommonRepositorySQL();
		$cus_aggr_repo = new CustAgreementRepositorySQL();
		[$loan_products, $all_csf_values] = $this->get_products_with_eligibility($borrower_info);
		//$all_ineligible = $this->is_all_ineligible($loan_products);
		[$all_ineligible, $all_eligible] = $this->is_all_ineligible($loan_products);
		if($all_eligible == false){
			$this->apply_eligibility_across_products($loan_products);
		}
		$products_result['cust_agreement_status'] = null;
		$agreement_status = $cus_aggr_repo->get_cust_agreement_status($borrower_info->current_aggr_doc_id);

		if (isset($agreement_status->status) && $agreement_status->status) {
			$products_result['cust_agreement_status'] = $agreement_status->status;
		}
		$products_result["borrower"] = $borrower_info;
		$products_result["loan_products"] = $loan_products;
		$products_result["all_csf_values"] = $all_csf_values;
		$products_result['all_ineligible'] = $all_ineligible;
		$products_result['category'] = $borrower_info->category;

		if($get_products){
			return $loan_products;
		}
		return $products_result;
	}

	public function apply_eligibility_across_products(&$loan_products){
		$max_ineligible = $this->get_max_amount($loan_products, ['ineligible', 'rm_approvals_exhausted']);
		$max_rm_approval = $this->get_max_amount($loan_products, ['requires_flow_rm_approval']);
		//$max_apprvl_exhausted = $this->get_max_amount($loan_products, 'rm_approvals_exhausted');

		// $max_amounts = [array_values($max_ineligible)['amount'],
		// 				array_values($max_rm_approval)['amount'],
		// 				array_values($max_apprvl_exhausted)['amount']];
		// $max_amount  = max($max_amounts);

		$max_amount = null;

		if($max_ineligible['amount'] > 0 && $max_ineligible['amount'] != $max_rm_approval['amount'] ){
			$max_amount = $max_ineligible;
		}
		else if ($max_rm_approval['amount'] > 0) {
            $max_amount = $max_rm_approval;
		}

		foreach($loan_products as $loan_product){
			if($max_amount && $loan_product->max_loan_amount > $max_amount['amount'] && $loan_product->product_type != 'topup'){

				$loan_product->result_code = $max_amount['result_code'][0];
				$loan_product->is_eligible = false;
				$loan_product->approver_first_name = $max_amount['approver_name'];

				if($loan_product->result_code == 'ineligible'){
					$loan_product->result_msg = $this->get_result_msg($loan_product->result_code, $loan_product->cs_model_code, $loan_product->product_type, null);
				}
				else if($loan_product->result_code != 'rm_approvals_exhausted'){
					$loan_product->result_msg = $loan_product->result_code;
				}
			}
		}
	}
	private function get_max_amount($loan_products, $result_code){

		$loan_amounts = array();
		$max_amount = 0;
		$approver_name =null;

		foreach($loan_products as $loan_product){
			foreach($result_code as $value){
				if($loan_product->result_code == $value && $loan_product->product_type != 'topup'){
					array_push($loan_amounts, $loan_product->max_loan_amount );
					$approver_name = $loan_product->approver_first_name;
				}
			}
		}
		if($loan_amounts){
			$unique_amounts = array_unique($loan_amounts);
			$max_amount  = max($unique_amounts);
		}
		// $result[$max_amount]
		$result = ['amount' => $max_amount, 'result_code' => $result_code, 'approver_name' => $approver_name];
		return $result;
	}
	public function get_active_agreement($current_aggr_doc_id){
		$cus_aggr_repo = new CustAgreementRepositorySQL();
	 			//$agrmt_products = $cus_aggr_repo->get_record_by('aggr_doc_id', $borrower_info->current_aggr_doc_id, ['product_id_csv'], $this->country_code);
		$date = Carbon::now();
		$today_date = date_db($date);
		$addl_sql_condition = " and (date(valid_from) <= '$today_date' and (date(valid_upto) >= '$today_date' or valid_upto is null) ) ";

		return $cus_aggr_repo->get_record_by_many(['aggr_doc_id','status'], [$current_aggr_doc_id,"active"], ['product_id_csv','status'],"and",$addl_sql_condition);

	}
	public function get_cust_csf_values(&$borrower_info){

		if(in_array($borrower_info->biz_type, [Consts::FLOW_RM, Consts::DP_RM])){
			$borrower_info->agrmt_for = 'float_vending';
			return [ "perf_eff_result" => [[], []], "true_perf_eff_result" => [[], []]];
		}
		else if($borrower_info->prob_fas > 0){
			[$current_prob_fa, $not_calc_score] = $this->get_current_prob_fa_count($borrower_info->category, $borrower_info->prob_fas, $borrower_info->acc_prvdr_code);

			if($current_prob_fa <= $not_calc_score){       // if($current_prob_fa <= 2){
				$borrower_info->agrmt_for = 'probation';
				return [ "perf_eff_result" => [[], []], "true_perf_eff_result" => [[], []]];
			}
			else{
				$borrower_info->agrmt_for = 'probation';
				return $this->get_csf_values($borrower_info);
				//return [$csf_values_arr, $csf_values];
			}
		}
		else{
			$borrower_info->agrmt_for = 'onboarded';
			return $this->get_csf_values($borrower_info);
			//return [$csf_values_arr, $csf_values];
		}
				
		
	}

	public function get_current_prob_fa_count($category, $prob_fas, $ap_code){
		
		if($category == 'Probation'){
			$current_prob_fa = config('app.default_prob_fas') - $prob_fas + 1;
			$not_calc_score = config('app.first_n_prob_fas_wo_score')[$ap_code];
		}else if($category == 'Condonation'){
			$current_prob_fa = config('app.default_cond_fas') - $prob_fas + 1;
			$not_calc_score = config('app.first_n_cond_fas_wo_score')[$ap_code];
		}
		return [$current_prob_fa, $not_calc_score];
	}
	public function get_csf_values($borrower_info){

			#$csf_values_return = $this->get_cust_csf_values($borrower_info);
		#}

		$csf_run_id = $borrower_info->csf_run_id;
		$run_csf_values = array();
		$perf_csf_values = $this->get_cust_perf_factors($borrower_info);
		$true_perf_csf_values = $this->get_cust_perf_factors($borrower_info, true);

		if($csf_run_id){
			$acc_number = $borrower_info->acc_number;
			$run_csf_values = $this->get_run_csf_values($csf_run_id);
		}

		$csf_values = array_merge($run_csf_values, $perf_csf_values);
		$true_csf_values = array_merge($run_csf_values, $true_perf_csf_values);


		$csf_values_arr = array();
		foreach($csf_values as $csf_value){
			$csf_values_arr[$csf_value->csf_type] = $csf_value->n_val;
			$csf_values_arr['gross_'.$csf_value->csf_type] = $csf_value->g_val; // added
		}

		$true_csf_values_arr = array();
		foreach($true_csf_values as $csf_value){
			$true_csf_values_arr[$csf_value->csf_type] = $csf_value->n_val;
			$true_csf_values_arr['gross_'.$csf_value->csf_type] = $csf_value->g_val; // added
		}

		//return [$csf_values_arr, $perf_csf_values];
		return ["perf_eff_result" => [$csf_values_arr, $csf_values], "true_perf_eff_result" => [$true_csf_values_arr, $true_csf_values]];

	}

	public function get_run_csf_values($csf_run_id){

		$cust_csf_values_repo = new CustCSFValues;

		$field_names = ['run_id'];
		$field_values = [$csf_run_id];
		$fields_arr = ['cust_score_factors'];
		$run_csf_values = $cust_csf_values_repo->get_record_by_many($field_names, $field_values, $fields_arr);
		return $run_csf_values->cust_score_factors;
	}


	private function get_cust_perf_factors($borrower_info, $true_eff_date = false){
		$perf_factors = [];
		/*$current_prob_fa = config('app.default_prob_fas') - $borrower_info->prob_fas + 1;
		if($borrower_info->agrmt_for == 'probation' && $current_prob_fa <= 2){

		}else{*/

			$factors = new Factors($borrower_info->cust_id, $borrower_info->perf_eff_date, $true_eff_date);

			$ontime_loans_pc =  $factors-> _ontime_loans_pc();
			$repaid_after_3_days_pc = $factors-> _repaid_after_N_days_pc(3);
			$number_of_advances_till_now = $factors-> _number_of_advances_till_now();
			$number_of_advances_per_quarter = $factors-> _number_of_advances_per_quarter();
			$repaid_after_10_days_pc = $factors-> _repaid_after_N_days_pc(10);
			$repaid_after_30_days_pc = $factors-> _repaid_after_N_days_pc(30);
			$avg_days_delayed_per_FA = $factors-> _avg_days_delayed_per_FA();

			$perf_factors = ['ontime_loans_pc' => $ontime_loans_pc,
										'repaid_after_3_days_pc' => $repaid_after_3_days_pc,
										'number_of_advances_till_now' => $number_of_advances_till_now,
										'number_of_advances_per_quarter' => $number_of_advances_per_quarter,
										'repaid_after_10_days_pc' => $repaid_after_10_days_pc,
										'repaid_after_30_days_pc' => $repaid_after_30_days_pc,
										'delay_days_per_fa' => $avg_days_delayed_per_FA
									];

		#}
		return Factors::normalize($borrower_info->acc_number, $borrower_info->country_code, $perf_factors);

	}

	private function check_product_eligibility_1($borrower, &$product, $calc_csf_value_arr, $validate_agreement = true){
		$agrmt = $this->get_active_agreement($borrower->current_aggr_doc_id);
		$is_eligible = false;
		$result_code = null;
		$no_data = false;
		$cust_score = 0;
		$result_configs = null;
		$prod_csf_values = null;
		$result_msg = null;

		if($borrower->prob_fas > 0){

			[$current_prob_fa, $not_calc_score] = $this->get_current_prob_fa_count($borrower->category, $borrower->prob_fas, $borrower->acc_prvdr_code);
			if($product->product_type == "probation"){

				if($current_prob_fa > $not_calc_score){
					[$no_data, $result_code, $result_configs,
					$prod_csf_values, $cust_score, $is_eligible] = $calc_csf_value_arr;
				}
				else {
					//$is_eligible = true;
					$result_code = "first_n_fas_wo_score";
				}

			}else{
				//$is_eligible = false;
				$result_code = "on_probation";
			}

			//continue;
		}
		else if($product->product_type == "probation"){
				//$is_eligible = false;
				$result_code = "probation_product";
		}else if($product->product_type == "float_vending" && in_array($borrower->biz_type, [Consts::FLOW_RM, Consts::DP_RM])){
				//$is_eligible = true;
				$result_code = "eligible";
		}
		else if($calc_csf_value_arr && sizeof($calc_csf_value_arr) > 0){
			//$common_repo->class = CSModelWeightages::class;
			[$no_data, $result_code, $result_configs,
			$prod_csf_values, $cust_score, $is_eligible] = $calc_csf_value_arr;
		}else{

			$no_data = true;
			$result_code = "no_factor_data";
		}
		
		if(config('app.validate_agreement') == true && $validate_agreement){
			Log::warning("validate_agreement");
			if($agrmt == null){

				$result_code = "no_agrmt";
				//$is_eligible = false;

			}
			// else{

			// 	$prod_ids_arr = str_getcsv($agrmt->product_id_csv);

			// 	if(!in_array($product->id, $prod_ids_arr)){

			// 		$result_code = "no_agrmt";
			// 		//$is_eligible = false;
			// 	}
			// }
		}

		[$approver_id, $approver_name, $auto_approve, $result_code, $approver_role] = $this->get_approval_details($borrower->cust_id, $product->product_type, $result_code, $borrower->perf_eff_date,$borrower->acc_prvdr_code);

		$result_msg = $this->get_result_msg($result_code, $product->cs_model_code, $product->product_type, $borrower->category);

		$product->approver_id = $approver_id;
		$product->approver_name = $approver_name;

		$split_name = explode(" ", $approver_name);
		$product->approver_first_name = $split_name[0];

		$product->is_eligible = $result_code == 'eligible' ? true : false;# $is_eligible;
		$product->cust_score = $cust_score;
		$product->result_code = $result_code;
		$product->result_msg = $result_msg;
		$product->no_data = $no_data;
		$product->result_configs = $result_configs;
		$product->prod_csf_values = $prod_csf_values;
		$product->approver_role = $approver_role;


		return $product;

	}


	private function check_product_eligibility($borrower, &$product, $csf_values_arr, $validate_agreement = true){


		$field_name = ['csf_type','repeat_cust_weightage as weightage'];
		$agrmt = $this->get_active_agreement($borrower->current_aggr_doc_id);

		$is_eligible = false;
		$result_code = null;
		$no_data = false;
		$cust_score = 0;
		$result_configs = null;
		$prod_csf_values = null;
		$result_msg = null;

		if($borrower->prob_fas > 0){

			if($borrower->category == 'Probation'){
				$current_prob_fa = config('app.default_prob_fas') - $borrower->prob_fas + 1;
				$not_calc_score = 5;
			}else if($borrower->category == 'Condonation'){
				$current_prob_fa = config('app.default_cond_fas') - $borrower->prob_fas + 1;
				$not_calc_score = 3;
			}
			if($product->product_type == "probation"){
				//$current_prob_fa = config('app.default_prob_fas') - $borrower->prob_fas + 1;
				if($current_prob_fa > $not_calc_score){
					[$no_data, $result_code, $result_configs,
					$prod_csf_values, $cust_score, $is_eligible] = $this->calc_csf_value($product->cs_model_code, $csf_values_arr);

				}
				else {
					$is_eligible = true;
					$result_code = "eligible";
				}

			}else{
				$is_eligible = false;
				$result_code = "on_probation";
			}

			//continue;
		}
		else if($product->product_type == "probation"){
				$is_eligible = false;
				$result_code = "probation_product";
		}else if($product->product_type == "float_vending" && in_array($borrower->biz_type, [Consts::FLOW_RM, Consts::DP_RM])){
				$is_eligible = true;
				$result_code = "eligible";
		}
		else if($csf_values_arr && sizeof($csf_values_arr) > 0){
			Log::warning("csf_values_arr");
			//$common_repo->class = CSModelWeightages::class;
			[$no_data, $result_code, $result_configs,
			$prod_csf_values, $cust_score, $is_eligible] = $this->calc_csf_value($product->cs_model_code, $csf_values_arr);
		}else{

			$no_data = true;
			$result_code = "no_factor_data";
		}
		Log::warning(config('app.validate_agreement'));
		Log::warning($validate_agreement);
		if(config('app.validate_agreement') == true && $validate_agreement){
			Log::warning("validate_agreement");
			if($agrmt == null){

				$result_code = "no_agrmt";
				$is_eligible = false;

			}else{

				$prod_ids_arr = str_getcsv($agrmt->product_id_csv);

				if(!in_array($product->id, $prod_ids_arr)){

					$result_code = "no_agrmt";
					$is_eligible = false;
				}
			}
		}


		[$approver_id, $approver_name, $auto_approve, $result_code, $approver_role] = $this->get_approval_details($borrower->cust_id, $product->product_type, $result_code, $borrower->perf_eff_date,$borrower->acc_prvdr_code);

		$result_msg = $this->get_result_msg($result_code, $product->cs_model_code, $product->product_type, $borrower->category);

		$product->approver_id = $approver_id;
		$product->approver_name = $approver_name;

		$split_name = explode(" ", $approver_name);
		$product->approver_first_name = $split_name[0];

		$product->is_eligible = $is_eligible;
		$product->cust_score = $cust_score;
		$product->result_code = $result_code;
		$product->result_msg = $result_msg;
		$product->no_data = $no_data;
		$product->result_configs = $result_configs;
		$product->prod_csf_values = $prod_csf_values;
		$product->approver_role = $approver_role;


		return $product;

	}

	public function calc_csf_value($cs_model_code,  $csf_values_arr, $field_name = null){

		$result_code = null;
		$no_data = false;
		$result_configs = null;
		$prod_csf_values = null;
		$is_eligible = false;

		$common_repo = new CommonRepositorySQL(CSModelWeightages::class);
		if($field_name == null){
			$field_name = ['csf_type','repeat_cust_weightage as weightage'];
		}

		$weightages = $common_repo->get_records_by('cs_model_code', $cs_model_code, $field_name);

		if(sizeof($weightages) > 0){
			// Sending $weightages as "Pass by Ref" and put the csf_values
			$cust_score = $this->calc_score($csf_values_arr, $weightages);
				//$loan_product->cs_model_code, $loan_product->product_code);

			if(is_array($cust_score)){
				$no_data = true;
				$result_code = "no_factor_data";
			}
			else
			{
				$result_code_return = $this->decide_result($cust_score, $cs_model_code);

				$cust_score = $cust_score;

				$result_code = $result_code_return[0];

				if($result_code == "eligible" ){
					$is_eligible = true;
				}

				// This is to show the csf_Values, weightags & score_result ranges on Overlay
				$result_configs = $result_code_return[1];
				$prod_csf_values = $weightages;

			}
		}else{

			$no_data = true;
			$result_code = "no_score_model";
		}

		return [$no_data, $result_code, $result_configs, $prod_csf_values, $cust_score, $is_eligible];
 	}

 	private function get_result_msg($result_code, $cs_model_code, $product_type, $category){

		if(($category == 'Probation' || $category == 'Condonation') && $result_code == 'ineligible' && $product_type == "probation"){
			return "Customer is ineligible. Customer is already on probation / condonation";
	 	}else if($result_code == "no_score_model"){
			return "Please ensure Super Admin has configured the weightages for the model :". $cs_model_code;
		}else if ($result_code == "no_agrmt"){
			return "Please create agreement with this product to apply";
		}else if($result_code == "no_result"){
			return "Please ensure that the Super Admin has configured the Eligibility Result Criteria for the model : {$cs_model_code}";
		}else if($result_code == "no_factor_data"){
			return "One or more factors not available for the customer to calculate score";
		}else if($result_code == "ineligible"){
			return "Ineligible Score";
		}else if ($result_code == "requires_flow_rm_approval"){
		    return "Low Score. This product requires additional approval from FLOW Relationship Manager : ";
		}else if ($result_code == "on_probation"){
		    return "Customer is on probation / condonation period. Can not to apply regular products";
		}else if ($result_code == "probation_product"){
		    return "Probation / condonation product is not applicable for this customer. Customer's category is {$category}";

		}else if($result_code == "rm_approvals_exhausted"){
			return "Customer has exhausted all the approval limits. If the customer is ineligible for all the products, consider allowing condonation";

		}else{
			return $result_code;
		}
 	}
 	public function decide_result($cust_score, $cs_model_code){

 		$common_repo = new CommonRepositorySQL(CSResultConfig::class);
 		$cs_result_configs = $common_repo->get_records_by('csf_model', $cs_model_code, ['score_result_code','score_from','score_to']);
 		foreach ($cs_result_configs as $cs_result_config) {
 			if($cust_score >= $cs_result_config->score_from && $cust_score <= $cs_result_config->score_to){

 				 return [$cs_result_config->score_result_code, $cs_result_configs];

 			}
 		}
 		return ["no_result", $cs_result_configs];
 	}

   public function calc_score($csf_values_arr, &$weightages){

   		$cust_cs = array();
   		$final_weightages = array();
        $this->override_cust_txn_factors($csf_values_arr, $weightages);
   		foreach ($weightages as $index => $weightage_obj) {
			$weightage = $weightage_obj->weightage;
   			if ($weightage > 0){
	   			if(isset($csf_values_arr[$weightage_obj->csf_type])){

			   			$csf_value = floatval($csf_values_arr[$weightage_obj->csf_type]);

						$un_csf_type = 'gross_'.$weightage_obj->csf_type; // added
						$csf_gross_value = intval($csf_values_arr[$un_csf_type]); // added

			   			$weightage_obj->csf_value = $csf_value;
			   			$weightage_obj->csf_gross_value = $csf_gross_value; // added
			 			$cust_cs[] = $csf_value * $weightage;
			 			$final_weightages[] = $weightage_obj;
	 			}else {
	 				return [$weightage_obj->csf_type];
	 				//thrw("* Product $prod_code is mapped with score model '$cs_model_code'. \n* This customer does not have value for  '{$weightage_obj->csf_type}'");
	 			}
 			}
   		}
   		$weightages = $final_weightages;

   		return array_sum($cust_cs);

   }
   public function loan_appl_search($criteria_array){
			
			m_array_filter($criteria_array);
			$pending_w_rm = false;
			if(isset($criteria_array['pending_w_rm'])){
				$pending_w_rm = true;
			}
            $addl_sql_condition = null;
			if(array_key_exists('req_parameter', $criteria_array)){
				$req_pram = $criteria_array['req_parameter'];
				$borrower_service = new BorrowerService($this->country_code);
				$borrowers = $borrower_service->search_borrower($req_pram);

				if(sizeof($borrowers) == 1){
					$criteria_array["cust_id"] = $borrowers[0]->cust_id;
					unset($criteria_array['req_parameter']);
				}else if (sizeof($borrowers) == 0){
					$criteria_array['loan_appl_doc_id'] = $criteria_array['req_parameter'];
					unset($criteria_array['req_parameter']);
				}else if (sizeof($borrowers) > 1){
					thrw("Please refine your search");
				}
			}
            else{
                $addl_sql_condition = $this->get_addl_search_criteria($criteria_array);
            }


		$loan_appl_repo = new LoanApplicationRepositorySQL();

		$fields_arr = ['pre_appr_id','pre_appr_ignr_reason','product_name','cust_name','cust_mobile_num','product_name','loan_principal','due_amount', 'duration', 'currency_code', 'cust_id','credit_score','loan_appl_date', 'created_at','status', 'loan_appl_doc_id', 'loan_applied_by','flow_fee_type', 'flow_fee_duration', 'flow_fee','data_prvdr_cust_id','biz_name', 'loan_doc_id','appr_reason','cs_result_code','approver_role','loan_approver_name','acc_prvdr_code', 'acc_number'];

		$loan_applns =  $loan_appl_repo->get_records_by_many(array_keys($criteria_array), array_values($criteria_array), $fields_arr ," and ", $addl_sql_condition);
		
		if($pending_w_rm == true){
			foreach($loan_applns as $loan_appln){
				$loan_appln->tot_calls_made = DB::selectOne("select count(1) tot_calls_made from voice_call_logs where JSON_CONTAINS(loan_appl_doc_ids, JSON_ARRAY(?))", [$loan_appln->loan_appl_doc_id])->tot_calls_made;

			
			}
		}
		
		return $loan_applns;

	}

    private function get_addl_search_criteria(&$criteria_array){
        $conditions = [
            "pending_w_rm" => "status = 'pending_approval'"
        ];


        $addl_sql_condition = get_sql_condition($criteria_array, $conditions);

        $valid_criterias = ['status'];
        if($addl_sql_condition == ""){
            if(sizeof($criteria_array) == 0 || sizeof(array_intersect(array_keys($criteria_array), $valid_criterias)) == 0){
                thrw("Please enter a valid search criteria");
            }
        }

        $order_condn = "order by loan_appl_date desc";

        $addl_sql_condition .= " ".$order_condn;
        return $addl_sql_condition;
    }



	public function list_approvers($data, $select_login_user = false)
	{
	   	$data['priv_code'] = Consts::PRIV_CODE_APPROVAL;

       	$data['select_login_user'] = $select_login_user;

        $common_serv = new CommonService();

        return $common_serv->get_users($data);

	}


	public function get_application($data)
	{
		$application_repo = new LoanApplicationRepositorySQL();
		$borrow_repo = new BorrowerRepositorySQL();
		$address_repo = new AddressInfoRepositorySQL();
		$lender_repo = new LenderRepositorySQL();
		$person_repo = new PersonRepositorySQL();
		$account_repo = new AccountRepositorySQL();
		$accountprovider_repo = new AccProviderRepositorySQL();
		$acc_serv = new AccountService();
		$application = $application_repo->find_by_code($data['loan_appl_doc_id']);
		$borrower = $borrow_repo->find_by_code($application->cust_id, ["biz_name","acc_number","biz_type","tot_loan_appls","tot_loans","first_loan_date","late_loans", "acc_purpose"]);
		$application->biz_type = $borrower->biz_type;
		$application->biz_name = $borrower->biz_name;
		$application->acc_number = $borrower->acc_number;
		$application->tot_loan_appls = $borrower->tot_loan_appls;
		$application->tot_loans = $borrower->tot_loans;
		$application->late_loans = $borrower->late_loans;
		$application->first_loan_date = $borrower->first_loan_date;
		$acc_prvdr = $accountprovider_repo->find_by_code($application->acc_prvdr_code, ['name']);  
		$application->acc_prvdr_name = $acc_prvdr->name;
		$lenders = $lender_repo->find_by_code($application->lender_code,  ['name']);
		$application->lender_name = $lenders->name;
		$flow_rel_mgr = $person_repo->find($application->flow_rel_mgr_id, ["id","first_name","middle_name","last_name","mobile_num"]);
		$application->flow_rel_mgr_id = $flow_rel_mgr->id;
		$application->flow_rel_mgr_mobile_num = $flow_rel_mgr->mobile_num;
		$application->flow_rel_mgr_first_name = $flow_rel_mgr->first_name;
		$application->flow_rel_mgr_first_name = $flow_rel_mgr->middle_name;
		$application->flow_rel_mgr_last_name = $flow_rel_mgr->last_name;
		$application->flow_rel_mgr_name = full_name($flow_rel_mgr);
		$dp_rel_mgr = $person_repo->find($application->dp_rel_mgr_id, ["id","first_name","middle_name","last_name","mobile_num"]);
		$application->dp_rel_mgr_id = $dp_rel_mgr->id;
		$application->dp_rel_mgr_mobile_num = $dp_rel_mgr->mobile_num;
		$application->dp_rel_mgr_first_name = $dp_rel_mgr->first_name;
		$application->dp_rel_mgr_middle_name = $dp_rel_mgr->middle_name;
		$application->dp_rel_mgr_last_name = $dp_rel_mgr->last_name;
		$application->dp_rel_mgr_name = full_name($dp_rel_mgr);
		// $account = $account_repo->getCustomerAccount($application->cust_id);
		$account = $acc_serv->get_customer_accounts(['cust_id' => $application->cust_id, 'status' => 'enabled', 'acc_purpose' => 'float_advance']);
		if($account){
			$application->acc_type = $account[0]['acc_type'];
		    $application->acc_number = $account[0]['acc_number'];
		    $application->acc_prvdr_name = $account[0]['acc_prvdr_name'];
		}else{
			thrw("Account not configured for the Customer", 1002);
		}

	   return $application;

	}


	/*private function check_for_duplicate($data){
		$loan_repo = new LoanRepositorySQL();
		$field_names = ["product_id", "cust_id", ];
		$field_values = [$data['product_id'], $data['cust_id']];
    	$fields_arr=['loan_doc_id'];
        $existing_loans = $loan_repo->get_records_by_many($field_names,$field_values,$fields_arr, " and ");
        if(sizeof($existing_loans) > 0)
        {
        	thrw("Already another FA has been disbursed for the same product with same disbursal date. Existing FA ID {$existing_loans[0]->loan_doc_id}");
        }
	}*/
	private function handle_preapproval($data, &$loan_appl){

		if(isset($data['allow_preapprove']) && $data['allow_preapprove'] == true){ // From Approve screen
			$this->check_loan_for_preapproval($data['cust_id'], (array)$loan_appl);
			$this->check_recent_late_payments($loan_appl->cust_id);
			$this->check_recent_visits($loan_appl->cust_id);
			$flow_rel_mgr_id = session('user_person_id');
            $this->insert_preapproval($loan_appl->cust_id, $flow_rel_mgr_id, false);
			
		}

		// This block will be called when this function is called from apply function. 
		//From apply function, pre_appr_id will be sent.

		if(isset($data['pre_appr_id'])){
			$pre_appr_repo = new PreApproval;
			$brwr_repo = new BorrowerRepositorySQL;
			$application_repo = new LoanApplicationRepositorySQL();
			$pre_apprls = $pre_appr_repo->find($data['pre_appr_id'], ['appr_count']);
            $addl_sql = "";
			if($pre_apprls->appr_count == 1){
				$addl_sql = ", status = 'disabled'";
			}
			DB::update("update pre_approvals set appr_count =  appr_count - 1  $addl_sql where cust_id = ? and status = ?",[$loan_appl->cust_id,'enabled']);
			$brwr_repo->increment_by_code("pre_appr_count", $loan_appl->cust_id, -1);
			$application_repo->update_model_by_code(['loan_appl_doc_id' => $data['loan_appl_doc_id'], 'pre_appr_id' => $data['pre_appr_id']]);
			$loan_appl->pre_appr_id = $data['pre_appr_id'];
		}
	} 

	public function approval($data , $with_txn = true, $send_sms = true)
	{
		
	  $loan_id = null;
	  $new_appl_status = "error";
	  $loan = null;
	  try {
	  	$with_txn ? DB::beginTransaction() : null;
		$application_repo = new LoanApplicationRepositorySQL();
		// $loan_event_repo = new LoanEventRepositorySQL();
		$loan_product_repo = new LoanProductRepositorySQL();

		//$this->check_for_duplicate($data);
		$loan_appl = $application_repo->find_by_code($data['loan_appl_doc_id']);
		
				
		$this->handle_preapproval($data, $loan_appl);

		if($loan_appl->status != Consts::LOAN_APPL_PNDNG_APPR){
			thrw("Not pending for approval. Could not {$data['action'] } the Float Advance.");
		}
		
		$credit_score = null;
		$loan_apprvd_date = null;
		$loan_appl_data = [];
		$borrower_data = [];
//		$borrower_data['pending_loan_appl_doc_id'] = null;
		$borrower_data['cust_id'] = $data['cust_id'];
		  if(in_array($data['action'], ["approve", "auto_approve", "pre_approve"])){
			  //$this->check_credit_score($data);
			$loan_appl->due_amount = $loan_appl->loan_principal + $loan_appl->flow_fee; // TODO
			$loan_appl->status = Consts::LOAN_PNDNG_DSBRSL; // pending_disbursal
			$loan_appl->loan_appl_id = $loan_appl->id;
			$credit_score = $data['credit_score'];

			$loan_appl->credit_score = $credit_score;
			if($data['action'] == "approve"){
				$loan_appl->loan_approved_date = array_key_exists('loan_apprvd_date', $data)  ? $data['loan_apprvd_date'] : datetime_db();

			}else if ($data['action'] != "pre_approve"){
				$loan_appl->loan_approver_id = null;
				$loan_appl->loan_approver_name = null;
				$loan_appl->loan_approved_date = null;
			}
				
			$loan_doc_id = loan_doc_id($data['loan_appl_doc_id']);
			$loan_appl->loan_doc_id = $loan_doc_id;
			$application_repo->update_model(['loan_doc_id' => $loan_doc_id, 'id' => $loan_appl->id]);
//			$borrower_data['ongoing_loan_doc_id'] = $loan_doc_id;
			// Using the same data in Loan Application to create Loan record
			$loan_repo = new LoanRepositorySQL();
			$loan_id = $loan_repo->create((array)$loan_appl);
			DB::update("update loans set loan_event_time = JSON_MERGE_PATCH(IFNULL(`loan_event_time`, '{}'), JSON_OBJECT('loan_appl_time',?,'loan_appr_time',?)) where id = ?",[$loan_appl->loan_appl_date,$loan_appl->loan_approved_date,$loan_id]);
			$loan_serv = new LoanService();
			$loan = $loan_serv->get_loan(array("loan_doc_id" => $loan_doc_id));
			$new_appl_status = Consts::LOAN_APPL_APPROVED; // approved
			// $loan_appl_data['loan_doc_id'] = $loan_doc_id;
            // $loan_event = $loan_event_repo->create_event($loan_doc_id,Consts::LOAN_APPL_APPROVED, null,null,$loan_appl->loan_approved_date);
			if($send_sms == true){
				$this->process_cust_conf_and_disbursal($loan_appl, $loan_doc_id);
			}
          }else  if($data['action'] == 'reject' || $data['action'] == 'cancel' ){
			if(array_key_exists("remarks", $data)){
				$loan_appl_data ['remarks'] = $data['remarks'];
			}
			//$loan_appl_data['action_reason_code'] = $data['action_reason_code'];
			if($data['action'] == 'reject'){
				$new_appl_status = Consts::LOAN_APPL_REJECTED;
			}elseif($data['action'] == 'cancel'){
				$new_appl_status = Consts::LOAN_APPL_CANCELLED;
			}
			// rejected : cancelled
		  }
		  $person_repo = new PersonRepositorySQL();

		if($data['action'] == 'pre_approve'){
			$loan_appl_data['loan_approver_name'] = $person_repo->full_name($loan_appl->flow_rel_mgr_id);
		}else{
			$loan_appl_data['loan_approver_name'] = $person_repo->full_name_by_user_id(session('user_id'));
		}
			


		  $loan_appl_data['credit_score']  = $credit_score;	// Will come as null for Cancelled status
		  $loan_appl_data["loan_approved_date"] = $loan_apprvd_date; // WIll come as null when action != approve
		  $loan_appl_data["status"] = $new_appl_status;
		  $loan_appl_data['id'] = $loan_appl->id;
		  $loan_appl_data['appr_reason'] = $data['appr_reason'];

		  $application_repo->update_model($loan_appl_data);

		  $brwr_repo = new BorrowerRepositorySQL();
		  $brwr_repo->update_model_by_code($borrower_data);
		  $result['status'] = $new_appl_status;
		  $result['loan'] = $loan;

		  if(isset($data['pre_appr_id'])){
			$pre_appr = (new PreApproval)->find($data['pre_appr_id'], ['appr_count']);
			$loan_appl->pre_appr_count = $pre_appr->appr_count;
			$this->send_pre_approved_fas_notification($loan_appl);
		  }
		  if ($loan_appl->channel == 'partner' && $new_appl_status == Consts::LOAN_APPL_REJECTED) {
			(new PartnerService)->reject_loan($loan_appl->id);
		  }
		  $with_txn ? DB::commit() : null;
		}
		catch (FlowCustomException $e) {
			$with_txn ?DB::rollback() : null;
			throw new FlowCustomException($e->getMessage());
		}
		catch (\Exception $e) {
			$with_txn ? DB::rollback() : null;
			//dd($e->getMessage(), $e->getTraceAsString());
			$result['status'] = "error";
			$result['status_message'] = $e->getMessage();
			$result['status_desc'] = $e->getTraceAsString();
			Log::warning($e->getTraceAsString());
			if ($e instanceof QueryException){
					throw $e;
				}else{
				thrw($e->getMessage());
				}
		}finally{

		}
	  return $result;
	}


	private function send_pre_approved_fas_notification($loan_appl){
		
		$brwr_repo = new BorrowerRepositorySQL;
		$person_repo = new PersonRepositorySQL;
		$borrower = $brwr_repo->find_by_code($loan_appl->cust_id, ['owner_person_id']);
        $loan_appl->photo_pps_path = get_file_path("persons", $borrower->owner_person_id, "photo_pps"); 
        $person = $person_repo->find($borrower->owner_person_id, ["photo_pps"]);
		$loan_appl->photo_pps = $person ? $person->photo_pps : null ;

		$this->send_approval_notification($loan_appl->loan_approver_id, ['notify_type' => 'pre_approved','loan'=> json_encode($loan_appl)]);
	
	}

	public function send_appl_confirmation($loan_appl)
	{
		
		//To get due date
		$duration = $loan_appl->duration;
		$due_date = getDueDate($duration);
		$otp_serv = new SMSService();
		$loan_repo = new LoanRepositorySQL;
		$borr_repo = new BorrowerRepositorySQL;
		$person_repo = new PersonRepositorySQL;
		$person_id = $borr_repo->get_record_by('cust_id', $loan_appl->cust_id, ['owner_person_id']);
	 	$cust_first_name = $person_repo->get_first_name($person_id->owner_person_id);
		[$confirm_code, $otp_id] = $otp_serv->get_otp_code(['cust_id' => $loan_appl->cust_id, 'entity' => 'loan', 'entity_id' => $loan_appl->loan_doc_id,
                                    'otp_type' => 'confirm_fa','mobile_num' => $loan_appl->cust_mobile_num,'country_code'=>session('country_code'),
									'entity_verify_col' => 'customer_consent_rcvd', 'entity_update_value' => 1]);
		
		//SMS Service
		$sms_serv = new SMSNotificationService();
		$sms_serv -> send_appl_confirmation_details(['cust_name' => $cust_first_name,
													'cust_id' => $loan_appl->cust_id,
													'cust_mobile_num' => $loan_appl->cust_mobile_num,
													'fa_amount' => number_format($loan_appl->loan_principal),
													'currency_code' => $loan_appl->currency_code,
													'country_code' => $loan_appl->country_code,
													'duration' => $loan_appl->duration,
													'sms_reply_to' => config('app.sms_reply_to')[$loan_appl->country_code],
													'confirm_code' => $confirm_code,
													'flow_fee' => number_format($loan_appl->flow_fee),
                                                    'current_date' => now()->format('d-M-Y'),
                                                    'due_amount' => number_format($loan_appl->due_amount),
													'cs_num' => config('app.customer_success_mobile')[$loan_appl->acc_prvdr_code],
                                                    'otp_id' => $otp_id,
                                                    'loan_doc_id' => $loan_appl->loan_doc_id,
													'starting_date' => date_ui(),
													'due_amount' => $loan_appl->due_amount
													
		                    					  ]);
		$first_otp_sent_time = DB::selectone('select generate_time from otps  where entity_id = ? order by generate_time asc limit 1',[$loan_appl->loan_doc_id]);
		$loan_repo->update_loan_event('first_otp_sent_time',$loan_appl->loan_doc_id,$first_otp_sent_time->generate_time);

	}

	private function get_response_msg($result){
	   $message = null;
       if(array_key_exists('loan',$result)){
            $message = "Float Advance submitted successfully.\n".confirm_code_alert(session('country_code'));
        }
       else if(array_key_exists('loan_application',$result)){

            $message = "FA application has been submitted successfully to {$result['loan_application']->loan_approver_name}";
        }
       return $message;
    }

    private function override_cust_txn_factors(&$csf_values_arr, &$weightages)
    {
            if(isset($csf_values_arr['approval'])){
                $appr_valid_upto = explode(',',$csf_values_arr['approval'])[1];
                if($appr_valid_upto == '*' || now()->lessThan(Carbon::parse($appr_valid_upto))){
                        unset($csf_values_arr['approval'], $csf_values_arr['gross_approval']);
                        $this->normalize_weightages_for_override($weightages);
                }
                else{
                    thrw("Approval Validity has expired. Please revoke approval for this account and submit statement for score calculation before applying FA");
                }
			}
    }


    private function normalize_weightages_for_override(&$weightages){
        $master_data_repo = new MasterDataRepositorySQL();
        $cust_txn_factors = collect($master_data_repo->get_records_by_many(['data_key','parent_data_code'], ['csf_type', 'cust_txn_factors'], ['data_code']))->pluck('data_code')->toArray();
        $filter_func = function($value, $key) use ($cust_txn_factors){
                            return !in_array($value->csf_type, $cust_txn_factors);
                        };

        $filtered_weightages = (collect($weightages))->filter($filter_func);
        $total_weightage = $filtered_weightages->sum('weightage');
        foreach($filtered_weightages as $weightage_obj){
            $weightage_obj->weightage =  floor($weightage_obj->weightage * (100/$total_weightage));
        }
        $norm_weightage = $filtered_weightages->sum('weightage');
        if($norm_weightage < 100){
            $filtered_weightages->last()->weightage += (100 - $norm_weightage);
        }
        $weightages = $filtered_weightages->toArray();
    }

	public function get_elig_appr_limit_amt($cust_id){
		$acc_repo = new AccountRepositorySQL();
		$account = $acc_repo->get_fa_accounts($cust_id);
		$limit_amt = 0;

		if($account) {
			if (count($account) > 1) {
				thrw('More than one FA account exists for the customer');
			}
			$account = array_shift($account);
			if ($account->acc_elig_reason) {
				$cust_csf_repo = new CustCSFValues;
				$csf_data = $cust_csf_repo->get_record_by_many(['acc_number', 'acc_prvdr_code'], [$account->acc_number, $account->acc_prvdr_code], ['conditions']);
				$conditions = $csf_data->conditions;
				if($conditions && isset($conditions->limit)) {
					$limit_amt = $conditions->limit;
				}
			}   
		}
		return $limit_amt;	
	}


    private function process_cust_conf_and_disbursal($loan_appl, $loan_doc_id)
    {
        $conf_channel = null;
        $otp_id = null;
        if($loan_appl->channel == 'cust_app' || $loan_appl->channel == 'partner'){
            $conf_channel = $loan_appl->channel;
        }
        else {
            $borrower_info = (new BorrowerRepositorySQL)->find_by_code($loan_appl->cust_id, ['last_loan_doc_id', 'temp_first_conf_code_sent']);
            $need_cust_conf = $this->need_cust_conf($loan_appl, $borrower_info);
            if ($need_cust_conf) {
                $this->send_appl_confirmation($loan_appl);
                return ;
            } else {
                $otp_id = (new LoanRepositorySQL)->find_by_code($borrower_info->last_loan_doc_id, ['conf_otp_id'])->conf_otp_id;
                $conf_channel = 'cust_otp_reuse';
            }
        }
        (new LoanService)->send_to_disbursal_queue($loan_doc_id, null, $conf_channel, $otp_id);
    }

    private function need_cust_conf($loan_appl, $borrower)
    {
        $last_loan_doc_id = $borrower->last_loan_doc_id;

        if(!$last_loan_doc_id || !$borrower->temp_first_conf_code_sent){
            return true;
        }

        $last_loan = (new LoanRepositorySQL)->find_by_code($last_loan_doc_id, ['product_id', 'cust_conf_channel']);

        $last_loan_has_otp = in_array($last_loan->cust_conf_channel, ['cust_otp', 'cust_otp_reuse']);
        $diff_prod = (new LoanProductRepositorySQL())->is_diff_products($last_loan->product_id, $loan_appl->product_id);
        if(!$last_loan_has_otp || $diff_prod){
            return true;
        }
        else{
            return false;
        }

    }


}
