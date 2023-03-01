<?php
namespace App\Services;

use Log;
use App\Models\Task;
use App\Consts;
use Illuminate\Support\Facades\DB;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Services\Mobile\RMService;
use App\Services\RepaymentService;
use App\Services\Support\FireBaseService;
use Illuminate\Support\Facades\Mail;
use App\Mail\FlowCustomMail;


class TaskService{

    public function create_task($data){

        try{
       
            if($data['task_type'] == 'waiver_request'){
                [$task_data, $message] = $this->get_waiver_request_task_data($data);
                $task_data['remarks'] = $data['remarks'];
            }else if($data['task_type'] == 'cust_app_access'){
                [$task_data, $message] = $this->get_cust_app_req_task_data($data);
                $task_data['device_info_json'] = $data['device_info'];
            }

            $country_code = session('country_code');
            $task_data['status'] = Consts::TASK_REQUESTED;
            $task_data['task_type'] = $data['task_type'];
            $task_data['country_code'] = $country_code;
            
            $result['task_id'] = (new Task())->insert_model($task_data);
            if(isset($task_data['send_email'])) {
                $this->send_task_email_notification($task_data);
            }
            $this->send_task_push_notification($task_data['rm_id'], $data['task_type']);
            $result['message'] = $message;
        
        }catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        
        return  $result;
    }

    public function send_task_email_notification($task_data){
        
        $task_arr = json_decode($task_data['task_json'], true);
        $currency_code = (new CommonRepositorySQL())->get_currency_code(session('country_code'))->currency_code;
		$mail_data = ['country_code' => session('country_code'), 'loan_doc_id' => $task_data['loan_doc_id'], 'requested_amount' => "{$task_arr['waiver_penalty']} {$currency_code}" ,
					 'requested_on' => date_ui(), 'os_penalty' => "{$task_arr['os_penalty']} {$currency_code}", 'penalty_days' => $task_arr['penalty_days']];
        Mail::to(get_ops_admin_email())->queue((new FlowCustomMail('waiver_request_notification',$mail_data))->onQueue('emails'));

    }

    private function send_task_push_notification($rm_id, $task_type){
        $serv = new FireBaseService();
        [$email, $messenger_token] = (new PersonRepositorySQL)->get_email_n_msgr_token($rm_id);
        if(isset($messenger_token)){
            $data['notify_type'] = $task_type;
		$serv->send_message($data, $messenger_token);
        }
    }

    private function get_waiver_request_task_data($data){
        
        $country_code = session('country_code');

        $task = (new Task())->get_record_by_many(['loan_doc_id', 'status'], [$data['loan_doc_id'], Consts::TASK_REQUESTED], ['id']);

        if(isset($task->id)){
            thrw("Already a waiver requested for this FA");
        }

        $approvers = config('app.task_approvers');
        
        $approval_data = array();

        $loan = (new LoanRepositorySQL())->find_by_code($data['loan_doc_id'], ['flow_rel_mgr_id', 'due_date', 'provisional_penalty','penalty_days']);
        

        foreach ($approvers as $approver){
            if($approver == 'relationship_manager'){
                $approver_id = $task_data['rm_id'] = $loan->flow_rel_mgr_id;
                $approver_name = $rm_name =  (new PersonRepositorySQL)->full_name($approver_id);
                $approval_data[] = ['person_id' => $approver_id, 'approved' => false , 'approver_name' => $approver_name];
            }
            else if ($loan->penalty_days > 5 && $approver == 'ops_admin'){
                $app_user = DB::selectOne("select person_id from app_users where role_codes = '{$approver}' and status = 'enabled' and country_code = '{$country_code}'");
                $approver_id = $app_user->person_id;
                $approver_name =  (new PersonRepositorySQL)->full_name($approver_id);
                $approval_data[] = ['person_id' => $approver_id, 'approved' => false, 'approver_name' => $approver_name];
                $task_data['send_email'] = true;
            }
        }

        $task_arr['waiver_penalty'] =  $data['waived_penalty'];
        $task_arr['mode'] = 'capture';
        $task_arr['os_penalty'] = $data['new_outstanding_penalty'];
        $task_arr['penalty_days'] =  $loan->penalty_days;
        $task_data['approval_json'] = json_encode($approval_data);
        $task_data['loan_doc_id'] = $data['loan_doc_id'];
        $task_data['cust_id'] = $data['cust_id'];
        $task_data['task_json'] = json_encode($task_arr);

        $message = "Waiver request submitted to {$rm_name} successfully.";

        return  [$task_data, $message];

    }

