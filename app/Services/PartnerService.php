<?php

namespace App\Services;

use App\Services\LoanApplicationService;
use App\Services\LoanService;
use App\Services\BorrowerService;
use App\Services\Partners\CCA\ChapChapService;
use App\Services\Partners\UISG\UISGService;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Mail\FlowCustomMail;
use Illuminate\Support\Facades\Mail;
use Exception;
use App\Consts;
use App\Exceptions\FlowCustomException;
use App\Models\LoanApplication;
use App\Models\LoansView;
use App\Models\PartnerAccStmtRequests;
use App\Models\Person;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Services\Partners\RRTN\RRTNService;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Scripts\php\CustStatementProcessScript;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Validators\FlowValidator;
use AWS;
use App\Services\RepaymentService;
use Illuminate\Support\Facades\URL;
use DB;

class PartnerService {

    public function get_float_adv_amounts($data) {

        $data["req_parameter"] = $data["acc_number"];
        unset($data["acc_number"]);
        $loan_appl_service = new LoanApplicationService();
        $products = $loan_appl_service->product_search($data);
        $loan_products = $products["loan_products"];

        if (!empty($loan_products)) {
            $loan_amounts = [];
            foreach ($loan_products as $product) {
                $amount = $product->max_loan_amount;
                array_push($loan_amounts, $amount);
                $loan_amounts = array_unique($loan_amounts);
            }
        } else {
            thrw('Customer is not entitled for a float advance product', 2001);
        }

        $currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;

        return [
            "amounts" => $loan_amounts,
            "currency_code" => $currency_code
        ];
    }

    public function get_float_adv_durations($data) {

        $amount = $data["amount"];
        $data["req_parameter"] = $data["acc_number"];
        unset($data["acc_number"]);
        $loan_appl_service = new LoanApplicationService();
        $products = $loan_appl_service->product_search($data);
        $loan_products = $products["loan_products"];

        if (!empty($loan_products)) {
            $durations = [];
            foreach ($loan_products as $product) {
                if ($amount == $product->max_loan_amount) {
                    array_push($durations, $product->duration);
                }
            }
            if (empty($durations)) {
                thrw("There is no product for the given amount", 2001);
            }
        } else {
            thrw("Customer is not entitled for a float advance product", 2001);
        }
        return ["durations" => $durations];
    }

    public function get_float_adv_products($data) {

        $amount = $data["amount"];
        $duration = $data["duration"];
        $data["req_parameter"] = $data["acc_number"];
        unset($data["acc_number"]);
        $loan_appl_service = new LoanApplicationService();
        $products = $loan_appl_service->product_search($data);
        $loan_products = $products["loan_products"];

        $currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;

        if (!empty($loan_products)) {
            $matching_products = [];
            foreach ($loan_products as $product) {
                if ($amount == $product->max_loan_amount && $duration == $product->duration) {
                    $product_duration = $product->duration;
                    $due_date = Carbon::now()->addDays($product_duration)->toDateTimeString();

                    $matching_product["product_id"] = $product->id;
                    $matching_product["amount"] = $product->max_loan_amount;
                    $matching_product["duration"] = $product->duration;
                    $matching_product["proj_due_date"] = $due_date;
                    $matching_product["fee"] = $product->flow_fee;
                    $matching_product["penalty_per_day"] = $product->penalty_amount;
                    $matching_product["currency_code"] = $currency_code;

                    array_push($matching_products, $matching_product);
                }
            }
            if (empty($matching_products)) {
                thrw("Customer is not entitled for a float advance product", 2001);
            }
        } else {
            thrw("Customer is not entitled for a float advance product", 2001);
        }

        return ["products" => $matching_products];
    }

