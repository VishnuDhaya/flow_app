<?php

namespace App\Scripts\php;

use Illuminate\Http\Request;
use DB;
use Excel;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Services\RecordAuditService;
use App\Repositories\SQL\CommonRepositorySQL;
use Illuminate\Support\Facades\DB as FacadesDB;

class UpdateTerritory{


    public function update(){

        $borr_repo = new BorrowerRepositorySQL();
        $record_serv = new RecordAuditService();
        $comm_repo = new CommonRepositorySQL();

        try {
            DB::beginTransaction();

            $borrowers = $borr_repo->get_records_by('country_code', session('country_code'), ['id', 'cust_id', 'territory', 'data_prvdr_code', 'reg_date']);

            foreach($borrowers as $borrower){
                if(($borrower->territory == null || $borrower->territory == '') && $borrower->data_prvdr_code != 'UFLO' 
                && $borrower->reg_date < '2021-10-01'){
                    $record = ['borrowers' => [
                            'status' => 'disabled',
                            'status_reason' => 'inactive',
                            'cust_id' => $borrower->cust_id,
                            'id' => $borrower->id
                            ]
                            ]; 

                    $record_serv->audit_borrower_status_change($record);
                    $comm_repo->update_status($record);
                    $borr_repo->update_model(['id' => $borrower->id, 'flow_rel_mgr_id' => 13]);

                }
                else{
                    
                }
            }
            
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
            thrw($e);
        }
    }
}

