<?php
namespace App\Services\Schedule;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Consts;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Models\CustAgreement;
use App\Mail\EmailAgreementsExpiry;
use Mail;
use App\Services\RecordAuditService;
use App\Repositories\SQL\BorrowerRepositorySQL;
use Illuminate\Support\Facades\Session;
use App\Mail\FlowCustomMail;

class AgreementScheduleService {

	// public function __construct(){
	// $this->country_code = 'UGA';
	// 	//session()->put('country_code','UGA');
 //        //$this->country_code = session('country_code');
 //        $this->handle_expired_agrmts();        
 //    }

	 public function __invoke(){
        print_r("calling..");
        $this->country_code = 'UGA';
        session()->put('country_code','UGA');
        $this->handle_expired_agrmts();
        // $this->process_due_loans();
    }


    public function handle_expiring_agrmts(){
    	try
    	{
	        DB::beginTransaction();
	    	$today = Carbon::yesterday()->endOfDay();
	    	$date = Carbon::yesterday()->endOfDay()->addDays(3);
	    	$expiring_agreements = DB::select("select id, aggr_doc_id, cust_id, valid_upto from cust_agreements where valid_upto <= ? and valid_upto > ? and country_code = ?", [$date, $today, session('country_code')]);
	    	  
	    	foreach($expiring_agreements as $expiring_agreement){
	    		$this->send_mail($expiring_agreement, "expiring");
	    	}
	    	DB::commit();
    	}
    	catch (\Exception $e) {
	        DB::rollback();
	        if ($e instanceof QueryException){
	            throw $e;
	        }else{
	            thrw($e->getMessage());
	        }
	    }	

    }

    public function handle_expired_agrmts(){
		try
    	{
	        DB::beginTransaction();
			$today = Carbon::yesterday()->endOfDay();
			$country_code = session('country_code');

			$expired_agreements = DB::select("select id, aggr_doc_id, cust_id, valid_upto from cust_agreements where valid_upto <= '{$today}' and status != 'inactive' and duration_type = 'days' and country_code = '{$country_code}'");
			
			foreach($expired_agreements as $expired_agreement){					
				//DB::update("update cust_agreements set status = 'inactive' where id = ?", [$agreement->id]);
				
				$common_repo = new CommonRepositorySQL(CustAgreement::class);
				$record_serv = new RecordAuditService();
				$borr_repo = new BorrowerRepositorySQL();
				$common_repo->update_record_status('inactive', $expired_agreement->id);

				DB::update("update borrowers set aggr_status = 'inactive', status = 'disabled', current_aggr_doc_id = null, updated_at = ? , updated_by = ? where current_aggr_doc_id = ? and cust_id = ?", [datetime_db(), session('user_id'),$expired_agreement->aggr_doc_id, $expired_agreement->cust_id]);
				// $this->send_mail($expired_agreement, "expired");
				$borrower = $borr_repo->get_record_by('cust_id', $expired_agreement->cust_id, ['id']);

				$record = ['borrowers' => [
					'status' => 'disabled',
					'status_reason' => 'agreement_expired',
					'cust_id' => $expired_agreement->cust_id,
					'id' => $borrower->id,
					'remarks' => 'agreement_expired'    ]
				  ];                                
				$record_serv->audit_borrower_status_change($record);
			}	
			DB::commit();
    	}
    	catch (\Exception $e) {
	        DB::rollback();
	        if ($e instanceof QueryException){
	            throw $e;
	        }else{
	            thrw($e->getMessage());
	        }
	    }	
	}

    public function send_mail($data, $expiry_state){

    	$result = DB::selectOne("select flow_rel_mgr_id, owner_person_id, acc_number from borrowers where cust_id = ?", [$data->cust_id]);
		
		$person_repo = new PersonRepositorySQL();
        $rel_mgr_name = $person_repo->full_name($result->flow_rel_mgr_id);
        $cust_name = $person_repo->full_name($result->owner_person_id);
		$email_id = DB::selectOne("select email_id from persons where id = ? and status = ?", [$result->flow_rel_mgr_id, "enabled"]);

		$mail_data = ["country_code" => session('country_code'),
					  "acc_number" => $result->acc_number,
					  "flow_rel_mgr_name" => $rel_mgr_name,
					  "aggr_doc_id" => $data->aggr_doc_id,
					  "cust_name" => $cust_name,
					  "valid_upto" => $data->valid_upto,
					  "to_email_id" => $email_id->email_id
					];
		Mail::to($email_id->email_id)->send(new EmailAgreementsExpiry($mail_data, $expiry_state));
		
    }

	public function pending_agreement_renewal_list(){
		$curr_date = Carbon::now()->format("Y-m-d");
        $date_to_expiring = Carbon::now()->addDays(7);
		$date_to_expired = Carbon::now()->subDays(7);
		$country_code=session("country_code");
		$sql="select b.cust_id 'CUST ID' ,b.aggr_valid_upto 'AGGR VALID UPTO', b.activity_status 'ACTIVITY STATUS' ,concat(first_name,' ',last_name) 'RM NAME' from borrowers b, persons p where p.id = b.flow_rel_mgr_id  and b.activity_status='active' and b.country_code='{$country_code}' ";
    	$expiring_results = DB::select("$sql and aggr_status= ? and aggr_valid_upto <= ? and b. status=? and category !=?",["active",$date_to_expiring,"enabled","Probation"]);
    	$expired_results = DB::select("$sql and aggr_status= ? and aggr_valid_upto >= ? and b.status=?",["inactive",$date_to_expired,"disabled"]);
	       
			$empty_head = [ 0 => ["CUST ID" => "","AGGR VALID UPTO" => "","ACTIVITY STATUS" => "","RM NAME" => ""]];
			$expirying_head = [0 => ["CUST ID" => " ","AGGR VALID UPTO" => " Will be expired","ACTIVITY STATUS" => "","RM NAME" => ""]];
			$expired_head = [0 => ["CUST ID" => "","AGGR VALID UPTO" => " Expired","ACTIVITY STATUS" => "","RM NAME" => ""]];

			$result['data'] = array_merge($expirying_head,$expiring_results,$empty_head,$empty_head,$expired_head,$expired_results);
            $result['file_name'] = "expiring_agreements";
            $result = json_encode($result);
            $resp = run_python_script('array_to_csv',$result);

			if($expiring_results || $expired_results){
			  Mail::to(get_ops_admin_email())->queue((new FlowCustomMail('expiring_agreements', ["filename" => "expiring_agreements.csv","country_code"=> $country_code]))->onQueue('emails'));
			}
			
		}

}