    public function get_elig_float_adv_products($data) {

        $data["req_parameter"] = $data["acc_number"];
        unset($data["acc_number"]);
        $loan_appl_service = new LoanApplicationService();
        $products = $loan_appl_service->product_search($data);
        $loan_products = $products["loan_products"];

        $currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;

        if (!empty($loan_products)) {
            $eligible_products = [];
            foreach ($loan_products as $product) {
                // if ($product->is_eligible) {
                if (($product->result_code == 'eligible') || ($product->result_code == 'requires_flow_rm_approval')) {
                    $product_duration = $product->duration;
                    // $due_date = Carbon::now()->addDays($product_duration)->endOfDay()->format('Y-m-d H:i:s');
                    $due_date = format_date( getDueDate($product_duration), 'Y-m-d H:i:s' );

                    $eligible_product["product_id"] = $product->id;
                    $eligible_product["amount"] = $product->max_loan_amount;
                    $eligible_product["duration"] = $product->duration;
                    $eligible_product["proj_due_date"] = $due_date;
                    $eligible_product["fee"] = $product->flow_fee;
                    $eligible_product["penalty_per_day"] = $product->penalty_amount;
                    $eligible_product["currency_code"] = $currency_code;

                    array_push($eligible_products, $eligible_product);
                }
            }
            if (empty($eligible_products)) {
                thrw("Customer is not entitled for a float advance product", 2001);
            }
        } else {
            thrw("Customer is not entitled for a float advance product", 2001);
        }

        return ["products" => $eligible_products];
    }

    public function compose_loan_application_details($loan_appl) {
        $loan_appl_details["fa_appl_doc_id"]  = $loan_appl->loan_appl_doc_id;
        $loan_appl_details["fa_doc_id"] = $loan_appl->loan_doc_id;
        $loan_appl_details["fa_status"] = $loan_appl->status;
        $loan_appl_details["product_id"] = $loan_appl->product_id;
        $loan_appl_details["amount"] = $loan_appl->loan_principal;
        $loan_appl_details["duration"] = $loan_appl->duration;
        $loan_appl_details["fee"] = $loan_appl->flow_fee;
        $loan_appl_details["currency_code"] = $loan_appl->currency_code;
        return $loan_appl_details;
    }

    public function apply_float_advance($data) {
        
        $product_info = $data['products'];
        $validate_product = ['id' => $product_info['product_id'], 'max_loan_amount' => $product_info['amount'], 'duration' => $product_info['duration'], 'flow_fee' => $product_info['fee'], 'penalty_amount' => $product_info['penalty_per_day']];
        $products = (new LoanProductRepositorySQL())->get_record_by_many(array_keys($validate_product), array_values($validate_product));
        if(is_null($products)) {
            thrw("No such product exists", 2001);
        }

        $cust_id = (new AccountRepositorySQL())->get_cust_id_by_account($data["acc_number"], session('acc_prvdr_code'));
        $prod_id = $data["products"]["product_id"];

        $loan_appl_service = new LoanApplicationService();
        $loan_application = $loan_appl_service->apply_fa_by_product($cust_id, $prod_id);

        $loan_appl = $loan_application["loan_application"];
        $loan_appl_details = $this->compose_loan_application_details($loan_appl);
        
        $resp['float_advance'] = $loan_appl_details;
        return $resp;

    }

    public function get_float_adv_status($data) {

        $data["acc_number"] = $data["acc_number"];
        unset($data["acc_number"]);

        $data["loan_appl_doc_id"] = $data["fa_appl_doc_id"];
        $loan_appl_service = new LoanApplicationService();
        $loan_appl = $loan_appl_service->get_application($data);
        
        $loan_appl_details = $this->compose_loan_application_details($loan_appl);
        $resp['float_advance'] = $loan_appl_details;
        return $resp;

    }

