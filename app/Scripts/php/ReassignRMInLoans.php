<?php

namespace App\Scripts\php;

use App\Models\RMCustAssignments;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Services\RMCustAssignmentsService;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;

class ReassignRMInLoans{

    public function main(string $from_rm_id)
    {
        try {
            DB::beginTransaction();

            set_app_session('UGA');
            $rm_assign_serv = new RMCustAssignmentsService;
            $records = (new RMCustAssignments)->get_records_by_many(['from_rm_id', 'status'], [$from_rm_id, 'active'], ['cust_id', 'rm_id']);

            foreach($records as $record) {
                $cust_id = $record->cust_id;
                $to_rm_id = $record->rm_id;
                if( is_null($cust_id) || is_null($to_rm_id) ) continue;
                $to_rm_name = (new PersonRepositorySQL)->full_name($to_rm_id);

                $rm_assign_serv->update_rm_in_loans($cust_id, $from_rm_id, $to_rm_id, $to_rm_name);
                $rm_assign_serv->update_rm_in_loan_appls($cust_id, $from_rm_id, $to_rm_id, $to_rm_name);
            }
            DB::commit();
        }
        catch(Exception $e) {
            DB::rollBack();
            thrw($e->getMessage());
        }
    }
}