<?php
namespace App\Services\Schedule;

use App\Models\RMMetrics;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;



class RMMetricsScheduleService{
    public function update_loan_appl_metrics(){
       
        $loan_appl_repo = new LoanApplicationRepositorySQL;
        $rm_metrics = DB::update("delete from rm_metrics where country_code = ?",[session('country_code')]);
        $common_repo = new CommonRepositorySQL();
        $flow_rm_ids = collect($common_repo->get_users_by_role_codes(['relationship_manager'], 'enabled'))->pluck('person_id')->toArray();
        
        foreach($flow_rm_ids as $flow_rm_id){
            $rm_metrics = $this->get_loan_appl_metrics($flow_rm_id);
            $rm_metrics_30_days = $this->get_loan_appl_metrics($flow_rm_id,true);
            $rm_metrics = array_merge((array)$rm_metrics,(array)$rm_metrics_30_days);            
            $record = [ 
                'appr_count' => $rm_metrics['approve_count'],
                'avg_time' => $rm_metrics['avg_time'],
                'max_time' => $rm_metrics['max_time'],
                'rm_id' =>$rm_metrics['flow_rel_mgr_id'],
                '30_days_appr_count' => $rm_metrics['approve_count_30_days'],
                '30_days_avg_time' => $rm_metrics['avg_time_30_days'],
                '30_days_max_time' => $rm_metrics['max_time_30_days'],
                'country_code' => session('country_code')
            ];  
            $repo = new RMMetrics();
            $repo->insert_model($record);

        }
    }


    private function get_loan_appl_metrics($flow_rm_id,$last_30_days = false){
        $addl_sql = "";
        $count_as = 'approve_count';
        $max_time_as = 'max_time';
        $avg_time_as = 'avg_time';
        if($last_30_days){
            $from_date = Carbon::now()->subDays(30);
            $addl_sql = "and date(loan_appl_date) >= '$from_date'";
            $count_as = 'approve_count_30_days';
            $max_time_as = 'max_time_30_days';
            $avg_time_as = 'avg_time_30_days';
        }
        return DB::selectOne("SELECT  count(*) as $count_as, avg(TIMESTAMPDIFF(MINUTE,loan_appl_date,loan_approved_date)) AS $avg_time_as,max(TIMESTAMPDIFF(MINUTE,loan_appl_date,loan_approved_date)) AS $max_time_as ,flow_rel_mgr_id FROM loan_applications   where status in ('approved','rejected') and flow_rel_mgr_id = $flow_rm_id   $addl_sql");
    }
}