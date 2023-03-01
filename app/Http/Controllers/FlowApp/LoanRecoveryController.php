<?php

namespace App\Http\Controllers\FlowApp;

use App\Models\LoanRecovery;
use App\Services\LoanRecoveryService;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Consts;
use App\Validators\FlowValidator;


class LoanRecoveryController extends ApiController{


    private $serv;

    public function __construct()
    {
        $this->serv = new LoanRecoveryService();
    }

    public function create_recovery_request(Request $request){
        $data = $request->data;
        FlowValidator::validate($data, array("loan_recovery"));
        $this->serv->create_recovery_request($data);
        return $this->respondSuccess("Saved");



    }

    public function check_ongoing_recovery(Request $request){
        $data = $request->data;
        $response = $this->serv->check_ongoing_recovery($data);
        return $this->respondData($response);
    }

    public function cancel_ongoing_recovery(Request $request){
        $data = $request->data;
        $this->serv->cancel_ongoing_recovery($data);
        return $this->respondSuccess("Cancelled Successfully");
    }

    public function capture_recovery(Request $request){
        $data = $request->data;
        $this->serv->capture_recovery($data);
        return $this->respondSuccess("Recorded");

    }


    public function list_recoveries(){
        $response = $this->serv->list_recoveries();
        return $this->respondData($response);
    }

}