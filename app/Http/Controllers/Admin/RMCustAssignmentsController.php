<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Log;

use App\Http\Controllers\ApiController;
use App\Repositories\SQL\RMCustAssignmentsSQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Services\RMCustAssignmentsService;
use App\Services\BorrowerService;
use App\Repositories\SQL\PersonRepositorySQL;

class RMCustAssignmentsController extends ApiController
{
    
    public function rm_reassignment(Request $req)
    {
        $data = $req->data;
        $result = (new RMCustAssignmentsService())->rm_reassign($data);
        return $this->respondWithMessage("Reassignment completed successfully");
    }

    public function rm_and_terr_details(Request $req)
    {
        $data = $req->data;
        $result = (new RMCustAssignmentsService())->get_rm_and_terri_details($data);
        return $this->respondData($result);    
    }

    public function cust_details(Request $req)
    {
       $data = $req->data;
       
       $borrower_serv = new BorrowerService($data['country_code']);
       $person_repo = new PersonRepositorySQL();
       $borrowers = $borrower_serv->borrower_search($data['borrower_search_criteria'], ['territory', 'flow_rel_mgr_id', 'biz_name', 'lender_code', 'acc_prvdr_code', 'dp_rel_mgr_id', 'reg_flow_rel_mgr_id', 'owner_person_id', 'cust_id']);

       if(count($borrowers["results"]) == 1)
       {

        $cust_array=[];
        $cust_array['cust_id'] = $borrowers["results"][0]->cust_id;
        $cust_array['biz_name'] = $borrowers["results"][0]->biz_name;
        $cust_array['territory'] = $borrowers["results"][0]->territory;
        $cust_array['rm_id'] = $borrowers["results"][0]->flow_rel_mgr_id;  
        $cust_array['rm_name'] = $person_repo->full_name($borrowers["results"][0]->flow_rel_mgr_id);

       return $this->respondData($cust_array);
       }

       else{
        thrw("More than one customer returned for your search");
       }
    }

    public function temp_rm_details(Request $req)
    {
        $data = $req->data;
        $results = (new RMCustAssignmentsService())->temp_assigned_rms($data);
        return $this->respondData($results);                  
    }

    
    public function sms_details(Request $req)
    {
        $data = $req->data;
        if($data['reason_for_reassign'] == "rm_has_resigned")
        {
            $results = "Dear (customer name), From today, Your RM (disabled rm name) will no longer be assisting as he/she is no longer a FLOW staff. Any interaction with him/her will be at your own risk. You have been assigned a new RM (new rm name - new rm mobile num) to assist. Please call Flow customer success for Info.";
        }
        elseif($data['reason_for_reassign'] == "other_reason")
        {
            $results = "Dear (customer name), You have been assigned a new FLOW Relationship Manager (new RM name - mobile number) to assist. Please call FLOW customer success for details.";
        }
      
        return $this->respondData($results);                  
    }

}
