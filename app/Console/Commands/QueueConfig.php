<?php

namespace App\Console\Commands;

use App\Repositories\SQL\AccountRepositorySQL;
use App\Services\AccountService;
use App\Services\Schedule\ScheduleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Log;
use App\Consts;

class QueueConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure workers for each queue in supervisor configuration file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $queues = [['sms',200],['default',200],['emails',200]];
        $stmts = [];
        $disbs = [];

        $markets = (new ScheduleService)->get_markets_to_schedule();

        foreach($markets as $market){

            set_app_session($market->country_code);
            $accounts = (new AccountRepositorySQL())->get_recon_accounts(['acc_number', 'acc_prvdr_code', 'acc_purpose', 'web_cred', 'disb_int_type', 'stmt_int_type']);

            foreach($accounts as $account){
                $prefix = null;
                $timeout = (json_decode($account->web_cred, true))['timeout'];

                if($account->stmt_int_type == 'web'){

                    if($account->disb_int_type == 'web' && in_array('disbursement', $account->acc_purpose)){
                        if(is_single_session_ap($account->acc_prvdr_code)){
                            $prefix = "DISB_STMT";          // if the account is single session then both disbursement & statement will go the single queue 
                        }else{
                            $prefix = "DISB";               // DISB is for all Disbursement Accounts
                        }
                    }else if(is_single_session_ap($account->acc_prvdr_code)){
                        
                        $prefix = "STMT";                   //STMT is for all Repayment Accounts (currenty for MTN)
                    }

                    if($prefix){
                        $queue = "{$prefix}_{$account->acc_prvdr_code}_{$account->acc_number}_{$account->stmt_int_type}";
                        $queues[] = [$queue,$timeout];
                        $prefix == 'STMT' ? $stmts[] = $queue : $disbs[] = $queue;
                    }
                    
                }
                
                if($account->disb_int_type == 'mob' || $account->disb_int_type == 'api'){     // if the integration type is different, it will go the separate queue

                    $queue = "DISB_{$account->acc_prvdr_code}_{$account->acc_number}_{$account->disb_int_type}";
                    $queues[] = [$queue, $timeout];
                    $disbs[] = $queue;
                }
            }

            $kyc_name_retrieval_accs = (new AccountRepositorySQL())->get_accounts_by(["acc_purpose"], ["ussd"], ["acc_number"]);
            
            foreach($kyc_name_retrieval_accs as $kyc_name_retrieval_acc){
                $queue = "USSD_{$kyc_name_retrieval_acc->acc_number}";
                $queues[] = [$queue, 120];
            }
        }
        $base_path = base_path();
        $config_path = env('SUPERVISOR_CONFIG');
        $stmt_str = implode(',',$stmts);
        $disb_str = implode(',',$disbs);

        $text = "
[supervisord]
environment=APP_PATH=\"{$base_path}\",COMMAND=\"php artisan queue:work\"

[group:statement]
programs={$stmt_str}

[group:disbursal]
programs={$disb_str}

";
        foreach($queues as $queue){
            $text.= "

[program:{$queue[0]}]
command=/bin/bash -c \"cd \$APP_PATH && \$COMMAND --timeout {$queue[1]} --queue %(program_name)s\"
user=nginx
autostart=true
autorestart=true
startretries=5
numprocs=1
startsecs=0
process_name=%(program_name)s_%(process_num)02d
stdout_logfile={$base_path}/storage/logs/supervisor/%(program_name)s.log
redirect_stderr=true
stopasgroup=true

";
        }
        $text.="[program:mail_listener]
command=/bin/bash -c \"cd \$APP_PATH && php artisan mail:listen\"
user=nginx
autostart=true
autorestart=true
startretries=5
numprocs=1
startsecs=0
process_name=%(program_name)s_%(process_num)02d
stdout_logfile={$base_path}/storage/logs/supervisor/%(program_name)s.log
redirect_stderr=true
stopasgroup=true";

        $text.="
        
[program:short_schedule]
command=/bin/bash -c \"cd \$APP_PATH && php artisan short-schedule:run --lifetime=60\"
user=nginx
autostart=true
autorestart=true
startretries=5
numprocs=1
startsecs=0
process_name=%(program_name)s_%(process_num)02d
stdout_logfile={$base_path}/storage/logs/supervisor/%(program_name)s.log
redirect_stderr=true
stopasgroup=true";

        File::put($config_path, $text);
    }
}
