<?php

namespace App\Services\Partners\RRTN;

use App\Consts;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Services\AccountService;
use App\Services\Partners\FlowCrypt;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;
use App\Services\PartnerService;
use Illuminate\Support\Facades\URL;

class RRTNService 
{       

    public function call_rtn_api( $endpoint, $payload = [] ) {

        $account = (new AccountService)->get_lender_disbursal_account('RFLW', 'RRTN');
        $api_details = [
            'base_url' => $account->api_url,
            'api_cred' => $account->api_cred
        ];

        $resp = $this->encrypt_invoke_n_decrypt($api_details, $endpoint, $payload);
        return $resp;
    }

    public function prepare_payload($data, $token) { 
        
        $base_payload = [
            'token' => $token,
            'timestamp' => floor(microtime(true) * 1000),
        ];
        $resp = array_merge($base_payload, $data);
        return $resp;
    }

    public function encrypt_invoke_n_decrypt($api_details, $endpoint, $payload) {

        $api_cred = $api_details['api_cred'];
        $api_url = $api_details['base_url'].$endpoint;

        $payload = $this->prepare_payload($payload, $api_cred->token);
        $flow_crypt = new FlowCrypt($api_cred->password);
        $payload = $flow_crypt->encrypt($payload);

        $guzzle_resp = send_guzzle_req($api_url, $payload);
        
        $http_status_code = $guzzle_resp->getStatusCode();
        if ($http_status_code != 200) {
            $resp["status"] = 'error';
            $resp["statusCode"] = $http_status_code;
            $resp["error"] = "HTTP_ERROR_CODE:$http_status_code";
            return $resp;
        }
        
        $resp = json_decode($guzzle_resp->getBody(), true);

        $resp = $flow_crypt->decrypt($resp);
        return $resp;
    }

    public function invoke_stmt_req($data) {   

        $endpoint = '/getStatement'; 
        $req_body = [
            "acc_number" => $data["acc_number"],
            "flow_req_id" => $data["flow_req_id"],
            "start_time" => Carbon::parse($data["start_date"])->startOfDay()->toDateTimeString(),
            "end_time" => Carbon::parse($data["end_date"])->endOfDay()->toDateTimeString(),
            "url" => $data["presigned_url"],
        ];

        $resp = $this->call_rtn_api($endpoint, $req_body);
        return $resp;
    }

    public function call_stmt_req_api($data) {
        $update_arr = [];
        try {
            $resp = $this->invoke_stmt_req($data);
            if ($resp['statusCode'] == 200) {
                $update_arr["status"] = "requested";
            } else {
                $update_arr["status"] = 'err_req';
                $update_arr["error_message"] = $resp["error"];
            }
        }
        catch (Exception $e) {
            $update_arr["status"] = "err_req";
            $update_arr["error_message"] = $e->getMessage();
        } 
        if (array_key_exists("error_message", $update_arr)) {
            $update_arr["error_message"] = Str::limit($update_arr["error_message"], 125);
        }
        return $update_arr;
    }

    public function notify_lead_status($data) {   

        $acc_number = $data['acc_number'];
        $lead_status = $data['lead_status'];

        if ( $lead_status == Consts::PS_PENDING ) {
            return;
        }
        
        if( $lead_status == Consts::PS_ONBOARDED ) {
            // Inform partner to Enable Agent
            $this->notify_eligibility([$acc_number]);
        }
        else {
            // Inform partner to Disable Agent
            $failed_statuses = [
                Consts::PS_KYC_FAILED, 
                Consts::PS_INELIGIBLE
            ];
            if (in_array($lead_status, $failed_statuses)) {
                $this->notify_eligibility([$acc_number], True);
            }
        }
    }

    public function transfer_money( $acc_prvdr, $api_cred, $loan_txn, $amount ) {
        
        $endpoint = '/approveLoan';

        $api_details = [
            'base_url' => $acc_prvdr->api_url,
            'api_cred' => $api_cred
        ];

        $loan_doc_id = $loan_txn['loan_doc_id'];
        $loan_appl_doc_id = (new LoanApplicationRepositorySQL)->get_record_by('loan_doc_id',$loan_doc_id,['loan_appl_doc_id'])->loan_appl_doc_id;


        $payload = [
            'agent_id' => $loan_txn['to_acc_num'],
            'application_id' => $loan_appl_doc_id,
            'loan_id' => $loan_doc_id,
            'due_date' => $loan_txn['due_date']->toDateTimeString()
        ];

        $resp = $this->encrypt_invoke_n_decrypt($api_details, $endpoint, $payload);
        if ($resp['statusCode'] == 200) {
            $response = [
                "status" => 'success',
                "txn_id" => $resp['result']['payment_txn_id'],
                "amount" => $amount,
                "message" => $resp['result']['message'],
            ];
        }
        else {
            $response = [
                "status" => 'failed',
                "message" => $resp['error']
            ];
        }

        $addl_resp = [
            'screenshot_path' => null,
            'traceback' => ''
        ];
        $resp = array_merge($response, $addl_resp);
        return $resp;
    }

    public function reject_loan( $loan_appl_doc_id, $remarks ) {
        
        $endpoint = '/rejectLoan';

        $payload = [
            'application_id' => $loan_appl_doc_id,
            'reject_reason' => $remarks
        ];

        $this->call_rtn_api($endpoint, $payload);
    }

    public function notify_eligibility( $acc_numbers, $revoke_approval=False ) {
        
        $endpoint = '/approveAgents';
        $payload = [
            'agents' => $acc_numbers,
        ];
        if ($revoke_approval) {
            $payload['revokeApproval'] = True;
        }

        $this->call_rtn_api($endpoint, $payload);
    }
}
