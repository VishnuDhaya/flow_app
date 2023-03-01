<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Services\Support\FireBaseService;
use Illuminate\Support\Facades\Log;
use DB;

class AuditKYC implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_app_session($this->data['country_code']);
        $agent_id = $this->data['agent_id'];
        $acc_mobile_creds = (new AccountRepositorySQL)->get_account_by(['acc_number'], [$agent_id], ['mobile_cred']);
        $messenger_serv = new FireBaseService();
        $params = ['ussd_code' => $this->data['ussd_code'], 'acc_number' =>$this->data['acc_number']];
        if (isset($acc_mobile_creds->mobile_cred->msg_token)){
		    $messenger_serv(['action' => 'audit_kyc', 'data' => json_encode($params)], $acc_mobile_creds->mobile_cred->msg_token, false);
        }
        $wait_secs = config('app.ussd_disbursal_app_process_time');
        $lead_repo = new LeadRepositorySQL();
        for($i = 0; $i < $wait_secs; ++$i) {
            //$lead_info = DB::selectOne("select id, cust_reg_json from leads where account_num = ? order by id desc limit 1", [$this->data['acc_number']]);
            $json_condition = ['account' => ['acc_number' => ['value' => $this->data['acc_number']]]];
            $lead_info = $lead_repo->get_json_by('cust_reg_json', $json_condition, ['cust_reg_json'], " AND profile_status = 'open'");
            if ($lead_info) {
                $cust_reg_arr = json_decode($lead_info->cust_reg_json, true);
                if(isset($cust_reg_arr['account']['holder_name'])){
                    break;
                }
            }
            sleep(1);
        }
    }
}
