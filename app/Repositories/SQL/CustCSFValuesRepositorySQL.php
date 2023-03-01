<?php
 
namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CustCSFValues;
use App\Consts;
use Carbon\Carbon;

class CustCSFValuesRepositorySQL extends BaseRepositorySQL{
    public function __construct()
    {
      parent::__construct();

    }
    public function model(){
      return CustCSFValues::class;
    }
    public function get_run_id($acc_number, $acc_prvdr_code){
      $record = DB::select("select distinct run_id from cust_csf_values where acc_number = ? and acc_prvdr_code = ?", [$acc_number, $acc_prvdr_code]);
      if (count($record) > 1){
        thrw("More than one run_id returned for the given acc_number: '{$acc_number}'");
      }
      $run_id = (count($record) == 1) ? $record[0]->run_id : NULL;
      return $run_id;
		}

    public function get_run_date($run_id){
      $record = $this->get_record_by_many(['csf_type', 'run_id'], ['score_date', $run_id], ['csf_normal_value']);
			return $record->csf_normal_value;
		}

    public function delete_cust_csf_values($acc_number, $acc_prvdr_code) {
      $result = DB::delete("delete from cust_csf_values where acc_number = ? and acc_prvdr_code = ?", [$acc_number, $acc_prvdr_code]);
    }
}
