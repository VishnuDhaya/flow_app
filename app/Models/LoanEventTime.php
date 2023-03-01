<?php

namespace App\Models;
use App\Models\Model;

class LoanEventTime extends Model
{
    const TABLE = "loan_event_times";


    const UPDATABLE = ['country_code','loan_appl_doc_id', 'loan_doc_id','flow_rel_mgr_id','rm_time','cust_time','total_wait_time','ops_wait_time','disbursal_time','cs_time','cust_conf_channel','no_of_attempts','disbursal_mode'];

    const INSERTABLE = ['country_code', 'loan_appl_doc_id','loan_doc_id', 'flow_rel_mgr_id','rm_time','cust_time','total_wait_time','ops_wait_time','disbursal_time','cs_time','cust_conf_channel','no_of_attempts','disbursal_mode'];

    public function model(){        
        return LoanEventTime::class;
    }
}