<?php

namespace App\Console;

use App\Http\Controllers\CustApp\CustAppUserAuthController;
use App\Models\CapitalFund;
use App\Models\PreApproval;
use App\Services\ForexService;
use App\Services\Schedule\CapitalFundScheduleService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use \App\Services\Schedule\LoanStatusScheduleService;
use \App\Services\Schedule\EmailScheduleService;
use \App\Services\Schedule\AgreementScheduleService;
use \App\Services\Schedule\ActivityStatusScheduleService;
use \App\Services\Schedule\RMMetricsScheduleService;
use \App\Services\Schedule\ScheduleService;
use \App\Services\FieldVisitService;
use \App\Services\Schedule\SMSScheduleService;
use \App\Services\ReportService;
use \App\Services\Schedule\UpdateStatusDisabled;
use \App\Services\Schedule\RiskCategoryScheduleService;
use \App\Services\Schedule\PreApprovalScheduleService;
use \App\Services\Schedule\DailyMetricsScheduleService;
use \App\Services\Schedule\SystemScheduleService;
use \App\Services\Schedule\BypassReportScheduleService;
use \App\Services\Schedule\PushNotificationScheduleService;
use App\Repositories\SQL\CommonRepositorySQL;

use App\Services\Vendors\Voice\AitVoiceService;

use App\Repositories\SQL\AccountRepositorySQL;

use App\Services\Mobile\RMService;
use App\Services\RMManagementService;

