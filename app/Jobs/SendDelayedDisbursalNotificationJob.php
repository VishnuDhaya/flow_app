<?php

namespace App\Jobs;

use App\Consts;
use App\Models\Person;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\OtpRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Services\Support\SMSNotificationService;
use App\SMSTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDelayedDisbursalNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $loan_appl;
    public $tries = 1;

    public function __construct($loan_appl)
    {
        $this->loan_appl = $loan_appl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_app_session($this->loan_appl['country_code']);

        $appl_repo = new LoanApplicationRepositorySQL();
        $borr_repo = new BorrowerRepositorySQL;
        $person_repo = new PersonRepositorySQL;
        $appl = $appl_repo->find_by_code($this->loan_appl['loan_appl_doc_id'], ['id','status', 'cust_id', 'cust_mobile_num', 'biz_name','cust_name']);
        $split_cust_name = explode(" ",$appl->cust_name);
        $cust_first_name = $split_cust_name[0];
        $appl->first_name = $cust_first_name;
        if(!in_array($appl->status, [Consts::LOAN_APPL_APPROVED, Consts::LOAN_APPL_PNDNG_APPR])){
            return;
        }
        elseif($appl->status == Consts::LOAN_APPL_APPROVED){
            $loan_repo = new LoanRepositorySQL();
            $loan = $loan_repo->get_record_by('loan_appl_id', $appl->id, ['loan_doc_id', 'disbursal_date', 'status']);
            if(isset($loan->disbursal_date) || $loan->status == Consts::LOAN_CANCELLED){
                return;
            }
            else{
                $otp_repo = new OtpRepositorySQL();
                $otps = $otp_repo->get_records_by('entity_id', $loan->loan_doc_id, ['id']);
                if(sizeof($otps) > 2){
                    return;
                }
            }
        }

        $notify_serv = new SMSNotificationService();
        $notify_serv->send_notification_message(['cust_name' => $appl->first_name,
                                                 'country_code' => session('country_code'),
                                                 'cust_mobile_num' => $appl->cust_mobile_num], 'FA_DELAYED_NOTIFICATION');
    }
}
