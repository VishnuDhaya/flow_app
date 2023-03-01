<?php

namespace App\Scripts\php;

use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Consts;
use App\Services\Schedule\ScheduleService;
use App\Repositories\SQL\RMCustAssignmentsSQL;
use Carbon\Carbon;
use Log;
use DB;
use Excel;

class InsertRMCustReassignments
{
    public function insert(){

        $data = [];
        try
        {
            DB::BeginTransaction();

            $s_serv = new ScheduleService();

            $markets = $s_serv->get_markets_to_schedule();
            foreach ($markets as $market) {
                session()->put('country_code', $market->country_code);

                $borr_repo = new BorrowerRepositorySQL();
                $loan_repo = new LoanRepositorySQL();
                $rm_cust_reass_repo = new RMCustAssignmentsSQL();

                $borrowers = $borr_repo->get_records_by('country_code', $market->country_code, ['cust_id', 'reg_flow_rel_mgr_id', 'flow_rel_mgr_id', 'reg_date', 'territory']);

                foreach($borrowers as $borrower){
                    if($borrower->reg_flow_rel_mgr_id == $borrower->flow_rel_mgr_id){
                        $from_date = $borrower->reg_date;
                    }
                    else{
                        $loan = DB::selectOne("select min(disbursal_date) disbursal_date from loans where flow_rel_mgr_id = ? and cust_id = ?", [$borrower->flow_rel_mgr_id, $borrower->cust_id]);
                        $from_date = $loan->disbursal_date;
                    }

                    $data['cust_id'] = $borrower->cust_id;
                    $data['country_code'] = $market->country_code;
                    $data['rm_id'] = $borrower->flow_rel_mgr_id;
                    $data['from_date'] = $from_date;
                    $data['status'] = 'active';
                    $data['territory'] = $borrower->territory;
                    $data['temporary_assign'] = false;

                    $rm_cust_reass_repo->insert_model($data);
                }
            }
            
            
            DB::commit();

        }
        catch (\Exception $e) {
            DB::rollback();
            Log::warning($e->getMessage());
            Log::warning($e->getTraceAsString());
        }

    }
}