    public function get_current_os($data) {

        $borrower_service = new BorrowerService();
        $borrowers = $borrower_service->search_borrower(
            $data["acc_number"],
            ['cust_id', 'ongoing_loan_doc_id', 'country_code', 'data_prvdr_code']
        );

        $borrower = $borrowers[0];
        $ongoing_loan_doc_id = $borrower->ongoing_loan_doc_id;

        if (is_null($ongoing_loan_doc_id)) {
            thrw("No ongoing loan", 4002);
        }
        
        $required_fields = ['loan_doc_id', 'status', 'product_id', 'loan_principal', 'duration', 'flow_fee', 'provisional_penalty', 'due_date', 'currency_code', 
                            'paid_principal', 'paid_fee', 'penalty_collected', 'penalty_collected', 'penalty_waived', 'penalty_days',
                            'tot_penalty', 'os_principal', 'os_fee', 'os_penalty', 'os_total'];
        // $loan = (new LoanRepositorySQL())->get_outstanding_loan($ongoing_loan_doc_id, $required_fields);
        $loan = (new LoansView)->get_record_by('loan_doc_id', $ongoing_loan_doc_id, $required_fields);

        $os_loan["fa_doc_id"] = $loan->loan_doc_id;
        $os_loan["fa_status"] = $loan->status;
        $os_loan["product_id"] = $loan->product_id;
        $os_loan["amount"] = $loan->loan_principal;
        $os_loan["duration"] = $loan->duration;
        $os_loan["fee"] = $loan->flow_fee;
        $os_loan["due_date"] = $loan->due_date;
        $os_loan["paid_principal"] = (float)$loan->paid_principal;
        $os_loan["paid_fee"] = (float)$loan->paid_fee;
        $os_loan["penalty_per_day"] = (float)$loan->provisional_penalty;
        $os_loan["penalty_days"] = $loan->penalty_days;
        $os_loan["tot_penalty"] = (float)$loan->tot_penalty;
        $os_loan["paid_penalty"] = (float)$loan->penalty_collected;
        $os_loan["waived_penalty"] = $loan->penalty_waived;
        $os_loan["os_principal"] = $loan->os_principal;
        $os_loan["os_fee"] = $loan->os_fee;
        $os_loan["os_penalty"] = $loan->os_penalty;
        $os_loan["os_total"] = $loan->os_total;
        $os_loan["currency_code"] = $loan->currency_code;

        return ["float_advance" => $os_loan];
    }

    public function get_last_n_float_advances($data) {

        $loan_service = new LoanService();
        try {
            $response = $loan_service->loan_search(['acc_number' => $data["acc_number"], 'mode' => "search", "last_n_fas" => "10"]);
        } catch (FlowCustomException $e) {
            if ($e->response_code == 8001) {
                thrw("No previous FAs", 8001);
            }
        }

        $loans = $response["results"];
        $float_advances = [];
        foreach ($loans as $loan) {
            $float_advance["fa_doc_id"] = $loan->loan_doc_id;
            $float_advance["fa_status"] = $loan->status;
            $float_advance["product_id"] = $loan->product_id;
            $float_advance["amount"] = $loan->loan_principal;
            $float_advance["duration"] = $loan->duration;
            $float_advance["fee"] = $loan->flow_fee;
            $float_advance["penalty_per_day"] = (float)$loan->provisional_penalty;
            $float_advance["due_date"] = $loan->due_date;
            $float_advance["currency_code"] = $loan->currency_code;

            array_push($float_advances, $float_advance);
        }
        return ["float_advances" => $float_advances];
    }

    public function notify_repayment($data) {

        $validate_arr["partner_data"] = $data;
        $validate_arr["country_code"] = session('country_code');

        FlowValidator::validate($validate_arr, array("partner_data"), __FUNCTION__);

        $borrower_repo = new BorrowerRepositorySQL();
        $cust_data = $borrower_repo->get_record_by('acc_number', $data['acc_number'], ['ongoing_loan_doc_id']);

        if (isset($cust_data->ongoing_loan_doc_id)) {
            $account_repo = new AccountRepositorySQL();
            $acc_prvdr_code = session('acc_prvdr_code');
            $repayment_account = $account_repo->get_record_by_many([ 'network_prvdr_code', 'acc_number', 'status'], [$acc_prvdr_code, $data['repayment_acc_number'], 'enabled'], ['id']);

            if (isset($repayment_account)) {
                $loan_txn = [
                    "loan_doc_id" => $cust_data->ongoing_loan_doc_id,
                    "txn_date" => $data["payment_datetime"],
                    "paid_date" => $data["payment_datetime"],
                    'txn_exec_by' => '',
                    "txn_id" => $data["payment_txn_id"],
                    "amount" => $data["payment_amount"],
                    "to_ac_id" => $repayment_account->id,
                    "txn_mode" => "wallet_transfer",
                    "send_sms" => true,
                    "is_part_payment" => true,
                    "waive_penalty" => true
                ];

                $repay_serv = new RepaymentService();
                $result = $repay_serv->capture_repayment($loan_txn);
                $resp["flow_payment_ref_id"] = $result["loan_txn_id"];
                return $resp;
            } else {
                thrw("No such repayment account exists", 5010);
            }
        } else {
            thrw("No ongoing loan exists for the agent.", 4002);
        }
    }

