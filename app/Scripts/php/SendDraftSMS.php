<?php

namespace App\Scripts\php;

use App\Repositories\SQL\BorrowerRepositorySQL;
use DB;
use Log;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\RMCustAssignmentsSQL;
use App\Services\Support\SMSService;

class SendDraftSMS{


    public function send_draft_sms(){

        $message = "Dear Flow Customer, you are reminded not to use Float Advance obtained from us in any online sports bettings, Ponzi schemes or any other online investments. Pay on time and keep Flow'ing";

        $sms_serv = new SMSService();

        $borrowers = (new BorrowerRepositorySQL)->get_records_by_many(['status', 'country_code'], ['enabled', 'UGA'], ['owner_person_id']);

        foreach($borrowers as $borrower){
            $person = (new PersonRepositorySQL)->get_record_by('id', $borrower->owner_person_id, ['mobile_num']);

            $sms_serv($person->mobile_num, $message, '256');
        }
  
    }

    public function send_sms_(){
        $rm_cust_repo =  new RMCustAssignmentsSQL();
        $sms_serv = new SMSService();
        
        $borrowers = $rm_cust_repo->get_records_by_many(['status', 'from_rm_id'], ['active', 2427], ['cust_id', 'rm_id']);

        foreach($borrowers as $borrower){
            $cust = (new BorrowerRepositorySQL)->get_record_by('cust_id', $borrower->cust_id, ['owner_person_id']); 
            $person = (new PersonRepositorySQL)->get_record_by('id', $cust->owner_person_id, ['first_name', 'mobile_num']);
            $cust_name = $person->first_name;
            // $mobile_num = $person->mobile_num;

            $rm = (new PersonRepositorySQL)->get_record_by('id', $borrower->rm_id, ['first_name', 'last_name', 'mobile_num']);
            $sub_rm_name = $rm->first_name." ".$rm->last_name;
            $sub_rm_mobile_num = $rm->mobile_num;


            $message = "Dear $cust_name, Your RM HENRY SIKYOMU will no longer be assisting. You have been assigned a new RM ($sub_rm_name - $sub_rm_mobile_num) to assist. Please call Flow customer success for Info.";

            $sms_serv($person->mobile_num, $message, '256');

        }

    }
    
}