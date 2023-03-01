<?php

use App\Models\Account;
use App\Repositories\SQL\CustCSFValuesRepositorySQL;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertMissingAccEligReason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up() {

        $country_codes = ['UGA', 'RWA'];
        foreach($country_codes as $country_code) {
            set_app_session($country_code);
            $acc_repo = new Account;
            $csf_repo = new CustCSFValuesRepositorySQL;
            
            $from_date = "2022-08-15";
            $accounts = $acc_repo->get_records_by('country_code', $country_code, ['id', 'acc_number', 'acc_prvdr_code'], null, "AND DATE(created_at) > '$from_date' and acc_elig_reason IS NULL");
            
            foreach($accounts as $account) {
				$record = $csf_repo->get_record_by_many(['acc_number', 'acc_prvdr_code'], [$account->acc_number, $account->acc_prvdr_code], [ 'conditions']);
                if($record){
                    $conditions = $record->conditions;
                    if(isset($conditions->acc_elig_reason)) {
                        $acc_elig_reason = $conditions->acc_elig_reason;
                        $acc_repo->update_model(['id'=>$account->id, 'acc_elig_reason'=>$acc_elig_reason]);
                    }
                }
               
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