    public function list_tasks($data){
        
        $person_id = session('user_person_id');
        $json_condition = ['person_id' => $person_id];
        $task_type = $data['task_type'];
        $tasks = (new Task)->get_jsons_by('approval_json', $json_condition, ['task_type', 'task_json', 'approval_json', 'device_info_json', 'status', 'loan_doc_id', 'cust_id', 'created_at'], $addl_sql = "and task_type = '{$task_type}' and status = 'requested'");
        (new RMService)->get_cust_photo_and_location($tasks);
       
        foreach ($tasks as $task){   
            
            $task->last_loan = (new BorrowerRepositorySQL)->get_last_loan($task->cust_id, ['loan_principal','disbursal_date']);
        
            $accounts = (new AccountRepositorySQL)->get_account_by(['cust_id', 'status', 'acc_purpose'], [$task->cust_id, 'enabled', 'float_advance'], ['acc_prvdr_code', 'acc_number']);
            if($accounts){
                $task->acc_number = $accounts->acc_number;
                $task->acc_prvdr_code = $accounts->acc_prvdr_code;
                $ap_logo_arr = config('app.acc_prvdr_logo')[session('country_code')];
                $task->ap_code_path = $ap_logo_arr[$task->acc_prvdr_code];
            }
            
            $task->approval_json = json_decode($task->approval_json);
            $task->dvic_info = [json_decode($task->device_info_json)];
            $task->task_json = json_decode($task->task_json);
            if($task->task_type == "cust_app_access"){
                unset($task->task_json, $task->loan_doc_id, $task->last_visit_ago, $task->last_loan, $task->device_info_json);
            }
        }
        return $tasks;
    }

    public function task_approval($data){


        try{

			DB::beginTransaction(); 

            $upgrade_req = new Task();

            $person_id = session('user_person_id');
            $update_arr = ['id'=> $data['task_id']];


            $task = $upgrade_req->find($data['task_id'], ['loan_doc_id', 'approval_json', 'cust_id', 'task_type', 'task_json']);

            if($data['action'] == 'approve'){

                $approval_json_arr = json_decode($task->approval_json, true);
                $task_arr = json_decode($task->task_json, true);
                $appr_count = 0;

                foreach ($approval_json_arr as &$approval_json){
                    if($person_id == $approval_json['person_id']){
                        if($approval_json['approved']){
                            thrw("You have already approved this ".dd_value($task->task_type));
                        }else{
                            $approval_json['approved'] = true;
                            $approval_json['approved_date'] = date_db();
                        }
                    }

                    if($approval_json['approved']){
                        $appr_count = $appr_count+1;
                    }
                }

                $update_arr['approval_json'] = json_encode($approval_json_arr);   

                if(sizeof($approval_json_arr) == $appr_count){
                    $update_arr['status'] = consts::TASK_APPROVED;
                    if($data['task_type'] == 'waiver_request'){
                        $penalty_data['new_outstanding_value'] = $task_arr['os_penalty'];
                        $penalty_data['mode'] = $task_arr['mode'];
                        $penalty_data['waived_amount'] = $task_arr['waiver_penalty'];
                        $penalty_data['loan_doc_id'] = $task->loan_doc_id;
                        $penalty_data['waiver_field'] = 'penalty';
                        (new RepaymentService())->update_waiver($penalty_data, $with_txn = false);
                    }elseif($data['task_type'] == 'cust_app_access'){
                        $status = 'enabled';
                        $cust_id = $task->cust_id;
                        (new BorrowerService)->set_cust_app_access($cust_id, $status);
                    }
                }

                $status = "approved";

            }else if($data['action'] == 'reject'){
                $update_arr['status'] = $status = consts::TASK_REJECTED; 

            }
            $resp['is_updated'] = $upgrade_req->update_model($update_arr);

            $resp['status'] = $status;
            
            DB::commit();

            return $resp;

    }
    catch (Exception $e) {
        DB::rollback();
        throw new FlowCustomException($e->getMessage());
    }
    
    }

    public function get_cust_app_req_task_data($data){
 
        $task = (new Task()) -> get_record_by_many(['cust_id', 'status'], [$data['cust_id'], Consts::TASK_REQUESTED], ['id']);
        $approvers = config('app.task_approvers');

        if(!isset($task)){
            foreach ($approvers as $approver){
                if($approver == 'relationship_manager'){
                    $approver_id = $task_data['rm_id'] = $data['rm_id'];
                    $approver_name = $rm_name =  (new PersonRepositorySQL) -> full_name($approver_id);
                    $approval_data[] = ['person_id' => $approver_id, 'approved' => false , 'approver_name' => $approver_name];
                }
            }

                $task_data['cust_id'] = $data['cust_id'];
                $task_data['approval_json'] = json_encode($approval_data);
                $message = "Customer app request is submitted to {$rm_name} successfully.";
                return  [$task_data, $message];
        }else{
           thrw("The request for the customer app access has been submitted already");
        }   
    }


}