<?php

namespace App\Scripts\php;
use DB;
use Log;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Services\Support\SMSService;

class ChangeRMNotification{


    public function customer_handover($disable_email, $subs_email){

        $disable_rm = DB::select("select id, person_id from app_users where email = ? ", [$disable_email]);
        $subs_rm = DB::select("select id, person_id from app_users where email = ? ", [$subs_email]);

        DB::update("update app_users set status = 'disabled' where id = ? ", [$disable_rm[0]->id]);
        DB::update("update persons set status = 'disabled' where id = ? ", [$disable_rm[0]->person_id]);

        DB::update("update borrowers set flow_rel_mgr_id = ? where flow_rel_mgr_id = ? ", [$subs_rm[0]->person_id, $disable_rm[0]->person_id]);
        DB::update("update loans set flow_rel_mgr_id = ? where flow_rel_mgr_id = ? and status in ('due', 'ongoing', 'overdue')", [$subs_rm[0]->person_id, $disable_rm[0]->person_id]);

    }


    public function notify_rm_change($disable_email, $subs_email, $cust_details = null){

        $disable_rm = DB::select("select person_id from app_users where email = ? ", [$disable_email]);
        $subs_rm = DB::select("select person_id from app_users where email = ? ", [$subs_email]);

        $disable_data = DB::select("select CONCAT(first_name, CONCAT(' ' , last_name)) as name from persons where id = ? ", [$disable_rm[0]->person_id]);

        $subs_data = DB::select("select CONCAT(first_name, CONCAT(' ' , last_name)) as name, mobile_num from persons where id = ? ", [$subs_rm[0]->person_id]);

        if($cust_details == null){
            $cust_details = DB::select("select CONCAT(first_name, CONCAT(' ' , last_name)) as name, mobile_num from borrowers b, persons p  where b.owner_person_id = p.id and flow_rel_mgr_id = ?", [$disable_rm[0]->person_id]);
        }

        $disable_rm_name = strtoupper($disable_data[0]->name);
        $subs_rm_name = strtoupper($subs_data[0]->name);

        $country_code = session('country_code');

        $market_repo = new MarketRepositorySQL();
        $isd_code = $market_repo->get_isd_code($country_code);

        foreach($cust_details as $cust_detail){

            $mobile_num = $cust_detail->mobile_num;
            $name = strtoupper($cust_detail->name);
            
            $message = "Dear $name, From today, Your RM $disable_rm_name will no longer be assisting as she is no longer a FLOW staff. Any interaction with her will be at your own risk. You have been assigned a new RM ($subs_rm_name - {$subs_data[0]->mobile_num}) to assist. Please call Flow customer success for Info.";

            $sms_serv = new SMSService();
            $sms_serv($mobile_num, $message, $isd_code->isd_code);
        }
    }

    public function customer_handover_by_district($disable_email, $subs_email, $district){

        $cust_details = DB::select("select cust_id, first_name as name, mobile_num from borrowers b, address_info a, persons p where a.id= b.biz_address_id and p.id = b.owner_person_id and field_2= ?", [$district]);

        $this->notify_rm_change($disable_email, $subs_email, $cust_details);

        $subs_rm = DB::select("select person_id from app_users where email = ? ", [$subs_email]);
        
        DB::update("update borrowers b, address_info a set flow_rel_mgr_id= ? where a.id= b.biz_address_id and field_2= ?", [$subs_rm[0]->person_id, $district]);

    }

}
