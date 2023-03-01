<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;

class PreApproval extends Model
{
     //$updatable = ["name","lender_type","status"];
    const TABLE = "pre_approvals";

    const CODE_NAME = "cust_id";
    
    const INSERTABLE = ['cust_id','appr_count','appr_start_date','appr_exp_date','country_code','status','flow_rel_mgr_id'];

    const UPDATABLE =  ['appr_count','appr_start_date','appr_exp_date','status'];

    public function model(){        
            return PreApproval::class;
    }

    
}
