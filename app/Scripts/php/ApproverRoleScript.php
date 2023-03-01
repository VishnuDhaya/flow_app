<?php

namespace App\Scripts\php;
use DB;


class ApproverRoleScript{

    public function updateApproverRole(){
        $loans = DB::select("select loan_approver_id, loan_doc_id, flow_rel_mgr_id from loans where loan_approver_id is not null");
        
        foreach($loans as $loan){

            if($loan->loan_approver_id == $loan->flow_rel_mgr_id){
                DB::select("update loans set approver_role = 'relationship_manager' where loan_doc_id = ? ",[$loan->loan_doc_id]);
                DB::select("update loan_applications set approver_role = 'relationship_manager' where loan_doc_id = ? ",[$loan->loan_doc_id]);
            }
            else {
                DB::select("update loans set approver_role = 'operations_manager' where loan_doc_id = ? ", [$loan->loan_doc_id]);
                DB::select("update loan_applications set approver_role = 'operations_manager' where loan_doc_id = ? ",[$loan->loan_doc_id]);
            }
        }
    }
}