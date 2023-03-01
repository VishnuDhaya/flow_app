<?php
namespace App\Repositories\SQL;

use App\Models\LoanLossProvisions;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\CommonRepositorySQL;
use DB;
class LoanProvisioningRepositorySQL  extends BaseRepositorySQL
{

	public function __construct(){
        parent::__construct();
        $this->class = LoanLossProvisions::class;

    }
        
    public function model(){        
        return $this->class;
    }

    public function get_loan_prov($country_code){
        $loan_prov = $this->get_record_by_many(['country_code', 'acc_prvdr_code'], [$country_code, session('acc_prvdr_code')], ['id', 'balance', 'requested_amount']);
        //$this->currency = (new CommonRepositorySQL())->get_currency()->currency_code;
        $resp['balance'] = $loan_prov->balance + $loan_prov->requested_amount; 
        $resp['id'] = $loan_prov->id;  
        return $resp;
    }

    public function get_loan_prov_year(){

        $loan_provs = $this->get_records_by('country_code', session('country_code'), ['id', 'year', 'balance', 'requested_amount']);
        
        $loan_prov_list =[];
        $currency = (new CommonRepositorySQL())->get_currency()->currency_code;
        foreach($loan_provs as $loan_prov){
            $item['loan_prov_id'] = $loan_prov->id;
            $balance = $loan_prov->balance + $loan_prov->requested_amount;
            $item['loan_prov_year_bal'] = "Provisioning Year $loan_prov->year - Balance ($balance) $currency";
            $loan_prov_list[] = $item;
        }
        
        return $loan_prov_list;
    }

}