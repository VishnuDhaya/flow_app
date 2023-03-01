<?php
 
namespace App\Repositories\SQL;

use App\Models\RMCustAssignments;
use Illuminate\Support\Facades\DB;
use Log;

class RMCustAssignmentsSQL extends BaseRepositorySQL{
    
    public function __construct(){
        parent::__construct();
        $this->class = RMCustAssignments::class;

    }
        
    public function model(){        
        return $this->class;
    }
 

    public function get_rm_territories($data)
    {
        $records = DB::select("select distinct territory from borrowers b where b.flow_rel_mgr_id = ? and b.status='enabled' ", [$data['from_rm_id']]);
        return $records;

    }   

    public function get_terri_cust_count($data)
    {
        $records = DB::selectOne("select COUNT(cust_id) count from borrowers where flow_rel_mgr_id = ? and territory = ? and status= 'enabled' ", [$data['from_rm_id'], $data['territory']]);
        return $records;

    }

    public function get_total_cust_count($data)
    {
        $records = DB::selectOne("select COUNT(cust_id) count from borrowers b where b.flow_rel_mgr_id = ? and b.status= 'enabled' ", [$data['from_rm_id']]);
        return $records;

    }

    public function update_existing_customer($cust_id)
    {
      
        DB::table("rm_cust_assignments")->where('cust_id', $cust_id)->where('status', 'active')->update(['to_date' => date_db(), "status" => "inactive"]); 
    }

    public function get_temp_assigned_rms($data)
    {
        $records = DB::select("select distinct from_rm_id, rm_id from rm_cust_assignments where status = ? and temporary_assign = ? ", ['active', true]);
        return $records;

    } 

    public function get_temp_assigned_rm($data)
    {
        $record = DB::select("select distinct rm_id from rm_cust_assignments where status = ? and temporary_assign = ? and from_rm_id = ? ", ['active', true, $data['from_rm_id']]);
        return $record[0]->rm_id;   
    }
}