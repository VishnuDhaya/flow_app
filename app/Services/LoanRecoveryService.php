<?php

namespace App\Services;
use App\Models\LoanRecovery;
use App\Consts;
use Carbon\Carbon;
use App\SMSTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Support\SMSService;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Services\Support\SMSNotificationService;


class LoanRecoveryService{

    public function create_recovery_request($data)
    {
        $loan_recovery = $data['loan_recovery'];
        $repo = new LoanRecovery();
        $loan_repo = new LoanRepositorySQL();
        $loan = $loan_repo->find_by_code($loan_recovery['loan_doc_id'],['current_os_amount']);

        if($loan_recovery['amount'] > $loan->current_os_amount){
            thrw("You cannot receive cash more than the current outstanding amount");
        }

        $loan_recovery['status'] = Consts::RCVRY_INIT;
        $loan_recovery['rs_id'] = session('user_person_id');
        $loan_recovery['country_code'] = session('country_code');

        $rec_id = $repo->insert_model($loan_recovery);
        $loan_recovery['id'] = $rec_id;
        $this->send_recovery_confirmation($loan_recovery);

    }

    public function send_recovery_confirmation($loan_recovery)
    {
        $loan_repo = new LoanRepositorySQL();
        $loan = $loan_repo->find_by_code($loan_recovery['loan_doc_id']);
        $otp_serv = new SMSService();
		[$confirm_code, $otp_id] = $otp_serv->get_otp_code(['cust_id' => $loan_recovery['cust_id'], 'entity' => 'loan_recovery', 'entity_id' => $loan_recovery['id'],
                                    'otp_type' => 'confirm_recovery','mobile_num' => $loan->cust_mobile_num,'country_code'=>session('country_code'),
                                    'entity_verify_col' => 'status', 'entity_update_value' => Consts::RCVRY_CNFM]);

		$sms_serv = new SMSNotificationService();
		$sms_serv -> send_confirmation_message(['cust_name' => $loan->cust_name,
													'cust_id' => $loan->cust_id,
													'recovery_amount' => $loan_recovery['amount'],
													'rec_date' => Carbon::now(),
													'cust_mobile_num' => $loan->cust_mobile_num,
													'currency_code' => $loan->currency_code,
													'country_code' => $loan->country_code,
													'sms_reply_to' => config('app.sms_reply_to')[$loan->country_code],
													'confirm_code' => $confirm_code,
                                                    'otp_id' => $otp_id,
                                                    'loan_doc_id' => $loan->loan_doc_id,
                                                    'cs_num' => config('app.customer_success_mobile')[$loan->acc_prvdr_code],
                                                    'purpose' => 'otp/confirm_recovery'
		                    					  ],
                                                    SMSTemplate::RCVRY_CONFIRM_MSG);

    }

    public function update_recovery_status($rec_id)
    {
        $repo = new LoanRecovery();
        $recovery = $repo->get_record_by('id',$rec_id);
        if ($recovery->status == Consts::RCVRY_INIT){
            $loan_repo = new LoanRepositorySQL();
            $sms_serv = new SMSNotificationService();
            $person_repo = new PersonRepositorySQL();

            $repo->update_record_status(Consts::RCVRY_CNFM,$rec_id);
        }


    }

    public function check_ongoing_recovery($data){
        $status_condition = " and status in ('".Consts::RCVRY_INIT."','".Consts::RCVRY_CNFM."')";
        $repo = new LoanRecovery();
        $records = $repo->get_records_by_many(['loan_doc_id'], [$data['loan_doc_id']], ['id','amount','rs_id','created_at','status'], "and", $status_condition);
        if(empty($records)){
            return ['ongoing'=> false];
        }
        else{
            $recovery_data = $records[0];
            $person_repo = new PersonRepositorySQL();
            $rs_data = $person_repo->get_person_name($recovery_data->rs_id);
            $recovery_data->rs_name = $rs_data->first_name." ".$rs_data->last_name;
            $recovery_data->ongoing = true;
            $recovery_data->otp_info = get_recovery_otp_info(session('country_code'));
            return $recovery_data;
        }
    }

    public function cancel_ongoing_recovery($data)
    {
        $repo = new LoanRecovery();
        Log::warning($data);
        $recovery = $repo->get_record_by('id',$data['rec_id']);
        if($recovery->status != Consts::RCVRY_INIT){
            thrw("Unable to cancel. Customer has confirmed the repayment. Please refresh the page");
        }
        $repo->update_record_status(Consts::RCVRY_CNCL, $data['rec_id']);

    }

    public function list_recoveries()
    {
        $country_code = session('country_code');
        $status_condition = "";
        if(auth()->user()->role_codes == 'recovery_specialist'){
            $status_condition = " status in ('".Consts::RCVRY_INIT."','".Consts::RCVRY_CNFM."')";
        }
        $repo = new LoanRecovery();
        $records = $repo->get_records_by_many([], [], ['cust_id', 'biz_name', 'loan_doc_id', 'amount', 'rs_id', 'created_at', 'status'], "and", $status_condition);
        foreach ($records as $record){
            $person_repo = new PersonRepositorySQL();
            $rs_data = $person_repo->get_person_name($record->rs_id);
            $record->rs_name = $rs_data->first_name." ".$rs_data->last_name;
        }

        return ['results' => $records];
    }

    public function capture_recovery($data)
    {
        $repo = new LoanRecovery();
        $loan_repo = new LoanRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $sms_serv = new SMSNotificationService();
        $repo->update_record_status(Consts::RCVRY_RCRD, $data['rec_id']);

        $recovery = $repo->get_record_by('id',$data['rec_id']);
        $loan = $loan_repo->find_by_code($recovery->loan_doc_id);

        $rs_data = $person_repo->get_person_name($recovery->rs_id);
        $rs_name = $rs_data->first_name." ".$rs_data->last_name;
        $sms_serv->send_notification_message(['cust_name' => $loan->cust_name,
                                              'currency_code' => $loan->currency_code,
                                              'recovery_amount' => $recovery->amount,
                                              'cust_mobile_num' => $loan->cust_mobile_num,
                                              'country_code' => $loan->country_code,
                                              'rs_name' => $rs_name
                                             ], SMSTemplate::RCVRY_RECORDED_MSG);


    }

}