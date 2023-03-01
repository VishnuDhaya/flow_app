<?php

namespace App\Services\Partners\UEZM;

use App\Repositories\SQL\BorrowerRepositorySQL;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use Carbon\Carbon;
use \GuzzleHttp\Client;
use Illuminate\Support\Str;
use Exception;
use App\Consts;
use DB;

class EzeeMoneyService 
{
    public function create_sc_code($lead_id)
    {
        $file_name = "UEZM_service_registration";

        $lead_repo = new LeadRepositorySQL();
        $lead_data_as_json = $lead_repo->find($lead_id,['cust_reg_json']);
        $lead_data = json_decode($lead_data_as_json->cust_reg_json, true);

        $account_id = $lead_data['account']['id'];
        $service_reg_array['username'] = config('app.UEZM_TF_CRED.SC_CODE_CRED.username');
        $service_reg_array['password'] = config('app.UEZM_TF_CRED.SC_CODE_CRED.password');

        $service_reg_array['biz_name'] = $lead_data['biz_info']['biz_name']['value'];
        $service_reg_array['ho_address'] = $lead_data['biz_address']['location']['value'].",".$lead_data['biz_address']['district']['value'];
        $service_reg_array['business_ph'] = $lead_data['biz_identity']['mobile_num']['value'];

        $tf_region = ['central' => '1','eastern' => '2', 'northern' => '3', 'western' => '4','south_west' => '5', 'corporate' => '6'];
        $service_reg_array['region'] = $tf_region[$lead_data['biz_address']['region']['value']];
            
        $service_reg_array['business_place'] = $lead_data['biz_address']['location']['value']; 
        $service_reg_array['village_location'] = $lead_data['biz_address']['location']['value']; 
        $service_reg_array['district'] = $lead_data['biz_address']['district']['value'];
        $service_reg_array['gender'] = $lead_data['owner_person']['gender']['value'];
        $service_reg_array['name'] = $lead_data['owner_person']['first_name']['value']." ".$lead_data['owner_person']['last_name']['value'];
        $service_reg_array['national_id'] = $lead_data['owner_person']['national_id']['value'];
        $service_reg_array['notification_ph'] = $lead_data['biz_identity']['mobile_num']['value'];
        $service_reg_array['email'] = $lead_data['owner_person']['email_id']['value'];
        $service_reg_array['lc1'] = $lead_data['biz_address']['location']['value'];
        $service_reg_array['bank_code'] = 'TBOA';
        $service_reg_array['bank_acc_num'] = '000000000';
        $service_reg_array['bank_acc_name'] = 'NA';
        $service_reg_array['wallet_type'] = '3';
        $service_reg_array['UEZM'] = $lead_data['partner_kyc']['UEZM'];

        $sc_reg_json = json_encode($service_reg_array);

        if(env('APP_ENV') == 'production'){
            $reg_status = run_python_script("UEZM/{$file_name}", $sc_reg_json);
        }
        else{
            $reg_status = ['status' => "success", "message" => null, "sc_code" => rand(10000000, 99999999)];
        }

        if($reg_status['status'] == "success"){
            $account_repo = new AccountRepositorySQL;
            $account = $account_repo->find($account_id, ['acc_number','cust_id']);
            (new BorrowerRepositorySQL)->update_model_by_code(['cust_id' => $account->cust_id, 'acc_number' => $reg_status['sc_code']]);
            DB::update("update cust_csf_values set acc_number = ?  where acc_number = ? and acc_prvdr_code = 'UEZM'",
                                            [$reg_status['sc_code'], $account->acc_number]);
            $account_repo->update_model(['acc_number' => $reg_status['sc_code'],"id" => $account_id]);
        }
        return $reg_status;
    }

    public function loan_repayment_setup($loan_data)
    {
        $file_name = "UEZM_add_loan_repayment";

        $loan_data['username'] = config('app.UEZM_TF_CRED.LOAN_SETUP_CRED.username');
        $loan_data['password'] = config('app.UEZM_TF_CRED.LOAN_SETUP_CRED.password');
        $loan_data_json = json_encode($loan_data);
    
        if(env('APP_ENV') == 'production'){
            $loan_reg_status = run_python_script("UEZM/{$file_name}", $loan_data_json);
        }
        else{
            $loan_reg_status = ['status' => "success", "message" => null, "loan_id" => rand(200, 10000)];
        }

        if($loan_reg_status['status'] == "success"){
            $loan_repo = new LoanRepositorySQL;
            $loan_repo->update_model(['partner_loan_id' => $loan_reg_status['loan_id'],"acc_number" => $loan_data['sc_code']], 'acc_number');
        }       

        return $loan_reg_status;

    }

}