use Log;
use Config;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ImapListenerCommand::class,
        \App\Console\Commands\WalletAppHeartbeat::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$common_repo = new CommonRepositorySQL();
        //$markets = $common_repo->get_markets();
        Config::set('logging.default', 'scheduler');

        $s_serv = new ScheduleService();
        $dates_arr = get_last_five_days();
        $date_str = implode(',', $dates_arr);

        $markets = $s_serv->get_markets_to_schedule();
        foreach ($markets as $market) {
                $schedule->call(
                     function($market){
                            Log::warning($market->country_code);
                            session()->put('country_code', $market->country_code);
                            setPHPTimeZone($market->time_zone);

                            session()->put('user_id', 0);
                            $serv = new ScheduleService();
                            #$serv->recon_all_acc_stmts($market->country_code);

                    }, ['market' => $market]
                    )
                            ->timezone($market->time_zone)
                            ->dailyAt('00:01');
                            #->cron('1-59/2 * * * *');


                $schedule->call(
                     function($market){
                            Log::warning($market->country_code);
                            session()->put('country_code', $market->country_code);
                            setPHPTimeZone($market->time_zone);

                            session()->put('user_id', 0);
                            $loan_statsu_serv = new LoanStatusScheduleService($market->country_code, $market->time_zone);
                            $loan_statsu_serv();

                    }, ['market' => $market]
                    )
                            ->timezone($market->time_zone)
                            ->dailyAt('00:15');              

                $schedule->call(
                     function($market){

                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                        $sms_serv = new SMSScheduleService($market);

                        $sms_serv->notify_today_due_loans();

                    },['market' => $market]

                    )
                            ->timezone($market->time_zone)
                            // ->twiceDaily(8, 15);
                            ->dailyAt('8:30');

                // $schedule->call(
                //     function($market){
                //         $sch_serv = new SMSScheduleService($market);
                //         $sch_serv->reminder_rm();
                //     },['market' => $market]
                //     )->timezone($market->time_zone)
                //      ->cron('00 09,12,15,18 * * *');


                $schedule->call(
                     function($market){

                            session()->put('country_code', $market->country_code);
                            setPHPTimeZone($market->time_zone);

                            session()->put('user_id', 0);
                            $update_status_serv = new UpdateStatusDisabled();
                            $update_status_serv->update_cust_status();



                    }, ['market' => $market]
                    )
                            ->timezone($market->time_zone)
                            ->dailyAt('00:15');
                            #->cron('1-59/2 * * * *');

                $schedule->call(
                    function($market){
                            session()->put('country_code', $market->country_code);
                            setPHPTimeZone($market->time_zone);

                            session()->put('user_id', 0);
                            $sms_serv = new SMSScheduleService($market);
                            $sms_serv->notify_tomorrow_due_loans();

                    }, ['market' => $market]
                    )
                            ->timezone($market->time_zone)
                            ->dailyAt('18:00');


                $schedule->call(
                    function($market){
                            session()->put('country_code', $market->country_code);
                            setPHPTimeZone($market->time_zone);

                            session()->put('user_id', 0);
                            $sms_serv = new SMSScheduleService($market);
                            $sms_serv->notify_overdue_loans();

                    }, ['market' => $market]
                    )
                            ->timezone($market->time_zone)
                            ->dailyAt('09:00');





                /*$schedule->call(
                    function(){
                            setPHPTimeZone($market->time_zone);
                            session()->put('country_code', $market->country_code);
                            session()->put('user_id', 0);
                            $email_serv = EmailScheduleService($market->country_code);
                            $email_serv->send_loan_reports();

                }
            )
            ->timezone($market->time_zone)
            ->dailyAt('00:15');  */

            $schedule->call(
                function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                        $agr_serv = new AgreementScheduleService($market->country_code);
                        $agr_serv->handle_expired_agrmts();

                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('00:30');

            $schedule->call(
                function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                        $activity_serv = new ActivityStatusScheduleService();
                        $activity_serv->update_activity_status();

                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('00:45');

            // $schedule->call(
            //     function($market){
            //         session()->put('country_code', $market->country_code);
            //         session()->put('user_id', 0);
            //         setPHPTimeZone($market->time_zone);
            //         $serv = new CapitalFundScheduleService();
            //         $serv->update_underused_funds();
            //     }, ['market' => $market]
            // )
            // ->timezone($market->time_zone)
            //   ->dailyAt('04:00');


              $schedule->call(
                function($market){
                    session()->put('country_code', $market->country_code);
                    session()->put('user_id', 0);
                    setPHPTimeZone($market->time_zone);
                    $serv = new ReportService(session('country_code'));
                    $serv->get_rm_distant_checkin_checkout_report([],true);
                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
             ->weeklyOn(1, '8:00');

            $schedule->call(
                function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                        $risk_categ = new RiskCategoryScheduleService();
                        $risk_categ->update_risk_type();

                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('01:00');

            $schedule->call(
                function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                        $rm_metric = new RMMetricsScheduleService();
                        $rm_metric->update_loan_appl_metrics();

                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('01:00');

            $schedule->call(
                function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                        $sys_serv = new SystemScheduleService();
                        $sys_serv->send_location_list_email();

                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->weeklyOn(1,'08:00');

            $schedule->call(
                function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                        $pre_appr_serv = new PreApprovalScheduleService();
                        $pre_appr_serv->send_pre_approved_fas_notification();

                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('18:00');

            $schedule->call(
                function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                  
                        $push_serv = new PushNotificationScheduleService();
                        $push_serv->send_visit_suggestions_notification();

                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('8:00');


            $schedule->call(
                function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                   $bypass_serv = new BypassReportScheduleService();
                   $bypass_serv->send_bypass_report_email();
                  
                   }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->weeklyOn(1, '8:00');

            $schedule->call(
                function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                   $bypass_serv = new BypassReportScheduleService();
                   $bypass_serv->send_weekly_bypass_report();
                  
                   }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->weeklyOn(1, '8:00');

            $schedule->call(
                function($market){
                    session()->put('country_code', $market->country_code);
                    session()->put('user_id', 0);
                    setPHPTimeZone($market->time_zone);
                    $serv = new SystemScheduleService(session('country_code'));
                    $serv->manual_capture_txns();
                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->weeklyOn(1, '8:00');

  
            $schedule->call(
                function ($market){
                    session()->put('country_code', $market->country_code);
                    session()->put('user_id', 0);
                    setPHPTimeZone($market->time_zone);
                    $custcontroller = new CustAppUserAuthController();
                    $custcontroller->get_language_json($market->country_code, true);

                }, ['market' => $market]
            )
                ->timezone($market->time_zone)
                ->dailyAt('8:00');
  
                $schedule->call(
                    function($market){
                            session()->put('country_code', $market->country_code);
                            session()->put('user_id', 0);
                            setPHPTimeZone($market->time_zone);
                            $push_serv = new PushNotificationScheduleService();
                            $push_serv->send_active_cust_list_notification();

                    }, ['market' => $market]
                )
                ->timezone($market->time_zone)
                ->dailyAt('8:30');

                //stmt import for other accounts
                $schedule->call(
                    function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                        $serv = new ScheduleService();
                        $serv->run_stmt_import_scripts(true);
                    }, ['market' => $market]
                )
                ->timezone($market->time_zone)
                ->everyFiveMinutes()
                ->between('08:00', '23:59')
                ->skip(function() use ($market){
                          session()->put('country_code', $market->country_code);
                          setPHPTimeZone($market->time_zone);
                        //   $sunday_run_times = ['08:00', '12:00', '16:00', '19:00', '23:30'];
                          if( now()->dayOfWeek == 0){
                              return true;
                          }
                      }
            );
            
            //schedular for udfc
            $schedule->call(
                function($market){
                    session()->put('country_code', $market->country_code);
                    session()->put('user_id', 0);
                    setPHPTimeZone($market->time_zone);
                    $serv = new ScheduleService();
                    $serv->run_stmt_import_scripts(false);
                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->everyThirtyMinutes()
            ->between('08:00', '23:59')
            ->skip(function() use ($market){
                session()->put('country_code', $market->country_code);
                setPHPTimeZone($market->time_zone);
                // $sunday_run_times = ['08:00', '12:00', '16:00', '19:00', '23:30'];
                if( now()->dayOfWeek == 0){
                    return true;
                }
            }
            );


                // only for sunday stmt import for other accounts
                $schedule->call(
                    function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                        $serv = new ScheduleService();
                        $serv->run_stmt_import_scripts(true);
                    }, ['market' => $market]
                )
                ->timezone($market->time_zone)
                ->cron("59 7,11,15,18,23 * * 0");


                //only for sunday stmt import for DFCU account
                $schedule->call(
                    function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        setPHPTimeZone($market->time_zone);
                        $serv = new ScheduleService();
                        $serv->run_stmt_import_scripts(false);
                    }, ['market' => $market]
                )
                ->timezone($market->time_zone)
                ->cron("59 7,11,15,18,23 * * 0");


                $schedule->call(
                    function($market){
                        session()->put('country_code', $market->country_code);
                        session()->put('user_id', 0);
                        $serv = new DailyMetricsScheduleService();
                        $serv->send_daily_metrics();
                    }, ['market' => $market]
                )

                ->timezone($market->time_zone)
                ->dailyAt('08:00');

               $schedule->call(
                   function($market){
                           session()->put('country_code', $market->country_code);
                           session()->put('user_id', 0);
                           setPHPTimeZone($market->time_zone);
                           $serv = new FieldVisitService();
                           $serv->process_visit_suggestions();
                   }, ['market' => $market]
               )
               ->timezone($market->time_zone)
               ->dailyAt('01:15');

                $schedule->call(
                    function($market){

                       session()->put('country_code', $market->country_code);
                       session()->put('user_id', 0);
                       setPHPTimeZone($market->time_zone);
                       $sms_serv = new SMSScheduleService($market);

                       $sms_serv->notify_today_due_loans_evening();

                   },['market' => $market]
                )
                ->timezone($market->time_zone)
                ->dailyAt('17:00');

            $schedule->call(
                function ($market) {
                    session()->put('country_code', $market->country_code);
                    session()->put('user_id', 0);
                    $country_code = $market->country_code;
                    $s_serv = new ScheduleService();
                    $s_serv->notify_stalled_disbursals();
                    // $s_serv->notify_failed_stmt_imports();
                },
                ['market' => $market]
            )
                ->between(config('app.biz_start_hour'), config('app.biz_end_hour'))
                ->everyTenMinutes();

            $schedule->call(
                function($market){
                    session()->put('country_code', $market->country_code);
                    $serv = new ScheduleService();
                    $serv->send_rm_feedback_notification();
                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('10:00');

            $schedule->call(
                function($market){
                        session()->put('country_code', $market->country_code);
                        $serv = new ScheduleService();
                        $serv->send_unknown_txn_email();

                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('08:00');
            
            
            $schedule->call(
                function($market){
    
                   session()->put('country_code', $market->country_code);
                   session()->put('user_id', 0);
                   setPHPTimeZone($market->time_zone);
    
                   (new ScheduleService)->run_internal_integrity_checks();
    
               },['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('00:00');

            $schedule->call(
                function($market){
                    session()->put('country_code', $market->country_code);
                    session()->put('user_id', 0);
                    setPHPTimeZone($market->time_zone);
                    $serv = new ScheduleService();
                    $serv->notify_acc_balance_above_threshold();
                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('23:59');


            $schedule->call(
                function($market){
                    session()->put('country_code', $market->country_code);
                    session()->put('user_id', 0);
                    setPHPTimeZone($market->time_zone);
                    $serv = new PreApprovalScheduleService();
                    $serv->disable_expired_pre_approval();
                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('23:30');
            

            $schedule->call(
                function($market){
                    session()->put('country_code', $market->country_code);
                    session()->put('user_id', 0);
                    setPHPTimeZone($market->time_zone);
                    $serv = new AitVoiceService();
                    $serv->update_cs_devices_duration();
                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('00:05');
            
            

            
            $schedule->call(
                function($market){
                    session()->put('country_code', $market->country_code);
                    session()->put('user_id', 0);
                    setPHPTimeZone($market->time_zone);
                    $serv = new RMManagementService();
                    $serv->send_mail_notify_rm_target();
                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->cron("0 10 $date_str * *");

            $schedule->call(
                function($market){
                    session()->put('country_code', $market->country_code);
                    session()->put('user_id', 0);
                    setPHPTimeZone($market->time_zone);
                    $serv = new RMService();
                    $data['punch_out'] = 'auto';
                    $serv->punch_out($data);
                }, ['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('23:00');

            $schedule->call(
                function($market){
    
                   session()->put('country_code', $market->country_code);
                   session()->put('user_id', 0);
                   setPHPTimeZone($market->time_zone);
                   $system_serv = new ScheduleService($market);
    
                   $system_serv->download_all_acc_stmts();
    
               },['market' => $market]
            )
            ->timezone($market->time_zone)
            ->monthlyOn(1, '7:00');

            $schedule->call(
                function($market){
    
                   session()->put('country_code', $market->country_code);
                   session()->put('user_id', 0);
                   setPHPTimeZone($market->time_zone);
                   $system_serv = new SystemScheduleService($market);
    
                   $system_serv->update_task_status();
    
               },['market' => $market]
            )
            ->timezone($market->time_zone)
            ->dailyAt('03:00');
        }

        $schedule->call(
            function(){
                session()->put('country_code', '*');
                session()->put('user_id', 0);
                $serv = new ForexService;
                $serv->fetch_current_forex_rates();
            }
        )
         ->timezone('GMT')
         ->dailyAt('01:00');

        $schedule->call(
            function(){
                    set_app_session('UGA');
                    session()->put('user_id', 0);
                    $serv = new ScheduleService();
                    $serv->capture_tf_repayment_transactions();
            }
        )
        ->timezone("EAT")
        ->dailyAt('23:00');

        $schedule->call(
            function(){
               set_app_session('UGA');
               session()->put('user_id', 0);
               $serv = new AitVoiceService();
               $serv->reminder_call_for_rm('lazy_rm');               
           }
        )
        ->timezone("EAT")
        ->between(config('app.biz_start_hour'), config('app.biz_end_hour'))
        ->everyFiveMinutes();


        $schedule->call(
            function($market){

               session()->put('country_code', $market->country_code);
               session()->put('user_id', 0);
               setPHPTimeZone($market->time_zone);
               $system_serv = new SystemScheduleService($market);
               $system_serv->update_repeat_queue_status();

           },['market' => $market]
        )
        ->timezone($market->time_zone)
        ->dailyAt('04:00');


        $schedule->call(
            function(){
               set_app_session('UGA');
               session()->put('user_id', 0);
               $serv = new AgreementScheduleService();
               $serv->pending_agreement_renewal_list();

           }
        )
        ->timezone("EAT")
        ->weeklyOn(1,'8:00');
                   
        $schedule->call(
            function($market){

               session()->put('country_code', $market->country_code);
               session()->put('user_id', 0);
               setPHPTimeZone($market->time_zone);
               $sms_serv = new SMSScheduleService($market);

               $sms_serv->expiring_agreement_sms($market);

           },['market' => $market]

           )
                   ->timezone($market->time_zone)
                   ->dailyAt('8:30');
                   
       
        $base_path = base_path();
        $schedule->exec("cd $base_path supervisorctl restart mail_listener:mail_listener_00")->timezone("CAT")->everyFifteenMinutes();//cron('*/20 * * * *');
        Log::warning('mail_listener started');
        
        #$app_path = app_path();
        #$schedule->exec("sh $app_path/Scripts/bash/db_backup.sh")->appendOutputTo('edx.backup.out')->cron('0 7,10,13,16,21 * * *');
        #$schedule->exec("cd $app_path && bash Scripts/bash/gds_reports.sh")->appendOutputTo('/usr/share/nginx/html/reports.log')->cron('0 4 * * *');
        #$schedule->exec("cd $app_path && python3 Scripts/python/reports/rm_weekly_report.py")->appendOutputTo('/usr/share/nginx/html/reports.log')->cron('0 1 * * 1');
        #$schedule->exec("cd $app_path && python3 Scripts/python/reports/finance_report.py")->appendOutputTo('/usr/share/nginx/html/reports.log')->cron('0 6 1 * *');


    }




    protected function shortSchedule(\Spatie\ShortSchedule\ShortSchedule $shortSchedule)
    {

        $shortSchedule->command('initiate_app_beat')
                      ->everySecond(config('app.heartbeat_interval'));

    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
