<?php

namespace App\Jobs;

use App\Consts;
use App\Mail\FlowCustomMail;
use App\Models\StatementImport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use App\Services\Schedule\ScheduleService;
use App\Services\AccountService;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use DB;
use App\Services\AutoCapturePaymentService;
use Log;

class StatementImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $account;
    public $tries = 1;

    public function __construct($account)
    {
        $this->account = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $account = $this->account;
        set_app_session($account->country_code);
        session()->put('user_id', 0);
        $acc_prvdr_code = $account->acc_prvdr_code;
        $network_prvdr_code = $account->network_prvdr_code;

        $stmt_import = new StatementImport();
        $import_id = $stmt_import->insert_model(['account_id' => $account->id,
                                                 'acc_prvdr_code' => $acc_prvdr_code,
                                                 'status' => 'started',
                                                 'country_code' => $account->country_code,
                                                 'start_time' => Carbon::now()]);
        $storage_path = env('FLOW_STORAGE_PATH');
        $storage_path = $storage_path.DIRECTORY_SEPARATOR."files".DIRECTORY_SEPARATOR."statements";

        $args = (array)json_decode($account->web_cred);
        $args['account_id'] = $account->id;
        $args['import_id'] = $import_id;
        $args['acc_number'] = $account->acc_number;
        $args['acc_prvdr_code'] = $acc_prvdr_code;
        $args['network_prvdr_code'] = $network_prvdr_code;
        $args['storage_path'] = $storage_path;
        $args['base_path'] = base_path();
        $args['imap_email'] = env('IMAP_USERNAME');
        $args['stmt_path'] = $account->stmt_path?? null;
        $args_json = json_encode($args);
        $recon_id = 456;
       // $this->recon_n_auto_capture($account->id, $account->acc_prvdr_code, $recon_id);
        // $file_name = app_path()."/Scripts/python/vendors/stmts/{$acc_prvdr_code}_stmt_import.py";
        // $process = new Process(['python', $file_name, $args_json]);

        // $process->setTimeout(3000);

        // $process->run();

        $file_path = "vendors/stmts/{$acc_prvdr_code}_stmt_import";
        $response = run_python_script($file_path, $args_json, 3000);

        $account->screenshot_path = trim_abs_path($response['screenshot_path']);
//            $output = $process->getOutput();
//            $status = str_contains($output, 'success') ? 'imported' : 'failed';
        $import_entry = $stmt_import->find($import_id, ['status', 'exception', 'start_time']);

        if($import_entry->status == 'failed'){
//                Log::error("---------Statement Import Error - {$account->acc_prvdr_code}---------");
//                Log::error($process->getErrorOutput());
//                Log::error($output);
            $account->exception = $import_entry->exception;
            $account->failed_at = now();
            $account->import_id = $import_id;
            // if(Str::contains($account->exception,'OTP Not Received') && $acc_prvdr_code != Consts::BOK_AP_CODE){
            //     $recipients = get_ops_admin_csm_email();
            //     Mail::to($recipients)
            //       ->queue(new FlowCustomMail('util_otp_not_rcvd', ['time' => datetime_db(),
            //                                                            'purpose' => 'stmt_import',
            //                                                            'account' => (array)$account,
            //                                                            'country_code' => session('country_code')]));
            // }
            // else{
            //     Mail::to([get_l3_email(), config('app.app_support_email')])
            //         ->queue(new FlowCustomMail('stmt_import_failure', (array)$account));
            // }
            if(Str::contains($account->exception,'OTP Not Received')){

                if($acc_prvdr_code != Consts::BOK_AP_CODE){
                    Mail::to([config('app.app_support_email')])
                          ->queue((new FlowCustomMail('util_otp_not_rcvd', ['time' => datetime_db(),
                                                                               'purpose' => 'stmt_import',
                                                                               'account' => (array)$account,
                                                                               'country_code' => session('country_code')]))->onQueue('emails'));
                }else{
                    Mail::to([config('app.app_support_email')])
                    ->queue((new FlowCustomMail('stmt_import_failure', (array)$account))->onQueue('emails'));
                }
            }
            else if(Str::contains($account->exception,'Incorrect OTP') || Str::contains($account->exception,'Login failed')){
                Mail::to([config('app.app_support_email')], get_ops_admin_csm_email())->queue((new FlowCustomMail('stmt_import_failure', (array)$account))->onQueue('emails'));
            }
            else{
                Mail::to([config('app.app_support_email')])->queue((new FlowCustomMail('stmt_import_failure', (array)$account))->onQueue('emails'));
            }
            return false;
        }
        elseif($import_entry->status == 'stmt_requested') {}
        else{
            $stmt_import->update_model(['end_time' => Carbon::now(), 'id' => $import_id, 'status' => 'imported']);
            $recon_id = 123;
            $this->recon_n_auto_capture($account->id, $account->acc_prvdr_code, $recon_id);
            (new AccountService)->update_notify_acc_balance($account->id);
            (new ScheduleService)->send_email_delayed_transaction($import_id);

        }
    }



    public function recon_n_auto_capture($acc_id, $acc_prvdr_code, $recon_id){
        $schedule_serv = new ScheduleService();
        $schedule_serv->recon_debit_txns($acc_id, $acc_prvdr_code, $recon_id);
        $schedule_serv->recon_credit_txns($acc_id, $acc_prvdr_code, $recon_id);    

        (new AutoCapturePaymentService())->auto_capture($acc_id);
        
        return true;
    }
    public function failed(){

        $stmt_import = new StatementImport();

        $last_stmt_imp_record = $stmt_import->get_record_by_many(['status', 'account_id'], ['started', $this->account->id], ['id'], 'and', "ORDER BY id DESC LIMIT 1");

        if($last_stmt_imp_record){
            $stmt_import->update_model(['end_time' => Carbon::now(), 'status' => 'timed_out', 'id' => $last_stmt_imp_record->id]);
        }

    }
}
