<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;

class RMCustAssignments extends Model
{
     
    const TABLE = "rm_cust_assignments";

    const CODE_NAME = "cust_id";

    
    const INSERTABLE = ['id','cust_id','from_rm_id','rm_id','country_code','from_date','to_date','territory','status','temporary_assign','reason_for_reassign','updated_at','updated_by','created_at','created_by'];

    const UPDATABLE =  ['status'];

    public function model(){        
            return RMCustAssignments ::class;
    }

    
}
