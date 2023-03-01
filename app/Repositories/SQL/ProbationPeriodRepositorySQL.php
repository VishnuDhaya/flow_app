<?php
namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use App\Models\ProbationPeriod;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Repositories\SQL\CustAgreementRepositorySQL;

class ProbationPeriodRepositorySQL  extends BaseRepositorySQL
{

	public function __construct(){
            parent::__construct();
            $this->class = ProbationPeriod::class;

    }
        
    public function model(){        
        return $this->class;
    }
    public function start_probation($cust_id, $type, $fa_count, $start_date){

        $condonation_delay = config('app.condonation_punishment_delay');
        if($type == "condonation") {
            $cust_agrmnt_repo = new CustAgreementRepositorySQL();
            $cust_agrmnt_repo->inactivate_agreement($cust_id);
        } 
        
        $cond_prob = [
            'cust_id' => $cust_id,
            'start_date' => $start_date,
            'type' => $type,
            'fa_count' => $fa_count,
            'status' => 'active',
            'created_at' => Carbon::now(),
            'country_code' => $this->country_code,
            
        ];

        $this->insert_model($cond_prob);
        
    }

    public function complete_probation($id, $cust_id, $end_date = null){
        if($end_date == null){
            $end_date = Carbon::now();
        }
        
        $cust_agrmnt_repo = new CustAgreementRepositorySQL();
        $cust_agrmnt_repo->inactivate_agreement($cust_id);
        $this->update_model(["id" => $id, "status" => 'completed', "end_date" => $end_date]);
    }
} 
