<?php

namespace App\Services\Schedule;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\BorrowerService;


class UpdateStatusDisabled{
    public function update_cust_status(){
        
        $kyc =  collect(DB::select("select id, cust_id, 'incomplete_kyc' as reason from borrowers where status = 'enabled' and kyc_status<>'completed'"));
       
        $inactive =  collect(DB::select("select id, cust_id, '90_day_inactivity' as reason from borrowers where status = 'enabled' and datediff(curdate(),last_loan_date)>=90"));
        
        
        //$invalid = collect(DB::select("select id, cust_id, 'expired_agreement' as reason from borrowers where status = 'enabled' and curdate()>aggr_valid_upto"));
        
        $overdue = collect(DB::select("select id, cust_id, 'more_than_30_day_overdue' as reason from borrowers where status='enabled' and cust_id in(select cust_id from loans where datediff(curdate(),due_date)>30 and status='overdue')"));

        //$no_agreement = collect(DB::select("select id, cust_id, 'no_agreement' as reason from borrowers where status = 'enabled' and current_aggr_doc_id IS NULL"));
      
        $merged = $kyc->merge($inactive)->merge($overdue);
       
        $borr_serv = new BorrowerService();
        foreach($merged as $item){
           
            $data['borrowers'] = [  'id' => $item->id,
                                    'cust_id' => $item->cust_id,
                                    'status_reason' => $item->reason, 
                                    'status' => 'disabled',
                                    'remarks' => null];
            
            $borr_serv->update_status($data);
             
            
        }    
    }
    
}