    public function get_presigned_url($key) {
        $s3Client = AWS::createClient('s3');
        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => env("S3_BUCKET"),
            'Key' => $key
        ]);

        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string)$request->getUri();
        return $presignedUrl;
    }

    public function invoke_lambda($data, $lambda_function) {

        $lambda_client = AWS::createClient('lambda');
        $lambda_client->invoke(array(
            "FunctionName" => $lambda_function,
            "InvocationType" => "Event",
            "Payload" => json_encode($data),
        ));
    }

    private function get_partner_service($partner)
    {
        if($partner == 'UISG'){
            return new UISGService();
        }
        if($partner == 'RRTN'){
            return new RRTNService();
        }
        if($partner == 'CCA'){
            return new ChapChapService();
        }
    }

    public function req_acc_stmt($data) {

        try {
            DB::beginTransaction();

            $flow_req_id = uniqid();
            (array_key_exists('acc_prvdr_code', $data)) ? session()->put('acc_prvdr_code',$data['acc_prvdr_code']) : NULL;
            $data["acc_prvdr_code"] = $acc_prvdr_code = session("acc_prvdr_code");
            $acc_number = $data["acc_number"];
            $data["req_time"] = datetime_db();
            $data["country_code"] = session("country_code");

            $data["flow_req_id"] = $flow_req_id;
            $data["object_key"] = "{$acc_prvdr_code}/{$flow_req_id}";
            $data["presigned_url"] = $this->get_presigned_url($data["object_key"]);
            $data["orig_flow_req_id"] = (array_key_exists('orig_flow_req_id', $data)) ? $data["orig_flow_req_id"] : $flow_req_id;

            $partner_stmt_req_repo = new PartnerAccStmtRequests();
            $partner_stmt_req_repo->insert_model($data);

            $stmt_procurement_config = config('app.agent_stmt_procure_method');
            if (in_array($acc_prvdr_code, $stmt_procurement_config['req_to_partner'])) {
                $partner_serv = $this->get_partner_service($acc_prvdr_code);
                $update_arr = $partner_serv->call_stmt_req_api($data);
            }
            elseif (in_array($acc_prvdr_code, $stmt_procurement_config['stmt_from_rm_app'])) {
                $update_arr = [
                    'presigned_url' => $data['presigned_url'],
                    'status' => 'requested'
                ];
            }
            else {
                thrw("Partner Statement Request integration not yet complete");
            }

            $update_arr["flow_req_id"] = $flow_req_id;
            $partner_stmt_req_repo->update_model($update_arr, "flow_req_id");
            DB::commit();
            return $update_arr;
        }
        catch (Exception $e){
            DB::rollback();
            throw new Exception($e->getMessage());
        }      
    }

    public function retry_stmt_req($data) {

        $data = (array)$data;
        $partner_stmt_req_repo = new PartnerAccStmtRequests();
        $retry_attempts = $partner_stmt_req_repo->get_records_by('orig_flow_req_id', $data["orig_flow_req_id"], ['flow_req_id']);
        $data['retries'] = count($retry_attempts);

        if ($data['retries'] < config('app.stmt_req_max_retries')) {
            $this->req_acc_stmt($data);
        } else {
            $recipient = config('app.app_support_email');
            Mail::to($recipient)->queue((new FlowCustomMail('repeat_acc_stmt_req_failure', $data))->onQueue('emails'));
        }
    }

    public function compose_payload_and_invoke_lambda($req_data, $record_data) {

        $req_data["lambda_status"] = "invoking_lambda";
        (new PartnerAccStmtRequests)->update_model($req_data, "flow_req_id");
        unset($req_data["resp_time"]);
        
        $payload = [
            "object_key" => $record_data->object_key,
            "acc_number" => $record_data->acc_number,
            "flow_req_id" => $req_data['flow_req_id'],
        ];

        $lead_id = $record_data->lead_id;
        $payload['lead_id'] = $lead_id;
        $payload['file_json'] = (isset($req_data['file_json'])) ? $req_data['file_json'] : null;
        $payload['acc_prvdr_code'] = $record_data->acc_prvdr_code;
        
        if($lead_id !== null) {
            $lead_repo = new LeadRepositorySQL;
            $lead_repo->update_model(['id'=> $lead_id, 'status' => Consts::PENDING_DATA_PROCESS,'score_status' => Consts::SC_PENDING_DATA_PROCESS]);
        }

        // $lambda_function = get_export_lambda($record_data->acc_prvdr_code);
        // $this->invoke_lambda($payload, $lambda_function);
        invoke_step_function($payload, 'ETLStateMachine');
        $req_data["lambda_status"] = "lambda_invoked";
        
        return $req_data;
    }

    public function notify_new_acc_stmt($data) {

        try{
            DB::beginTransaction();
            $partner_stmt_req_repo = new PartnerAccStmtRequests();
            $data["resp_time"] = datetime_db();

            $record_data = $partner_stmt_req_repo->get_record_by('flow_req_id', $data['flow_req_id'], ['orig_flow_req_id', 'lead_id', 'acc_number', 'status', 'object_key', 'acc_prvdr_code', 'start_date', 'end_date', 'country_code']);
            if (!isset($record_data)) {
                thrw("Given 'flow_req_id' doesn't exist", 5002);
            }

            $status = $record_data->status;
            if ($data["status"] == "success") {
                if ($status == "requested") {
                    $data = $this->compose_payload_and_invoke_lambda($data, $record_data);
                    $partner_stmt_req_repo->update_model($data, "flow_req_id");
                } 
                elseif ($status == 'success') {
                    thrw("Callback for this 'flow_req_id' is already received.", 5001);
                }
            } 
            elseif ($data["status"] == "failed") {
                if ($status == "requested") {
                    $this->retry_stmt_req($record_data);
                    $partner_stmt_req_repo->update_model($data, "flow_req_id");
                }
            }
            
            DB::commit();
            return $data;
        }
        catch (FlowCustomException $e){
            DB::rollback();
            throw new FlowCustomException($e->getMessage(), $e->response_code);
        }
        catch (Exception $e){
            DB::rollback();
            throw new Exception($e->getMessage());
        } 
    }

    public function sync_stmt_req_w_lambda($data) {

        $required_fields = ['flow_req_id', 'status', 'lambda_status', 'error_message'];
        $acc_stmt_data = array_intersect_key($data, array_flip( $required_fields));
        $acc_stmt_data["error_message"] = array_key_exists("error_message", $acc_stmt_data) ? Str::limit($acc_stmt_data["error_message"], 125) : NULL;
        
        $partner_stmt_req_repo = new PartnerAccStmtRequests();

        $partner_req_record = $partner_stmt_req_repo->get_record_by('flow_req_id', $data['flow_req_id'], ['lead_id', 'country_code', 'acc_prvdr_code', 'acc_number']);
        if (!isset($partner_req_record)) {
            thrw("Given 'flow_req_id' doesn't exist", 5002);
        }
        $partner_stmt_req_repo->update_model($acc_stmt_data, "flow_req_id");
        if ($data['lambda_status'] == "score_calc_success") {
            (new CustStatementProcessScript)->delete_stmt_folder($partner_req_record->country_code, $partner_req_record->acc_prvdr_code, $partner_req_record->acc_number);
        }

        return $partner_req_record->lead_id;
    }

    public function get_lead_update_info($data, $lead_file_json, $channel) {

        $update_data = [];
        $file_json = (isset($data['file_json'])) ? $data['file_json'] : json_decode($lead_file_json, true);

        if ($data['status'] == 'success') {
            
            if ($file_json) {
                $file_json['file_err'] = NULL;
                $update_data['file_json'] = json_encode($file_json);
            }

            if($data['lambda_status'] == 'score_calc_success') {
                if ($data['result'] == 'ineligible') {
                    $status = Consts::INELIGIBLE_CUST;
                }
                else {
                    $status = ($channel == 'partner') ? Consts::PENDING_RM_ALLOC : Consts::PENDING_KYC;
                }

                $update_data['status'] = $status;
                $update_data['run_id'] = $data['flow_req_id'];
                $update_data['score_status'] = Consts::SC_SCORED;
            }
        }
        else {
            if ($file_json) {
                $file_json['file_err'] = 'Unknown error'; 
                $update_data['file_json'] = json_encode($file_json);
            }
        } 
        return $update_data;
    }

    public function update_lambda_status($data) {
        
        try{
            DB::beginTransaction();
            $lead_id = $this->sync_stmt_req_w_lambda($data);
            
            if ($lead_id !== null) {
                
                $lead_repo = new LeadRepositorySQL;
                $lead_data = (array)$lead_repo->find($lead_id, ['file_json', 'channel', 'acc_prvdr_code', 'account_num', 'biz_name', 'country_code']);
                $update_data = $this->get_lead_update_info($data, $lead_data['file_json'], $lead_data['channel']);
                unset($lead_data['file_json']);

                if (!empty($update_data)) {
                    $update_data['id'] = $lead_id; 
                    $lead_repo->update_model($update_data);

                    if (isset($update_data['status'])) {
                        if( $update_data['status'] == Consts::PENDING_RM_ALLOC ) {
                            $recipient = get_ops_admin_email();
                            Mail::to($recipient)->queue((new FlowCustomMail('lead_rm_assign', $lead_data))->onQueue('emails'));
                        }
                        elseif( $update_data['status'] == Consts::INELIGIBLE_CUST ) {
                            $this->notify_lead_status( $lead_id );
                        }
                        if ($update_data['status'] == Consts::PENDING_KYC) {
                            (new LeadService())->send_file_process_notification($lead_id);
                        }
                    }
                }
            }
            DB::commit();
            return $data;
        }
        catch (Exception $e){
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }

    public function notify_cust_interest($data) {

        $acc_prvdr_code = session('acc_prvdr_code');
        $country_code = session('country_code');

        $lead_data = [];
        if (isset($data['mobile_num'])) {
            $data['mobile_num'] = (string)(int)$data['mobile_num'];
        }
        if(array_key_exists('gps',$data)){
            if ( gettype($data['gps']) == "array") {
                $gps_arr = $data['gps'];
                $gps_string = implode(",", $gps_arr);
            }
            else {
                $gps_string = $data['gps'];
                $gps_arr = explode(",", $gps_string);
            }

            if(count($gps_arr) > 1){
                $data['latitude'] = $gps_arr[0];
                $data['longitude'] = $gps_arr[1];
                $lead_data['gps'] = $gps_string;
            }
        }

        $validate_arr['partner_data'] = $data;
        $validate_arr['country_code'] = $country_code;
        FlowValidator::validate($validate_arr, array('partner_data'), __FUNCTION__);
        
        try{
            DB::beginTransaction();

            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $biz_name = (isset($data['biz_name'])) ? $data['biz_name'] : implode([$first_name, $last_name], " ");

            $lead_data += [  
                            'biz_name' => $biz_name,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'acc_prvdr_code' => $acc_prvdr_code,
                            'account_num' => $data['acc_number'],
                            'country_code' => $country_code,
                            'mobile_num' => $data['mobile_num'],
                            'status' => Consts::PENDING_DATA_UPLOAD,
                            'score_status' => Consts::SC_PENDING_DATA_UPLOAD
                        ];
            
            $lead_id = (new LeadService())->create_lead([ 'lead' => $lead_data ]);
            $req_data = [
                            'lead_id' => $lead_id,
                            'acc_number' => $data['acc_number'],
                            'acc_prvdr_code' => $acc_prvdr_code,
                            'start_date' => Carbon::today()->subMonths(3)->toDateString(),
                            'end_date' => Carbon::today()->toDateString(),
                        ];
            $this->req_acc_stmt($req_data);
            DB::commit();
        }
        catch (FlowCustomException $e){
            DB::rollback();
            throw new FlowCustomException($e->getMessage(), $e->response_code);
        }
        catch (Exception $e){
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }
    
    public function get_lead_partner_status_info($lead_data) {
        
        $lead_status = $lead_data->status;
        $score_status = $lead_data->score_status;
        
        // Status doesn't change after these states are reached
        $supported_statuses = [Consts::KYC_FAILED => Consts::PS_KYC_FAILED, Consts::CUSTOMER_ONBOARDED => Consts::PS_ONBOARDED, Consts::INELIGIBLE_CUST => Consts::PS_INELIGIBLE];
        $partner_status = (in_array($lead_status, array_keys($supported_statuses))) ? $supported_statuses[$lead_status] : Consts::PS_PENDING;	
        
        if ($partner_status == Consts::PS_PENDING) {
            $lead_stage = (int)explode('_', $lead_status)[0];
            $stage_after_kyc_pass = (int)explode('_', Consts::PENDING_ENABLE)[0];

            // Assessment Passed Case
            if($score_status == Consts::SC_SCORED) {
                $partner_status = Consts::PS_ELIGIBLE;
            }
            // KYC passed cases
            elseif( $lead_stage >= $stage_after_kyc_pass ) {
                $partner_status = Consts::PS_KYC_PASSED;
            }
        }

        $resp = ['acc_number' => $lead_data->account_num, 'lead_status' => $partner_status];
        // Reason required if KYC was rejected
        if ($partner_status == Consts::PS_KYC_FAILED) {
            $resp['reason'] = $lead_data->close_reason;
        }

        return $resp;
    }

    public function check_lead_status($acc_number) {
        
        $lead_repo = new LeadRepositorySQL();
        $lead_data = $lead_repo->get_record_by_many(['account_num', 'acc_prvdr_code', 'profile_status'], [$acc_number, session('acc_prvdr_code'), 'open'], ['status', 'account_num', 'close_reason', 'score_status']);
        is_null($lead_data) ? thrw("An open lead doesn't exist for this agent") : NULL;
        
        $resp = $this->get_lead_partner_status_info($lead_data);
        return $resp;
    }

    public function notify_lead_status( $lead_id ) {

        $lead_data = (new LeadRepositorySQL)->find($lead_id,['status', 'channel', 'account_num', 'acc_prvdr_code', 'close_reason', 'score_status']);
        if ($lead_data && $lead_data->channel == 'partner') {
            $stmt_procurement_config = config('app.agent_stmt_procure_method');
            if ( in_array($lead_data->acc_prvdr_code, $stmt_procurement_config['req_to_partner'])) {
                $partner_serv = $this->get_partner_service($lead_data->acc_prvdr_code);
                $payload = $this->get_lead_partner_status_info($lead_data);
                $partner_serv->notify_lead_status($payload);
            }
        }
    }

    public function mock_req_acc_stmt($data) {
        $req_data = [
            'acc_number' => $data['acc_number'],
            'acc_prvdr_code' => session('acc_prvdr_code'),
            'start_date' => Carbon::today()->subMonths(3)->toDateString(),
            'end_date' => Carbon::today()->toDateString(),
        ];

        return $this->req_acc_stmt($req_data);
    }

    public function mock_float_adv_approval($data) {

        $loan_appl_doc_id = $data['fa_appl_doc_id'];
        $data['loan_appl_doc_id'] = $loan_appl_doc_id;
        $data['appr_reason'] = 'test';

        $user_id_by_country = ['UGA' => 16, 'RWA' => 56];
        $user_id = $user_id_by_country[session('country_code')];
        session()->put('user_id', $user_id);

        $flow_rel_mgr_id = (new LoanApplicationRepositorySQL)->find_by_code($loan_appl_doc_id, ['flow_rel_mgr_id'])->flow_rel_mgr_id;
        session()->put('user_person_id', $flow_rel_mgr_id);
        
        $cust_id = (new AccountRepositorySQL())->get_cust_id_by_account($data['acc_number'], session('acc_prvdr_code'), 'enabled');
        $data['cust_id'] = $cust_id;
        $data['credit_score'] = 0;
        $apprvl_resp = (new LoanApplicationService())->approval($data);

        unset($apprvl_resp['loan']);
        return $apprvl_resp;
    }

    public function reject_loan($loan_appl_id) {
        $loan_appl = (new LoanApplicationRepositorySQL)->find($loan_appl_id, ['acc_prvdr_code', 'loan_appl_doc_id', 'remarks']);
        $serv = $this->get_partner_service($loan_appl->acc_prvdr_code);
        $serv->reject_loan($loan_appl->loan_appl_doc_id, $loan_appl->remarks);
    }
}