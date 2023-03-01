<?php

namespace App\Http\Middleware;

use App\Consts as AppConsts;
use App\Models\RMActivityLogs;
use App\Models\RMPunchTime;
use Closure;
use Response;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Services\Mobile\RMService;
use Carbon\Carbon;
use App\Consts;

class RMActivityLogMiddleware{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next){
        
        $req_json = $request->json()->all();
        $path = $request->path();
        $tracking_status = $this->log_rm_activity($path);
        $request->start_tracking = true;
        $resp = $next($request);
        $resp = (array)$resp->getData();
        if(isWorkingDay(carbon::now()) == true){
            $resp['tracking_status'] = $tracking_status;
            $resp['punch_in_time'] = $this->get_punch_in_time($rm_id = session('user_person_id'));
            // $punch_in_time = $this->get_punch_in_time($rm_id = session('user_person_id'));
            // $resp['punch_in_time'] = gmdate(Consts::DB_DATETIME_FORMAT, strtotime($punch_in_time));
        }
        $headers = get_header(request()->headers->get('origin'));
        $resp =  Response::json($resp, $resp['status_code'], $headers);
        return $resp;
    }

    public function log_rm_activity($path){

        $rm_act = new RMActivityLogs;
        $rm_serv = new RMService;
        $rm_punch_time = new RMPunchTime;
        $cur_date = Carbon::now()->format('Y-m-d');
        $cur_time = Carbon::now()->format('H:i:s');
        
        $act_data['country_code'] = session('country_code');
        $act_data['rel_mgr_id'] = session('user_person_id');
        $act_data['date'] = $cur_date;
        $act_data['activities'] = [$cur_time => $path];

        $addl_sql_condition = "order by id desc limit 1";

        $rm_act_data = $rm_punch_time->get_record_by_many(['rel_mgr_id', 'date'], [session('user_person_id'), $cur_date], ['punch_in_time','punch_out_time'],$condition = "and",$addl_sql_condition);
        
        if(isset($rm_act_data -> punch_in_time) && !isset($rm_act_data -> punch_out_time)){
            $upt_act_data['activities'] = [$cur_time => $path]; 
            $upt_act_data['id'] = $rm_act_data->id;
            $rm_act->update_model($upt_act_data);
            $track_status = Consts::START_TRACKING_STATUS; 
        }
        else {
            $rm_act_id = $rm_act->insert_model($act_data);
            if($rm_act_id){
                $rm_serv->punch_in();
                $track_status = 'started';
            }
            
        }
        return $track_status;
    }
    public function get_punch_in_time($rm_id){
        
        $cur_date = Carbon::now()->format('Y-m-d');
        $addl_sql_condition = "order by id desc limit 1";
        $rm_in_time = (new RMPunchTime)->get_record_by_many(['rel_mgr_id', 'date'], [$rm_id, $cur_date], ['punch_in_time'],"and",$addl_sql_condition)->punch_in_time;
        $rm_in_time = $cur_date." ".$rm_in_time;
        return $rm_in_time;   
    }
}