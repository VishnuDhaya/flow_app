<?php
namespace App\Services;

use App\Mail\FlowCustomMail;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Services\Support\FireBaseService;
use App\Models\HeartBeat;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Support\Facades\Mail;
class HeartBeatService{

    public static function beat(){
        $accounts = DB::select("select id, acc_number, mobile_cred from accounts where status = 'enabled' and JSON_EXTRACT(mobile_cred, '$.msg_token') is not null and JSON_EXTRACT(mobile_cred, '$.heartbeat') = 'enabled' and country_code = ?", [session('country_code')]);
        foreach($accounts as $account){
            try{
                self::check_health_condition($account);
                self::send_heartbeat_pulse($account); 
            }
            catch(\Exception $e){
                $err_msg = $e->getMessage();
                Log::error($err_msg);
                Log::error($e->getTraceAsString());

                $country_code = session('country_code');
        
                $env = get_env();
                $subject = "[$country_code$env] - ";
                Mail::send([], [], function ($message) use ($err_msg, $subject, $account) {
                    $message->to('eben@inbox.flowglobal.net');
                    $message->subject($subject .= "Error in Heartbeat Process - {$account->acc_number}");
                    $message->setBody("The heartbeat process for {$account->acc_number} has run into an error.<br>Raise issue to L2 support and have them investigate and fix the issue based on the error message given below", 'text/html');
                    $message->addPart("<pre>{$err_msg}</pre>", 'text/html');
                });
            }
        }
    }


    private static function check_health_condition($account){
        $recent_beat_count = config('app.heartbeats_to_monitor');
        $beats_count = (DB::selectOne("select count(id) count from heartbeats where date(sent_at) = curdate() and account_id = ? order by id desc", [$account->id]))->count;
        if($beats_count < $recent_beat_count){
            return;
        }
        $result = DB::selectOne("select avg(TIMESTAMPDIFF(second, sent_at, received_at)) avg_resp_time, sum(if(received_at is null, 1, 0)) no_resp_count
                                         from (select sent_at, received_at from heartbeats where 
                                                account_id = ? order by id desc limit ?) p", 
                                        [$account->id, $recent_beat_count]);

        $poor_availability = ($result->no_resp_count > ($recent_beat_count / 2) || $result->avg_resp_time > config('app.wallet_app_max_resp_time'));
        $notify_events = DB::select("select id from events 
                                     where type = 'wallet_app_outage_notify' and entity = 'account' and 
                                     entity_id = ? and event_time > ?", 
                                     [$account->id, now()->subMinutes(config('app.wallet_app_outage_notify_interval'))]);

        $can_notify = sizeof($notify_events) == 0;

        if($can_notify && $poor_availability && session('country_code') == 'UGA'){
            $notification = "Heartbeat - {$account->acc_number} - Not receiving responses for FCM messages ";
            send_event_notification(['type' => 'wallet_app_outage_notify', 'entity' => "account", 'interval' => config('app.wallet_app_outage_notify_interval'), 'entity_id' => $account->id, 'group_id' => config('app.whatsapp_group_codes'), 'notification' => $notification, 'channel' => 'whatsapp']);
        }
        
    }

    private static function send_heartbeat_pulse($account){
        $heartbeat_token = uniqid();
        $data = ['action' => 'heartbeat', 'heartbeat_token' => $heartbeat_token, 'account_id' => (string)$account->id];
        $mobile_cred = json_decode($account->mobile_cred);
        (new FireBaseService)->send_message($data, $mobile_cred->msg_token);
        DB::table('heartbeats')->insert(['account_id' => $account->id, 'token' => $heartbeat_token, 
                                         'sent_at' => now(), 'country_code' => session('country_code')]);
    }

    public static function receive_heartbeat_pulse($data){
        $rcvd_at = now();
        $beat = DB::selectOne("select id from heartbeats where account_id = ? and token = ? and date(sent_at) = ? and country_code = ?", [$data['account_id'], $data['heartbeat_token'], today(), session('country_code')]);
        DB::update("update heartbeats set received_at = ? where id = ?", [$rcvd_at, $beat->id]);
    }


    public static function configure_heartbeat($account_ids, $enable = true){
        $ids = implode(',', $account_ids);
        $status = $enable ? 'enabled' : 'disabled';
        DB::update("update accounts set mobile_cred = JSON_MERGE_PATCH(IFNULL(mobile_cred, '{}'), '{\"heartbeat\":\"$status\"}') where id in ($ids)");
    }

}