<?php

namespace App\Services\Partners\UISG;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;
use App\Services\PartnerService;
use Illuminate\Support\Facades\URL;

class UISGService
{
    public function invoke_stmt_req($data)
    {   
        $username = "fw_test";
        $password = "flowPass123!";

        $url = 'https://iswintegrationbroker-develop.azurewebsites.net/api/Flow/request_txn_stmt';

        $req_body = [
            "terminalId" => $data["acc_number"],
            "startDate" => Carbon::parse($data["start_date"])->toISOString(),
            "endDate" => Carbon::parse($data["end_date"])->toISOString(),
            "preSignedUrl" => $data["presigned_url"],
            "flowRequestId" => $data["flow_req_id"],
            "username"=> $username,
            "password"=> $password,
            "CallbackUrl" => URL::route('notify_new_acc_stmt'),
        ];

        Log::warning('-------UISG STMT REQUEST-------');
        Log::warning($req_body);

        $resp = send_guzzle_req($url, $req_body);
        return $resp;
    }
    public function call_stmt_req_api($data)
    {
        $update_arr = [];
        try {
            $resp = $this->invoke_stmt_req($data);
            $http_status_code = $resp->getStatusCode();
            $response = json_decode($resp->getBody(), true);
        
            if ($http_status_code == 200) {
                $update_arr["status"] = $response["responseMessage"] == "REQUEST IN PROGRESS" ? "requested" : "err_req";
                if ($update_arr["status"] == "err_req") {
                    $update_arr["error_message"] = $response["responseMessage"];
                }
            } else {

                $update_arr["status"] = 'err_req';
                $update_arr["error_message"] = "HTTP_ERROR_CODE:" . $http_status_code;
            }
        } catch (Exception $e) {

            $update_arr["error_message"] = $e->getMessage();
            $update_arr["status"] = "err_req";
        } finally {
            if (array_key_exists("error_message", $update_arr)) {
                $update_arr["error_message"] = Str::limit($update_arr["error_message"], 125);
            }
            return $update_arr;
        }
    }
}
