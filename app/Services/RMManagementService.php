<?php
namespace App\Services;

use App\Repositories\SQL\PersonRepositorySQL;
use App\Models\RmTarget;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\FlowCustomMail;
use Illuminate\Support\Facades\Mail;


class RMManagementService{


    public function assign_target($data){

        $rm_target = new RmTarget;
        $rel_mgr_targets = $data['target'];
        $next_month = $data['target_month'];
        
        $date = strtotime($next_month);
        $month_str = date('M', $date);
        $year = date('Y',$date);
    
        foreach($rel_mgr_targets as $rm_id => $target){
           
            $rm_targets = $rm_target->get_records_by_many(['rel_mgr_id', 'year'], [$rm_id,$year], ['targets']);
            
            $rm_name = (new PersonRepositorySQL)->full_name($rm_id);

        if($rm_targets){
            $rm_target->update_json_arr_by_code('targets', [$month_str => $target], $rm_id);

        }else{
            $rm_target_data['year'] = $year;
            $rm_target_data['rel_mgr_id'] = $rm_id;
            $rm_target_data['rm_name'] = $rm_name;
            $rm_target_data['targets'] = [$month_str => $target];
            $rm_target_data['country_code'] = session('country_code');

            $rm_target->insert_model($rm_target_data);
           
        }
    }
        
        $result = 'The target for the RM is successfully assigned';
        return  $result;

    }
    public function view_targets($data){
 
        
            if($data['month'] <= Carbon::now()->addMonthsNoOverflow(1)->format('Y-m-d')){
                $date = strtotime($data['month']);
                $month_start = date('Y-m-01', $date);
                $year = date('Y', $date);
                $month_str = date('M',$date);
            }else{
                $next_month = Carbon::now()->addMonthsNoOverflow(1)->format('M-Y');
                thrw("You can view Targets upto  $next_month. Please enter the valid  Month");
            }
        

        $end_date_month = date('Y-m-t',$date);
        
        $next_month = Carbon::now()->addMonthsNoOverflow(1)->format('M');
        $next_month_year = Carbon::now()->addMonthsNoOverflow(1)->format('Y');
    
        $assigned_rm_targets = DB::select("select status, person_id, rel_mgr_id, rm_name, json_extract(targets, '$.{$month_str}')as rm_target , json_extract(targets, '$.{$next_month}')as target from rm_targets rm,  app_users ap where rm.year = ? and rm.country_code = ? and ap.status = 'enabled' and ap.role_codes='relationship_manager' and ap.person_id = rm.rel_mgr_id",[$year,session('country_code')]);
        $records = [];
        foreach($assigned_rm_targets as $assigned_rm_target){

            $rm_id =  $assigned_rm_target->rel_mgr_id;
           
            $target_accquired = DB::selectOne("select count(*) as acquired from borrowers where reg_flow_rel_mgr_id = {$rm_id} and date(first_loan_date) >= '{$month_start}' and date(first_loan_date) <= '{$end_date_month}' and country_code = ? ",[session('country_code')]);

            if($month_str == 'Dec'){
                $target_mon = DB::selectOne("select rel_mgr_id, rm_name , json_extract(targets, '$.{$next_month}')as target from rm_targets where year = ? and rel_mgr_id = ? and country_code = ? ",[$next_month_year,$rm_id, session('country_code')]);

                $next_target = $target_mon ? $target_mon->target : null; 
            }else{
                $next_target = $assigned_rm_target->target;
            }
            
            $target = $assigned_rm_target->rm_target;
            
            if(isset($target) && $target != 0){
                $target_acc_calc = ($target_accquired->acquired/$target)*100;
                $target_acc = round($target_acc_calc,2)." %";
            }else{
                $target = 'NA';
                $target_acc = "0 %";
                $target_accquired->acquired = 0;

            }
                $records[] = ["rm_name" => $assigned_rm_target->rm_name, "targetted"=>$target,  
                "month_accquired"=> $target_accquired->acquired,  "accquired"=>$target_acc,"rm_id" => $rm_id,"next_targets"=>$next_target, "target_entries" => null];              
            
        }
        $header = [  "rm_name" => "RM Name",  "month"=>"{$month_str} Targetted", "month_accquired"=>"{$month_str} Acquired", "accquired"=>"Acquired %", "add_targets" => "{$next_month} Target"];
        
         return [ 'records_arr'=>$records, 'headers'=> $header];  
    }

    public function send_mail_notify_rm_target(){
        
        $cur_date = Carbon::now();
        $next_month = $cur_date->copy()->addMonths(1);
        $end_date = $cur_date->lastOfMonth()->endOfDay();
        
        $year = $next_month->copy()->format('Y');
        $month_str = $next_month->copy()->format('M');
        $end_date_str = $end_date->copy()->format('d-M-Y');
        
        $end_before_date = $end_date->subDays('1')->format('Y-m-d');
       
        $count = 0;

        $targets = DB::select("select json_extract(targets, '$.{$month_str}') as target,rel_mgr_id,person_id from rm_targets rm, app_users ap  where rm.rel_mgr_id = ap.person_id and ap.status = 'enabled' and ap.role_codes = 'relationship_manager' and  rm.year = ? and  rm.country_code = ?",[$year,session('country_code')]);

        foreach($targets as $rm_target){
            if($rm_target->target === null){
                $count += 1;
            }
        }
        if($count > 0){
        $cur_date = carbon::now()->format('Y-m-d'); 
                if($cur_date < $end_before_date){
                    $mail_recp  = get_ops_admin_email();

                }else{
                    $mail_recp [] = get_ops_admin_email();
                    $mail_recp [] = config('app.app_support_email');
                    
                }
            Mail::to($mail_recp)->
            queue((new FlowCustomMail('rm_target_not_assigned',['country_code' => session('country_code'),
                                                                'date' => $end_date_str ]))->onQueue('emails')); 
        }   
    }
}