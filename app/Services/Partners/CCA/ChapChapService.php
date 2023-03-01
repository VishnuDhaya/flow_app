<?php

namespace App\Services\Partners\CCA;

use App\Models\PartnerAccStmtRequests;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;
use App\Services\PartnerService;
use Illuminate\Support\Facades\URL;

class ChapChapService 
{
    public function invoke_cca_stmt_req_api($data){
        $username = "FLOW";
        $password = "ASzpPCl61iiV";
        $bucket_name = env("S3_BUCKET");
        
        $url = is_array($data["acc_number"]) ? "https://apis.chapchap.co:9097/api/v1/GenerateStatementMultipleMerchants" : "https://apis.chapchap.co:9097/api/v1/GenerateStatement";
        $phone_num = '256'. (string)(int)$data["acc_number"];
        $req_body = [
            "username" => $username,
            "password" => $password,
            // "phoneNumber" => $data["phone_num"],
            "merchantNumber" => $phone_num,
            "s3Bucket" => $bucket_name,
            "awsBucketName" => $bucket_name,
            "awsAccessKey" => env("AWS_ACCESS_KEY_ID"),
            "awsSecretKey" => env("AWS_SECRET_ACCESS_KEY"),
            "startDate" => $data["start_date"],
            "awsRegion" => env("AWS_REGION"),
            "endDate" => $data["end_date"],
            "emailRecepients" => config('app.app_support_email'),
            "tranRef" => $data["flow_req_id"],
            "s3location" => "CCA",
            "callBackUrl" => URL::route('notify_new_acc_stmt')
        ];
        
        Log::warning('-------CCA STMT REQUEST-------');
        Log::warning($req_body);
    
        $resp = send_guzzle_req($url, $req_body);
        return $resp;
    }

    public function call_stmt_req_api($data) {
        try{
            $resp = $this->invoke_cca_stmt_req_api($data);

            $http_status_code = $resp->getStatusCode();
            $response = json_decode($resp->getBody(), true);
            $update_arr = [];

            if ($http_status_code == 200) {

                Log::warning("-------CCA STMT RESPONSE-------");
                Log::warning($response);

                $update_arr["status"] = $response["statusDescription"] == "REPORT REQUEST SUCCESSFUL" ? "requested" : "err_req";
                if ($update_arr["status"] == "err_req") {
                    $update_arr["error_message"] = $response["statusDescription"];
                }
            }
            else {

                $update_arr["status"] = 'err_req';
                $update_arr["error_message"] = "HTTP_ERROR_CODE:" . $http_status_code;
            }   
        }   
        catch(Exception $e) {

            $update_arr["error_message"] = $e->getMessage();
            $update_arr["status"] = "err_req";
        }    
        finally{
            if (array_key_exists("error_message",$update_arr)){
                $update_arr["error_message"] = Str::limit($update_arr["error_message"], 125);
            }
            return $update_arr;   
        }
    }

    public function req_cust_statement($data){
        session()->put("country_code", "UGA");

        $flow_req_id = uniqid();
        $data["req_time"] = datetime_db();
        $data["flow_req_id"] = $flow_req_id;
        $data["acc_prvdr_code"] = "CCA";
        $data["country_code"] = session("country_code");

        $partner_stmt_req_repo = new PartnerAccStmtRequests();
        $partner_stmt_req_repo->insert_model($data);

        // $data["agent_id"] = $data["acc_number"];
        $data["phone_num"] = $data["acc_number"];
        $data["callback_url"] = URL::to('/api/partner/cca/callback/stmt');
        $update_arr = $this->call_stmt_req_api($data);
        $update_arr["flow_req_id"] = $flow_req_id;
        $partner_stmt_req_repo->update_model($update_arr, "flow_req_id");

        return $update_arr;
    }

    public function req_mul_cust_statement($data){
        session()->put("country_code", "UGA");

        $data["req_time"] = datetime_db();
        $data["acc_prvdr_code"] = "CCA";
        $data["country_code"] = session("country_code");

        $partner_stmt_req_repo = new PartnerAccStmtRequests();

        $flow_req_ids = array();
        $acc_numbers = $data["acc_numbers"];
        foreach ($acc_numbers as $acc_number) {
            $data["flow_req_id"] = uniqid();
            $data["acc_number"] = $acc_number;
            array_push($flow_req_ids, $data["flow_req_id"]);
            $partner_stmt_req_repo->insert_model($data);
        }
        
        $data["flow_req_id"] = $flow_req_ids;
        $data["phone_num"] = $acc_numbers;
        
        $update_arr = $this->call_stmt_req_api($data);

        foreach($flow_req_ids as $flow_req_id) {
            $update_arr["flow_req_id"] = $flow_req_id;
            $partner_stmt_req_repo->update_model($update_arr, "flow_req_id");
        }          

        return $update_arr;
    }

    public function statement_req_callback($data) {
        set_app_session('UGA');
        $partner_stmt_req_repo = new PartnerAccStmtRequests();

        Log::warning('-------CCA CALLBACK RESPONSE-------');
        Log::warning($data);

        $data["resp_time"] = datetime_db();
        
        $record_data = $partner_stmt_req_repo->get_record_by('flow_req_id',$data['flow_req_id'],['acc_number','status', 'acc_prvdr_code']);

        if ( isset($record_data) ) {
            $status = $record_data->status;
            if( $data["status"] == "SUCCESS" && array_key_exists("bucket_key", $data) ) {
                if ( $status == "requested" ) {
                    $data["object_key"] = $data["bucket_key"];
                    unset($data["bucket_key"]);
                    
                    $data["lambda_status"] = "invoking_lambda";
                    $partner_stmt_req_repo->update_model($data, "flow_req_id");
                    unset($data["resp_time"]);

                    $acc_number = $record_data->acc_number;
                    
                    $payload = [    "object_key" => $data["object_key"],
                                    "acc_number" => $acc_number,
                                    "flow_req_id" => $data['flow_req_id']
                                ];
                    $partner_serv = new PartnerService();
                    $partner_serv->invoke_lambda($payload, $record_data->acc_prvdr_code);
                    
                    $data["lambda_status"] = "lambda_invoked";
                }
                else{
                    thrw("Callback for this 'flow_req_id' is already received.");
                }
            }
            elseif( $data["status"] == "FAILED" ) {
                if ( $status != "requested" ) {
                    thrw("Callback for this 'flow_req_id' is already received.");
                }
            }
            $partner_stmt_req_repo->update_model($data, "flow_req_id");
            return $data;
        }
        else{
            thrw("Given 'flow_req_id' doesn't exist");
        }
    }
}
