<?php
namespace App\Services\Schedule;

use App\Models\PreApproval;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;
use App\Services\Support\FireBaseService;
use App\Models\FlowApp\AppUser;
use App\Mail\FlowCustomMail;
use App\Repositories\SQL\LoanRepositorySQL;




class PreApprovalScheduleService{

    public function send_pre_approved_fas_notification(){
       
        $pre_appr_repo =  new PreApproval;
        $loan_repo = new LoanRepositorySQL;

        $sql_query = "select distinct(loan_approver_id) from loans where pre_appr_id is not null and date(disbursal_date) = ? and country_code = ?";
        $fields_arr = [date_db(), session('country_code')];
        $pre_appr_loans = DB::select($sql_query, $fields_arr);
       
        foreach($pre_appr_loans as $pre_appr_loan){
            
            $loan_approver_id = $pre_appr_loan->loan_approver_id;
            $field_names = ['loan_approver_id','date(disbursal_date)'];
            $field_values = [$loan_approver_id,date_db()];
            $addl_sql = "and pre_appr_id is not null";
            $fields_arr = ["cust_name", "cust_mobile_num", "loan_doc_id", "cust_id", "loan_principal", "flow_fee", "duration", "loan_principal", "flow_fee", "duration", "loan_appl_date"];
            $loans =  $loan_repo->get_records_by_many($field_names,$field_values,$fields_arr,"and",$addl_sql);
            
            $this->send_pre_approved_fas_push_notification($loan_approver_id);
            $this->send_pre_approved_fas_email_notification($loan_approver_id, $loans);
        }
        

        
    }

    private function send_pre_approved_fas_email_notification($approver_id, $loans){

        $email = AppUser::where('person_id',$approver_id)->get(['email'])->pluck("email")[0];
        $appr_mail_data = ['country_code' => session('country_code'),'loans' => $loans,'date' => date_ui()];
		Mail::to($email)->queue((new FlowCustomMail('pre_approved_fas', $appr_mail_data))->onQueue('emails'));


    }

    private function send_pre_approved_fas_push_notification($approver_id){
        
        try{
            
            $serv = new FireBaseService();
            $messenger_token = AppUser::where('person_id',$approver_id)->get(['email','messenger_token'])->pluck("messenger_token")[0];
            $data['notify_type'] = 'pre_approved_fas';
            $serv($data, $messenger_token, false);
            
        }catch(\Exception $e){
            $exp_msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            Log::error($exp_msg);
            Log::error($trace);
        }
    
    }

    public function disable_expired_pre_approval(){

        DB::update("update pre_approvals set status = 'disabled' where status = 'enabled' and date(appr_exp_date) = ?", [date_db()]);       

    }

}
