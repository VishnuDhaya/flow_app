<?php

namespace App\Scripts\php;

use App\Services\WriteOffService;
use Illuminate\Http\Request;
use DB;
use Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanProvisioningRepositorySQL;

use App\Consts;
use Carbon\Carbon;
use App\Imports\UsersImport;



class WriteOffScript{


    private function get_sum_amount($data,$key){
        $amount_arr =  Arr::pluck($data, $key);
        return array_sum(Str::replace(',', '', $amount_arr));

    }
    public function update_write_off(){
        $file_path = "/usr/share/nginx/html/flow-api/storage/data/Write off [2021] FAs for Flow App.xlsx";
        $xl_data = Excel::toArray([],$file_path);
        $records = $xl_data[0];

        $fa_amt_sum = $this->get_sum_amount($records, 7);
        $paid_amount_sum =  $this->get_sum_amount($records, 9);

        $loan_prov_amount = $fa_amt_sum;

        $disbursal_year =  $records[0][0];
                
        $country_code = session('country_code');
        

        try{
            DB::beginTransaction();

            DB::update("update loan_loss_provisions set prov_amount = prov_amount + $loan_prov_amount, balance = balance + $loan_prov_amount where country_code = '{$country_code}' and year = {$disbursal_year}");

            foreach ($records as $key =>$record) {

                if($key == 0 || $key == 1 ){
                    continue;
                }
                if($record[0] == null){
                    break;
                }
                $data['loan_doc_id'] = $record[0];
                $amount = Str::replace(',', '',$record[7]);
                $loan_loss_repo =  new LoanProvisioningRepositorySQL();
                $loan_loss_obj = $loan_loss_repo->get_record_by('year',$disbursal_year,['id']);
                $data['loan_prov_id'] = $loan_loss_obj->id;
               
                $loan_repo  = new LoanRepositorySQL;
                $write_off_serv = new WriteOffService();

                $loan = $loan_repo->find_by_code($data['loan_doc_id'],['paid_amount','acc_prvdr_code']);
                $data['year'] = $disbursal_year;
                
                $recovery_amount = $write_off_serv->get_recovery_amount($data);
                
                $data['write_off_amount'] = (int)$amount ;

                $data['acc_prvdr_code'] = $loan->acc_prvdr_code;
                $wo_result = $write_off_serv->request_write_off($data);

                if($wo_result['write_off_id']){
                    $data['write_off_id'] = $wo_result['write_off_id'];
                    $write_off_serv->approve_write_off($data);
                }

            }

            DB::commit();

        } catch(\Exception $e){
            DB::rollback();
            Log::warning($e);
        }
            
     }

    
}