<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\CommonService;
use App\Services\PartnerService;
use App\Validators\FlowValidator;
use App\Consts;
use App\Exceptions\FlowCustomException;
use App\Exceptions\FlowSystemException;
use Log;
use Exception;

class PartnerController extends ApiController
{
    public function __construct()
    {
        $this->partner_service = new PartnerService();
    }

    public function get_float_adv_amounts(Request $req) {
        $data = $req->data;
        $amounts = $this->partner_service->get_float_adv_amounts($data);
        return $this->respondWithResponseData($amounts);
    }

    public function get_float_adv_durations(Request $req) {
        $data = $req->data;
        $durations = $this->partner_service->get_float_adv_durations($data);
        return $this->respondWithResponseData($durations);
    }

    public function get_float_adv_products(Request $req) {
        $data = $req->data;
        $products = $this->partner_service->get_float_adv_products($data);
        return $this->respondWithResponseData($products);
    }

    public function get_elig_float_adv_products(Request $req) {
        $data = $req->data;
        $products = $this->partner_service->get_elig_float_adv_products($data);
        return $this->respondWithResponseData($products);
    }

    public function apply_float_advance(Request $req) {
        $data = $req->data;
        $loan_appl = $this->partner_service->apply_float_advance($data);
        return $this->respondWithResponseData($loan_appl);
    }

    public function get_float_adv_status(Request $req) {
        $data = $req->data;
        $fa_status = $this->partner_service->get_float_adv_status($data);
        return $this->respondWithResponseData($fa_status);
    }

    public function get_current_os(Request $req) {
        $data = $req->data;
        $os_amount = $this->partner_service->get_current_os($data);
        return $this->respondWithResponseData($os_amount);
    }

    public function get_last_n_float_advances(Request $req) {
        $data = $req->data;
        $last_n_fas = $this->partner_service->get_last_n_float_advances($data);
        return $this->respondWithResponseData($last_n_fas);
    }

    public function notify_repayment(Request $req) {
        $data = $req->data;
        $resp = $this->partner_service->notify_repayment($data);
        return $this->respondWithResponseData($resp);
    }

    public function req_acc_stmt(Request $req) {
        $data = $req->data;
        $resp = $this->partner_service->req_acc_stmt($data);
        return $this->respondWithResponseData($resp);   
    }

    public function notify_new_acc_stmt(Request $req) {
        $data = $req->data;
        $resp = $this->partner_service->notify_new_acc_stmt($data);
        return $this->respondWithResponseData($resp);
    }

    public function update_lambda_status(Request $req) {
        $data = $req->data;
        $resp = $this->partner_service->update_lambda_status($data);
        return $this->respondData($resp);
    }

    public function notify_cust_interest(Request $req) {
        $data = $req->data;
        $resp = $this->partner_service->notify_cust_interest($data);
        return $this->respondWithResponseData($resp);
    }

    public function check_lead_status(Request $req) {
        $data = $req->data;
        $resp = $this->partner_service->check_lead_status($data['acc_number']);
        return $this->respondWithResponseData($resp);
    }

    public function mock_req_acc_stmt(Request $req) {
        $data = $req->data;
        $resp = $this->partner_service->mock_req_acc_stmt($data);
        return $this->respondWithResponseData($resp);
    }
    
    public function mock_float_adv_approval(Request $req) {
        $data = $req->data;
        $resp = $this->partner_service->mock_float_adv_approval($data);
        return $this->respondData($resp);
    }

    public function send_internal_mail(Request $req) {
        $data = $req->data;
        send_email($data['view'], [config('app.app_support_email')], $data);
        return $this->respondSuccess('');
    }
}
