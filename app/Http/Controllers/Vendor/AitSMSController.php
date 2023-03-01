<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Services\Vendors\SMS\AitSMSService;
use Log;

class AitSMSController extends ApiController
{
    
    public function process_inbound_sms(Request $req){
        $serv = new AitSMSService;
        $serv->process_inbound_sms($req);
        return $this->respondSuccess('Received Code');
    }


    public function process_sms_delivery_report(Request $req){
        $serv = new AitSMSService();
        // $serv->process_sms_delivery_report($req);
        return $this->respondSuccess('Received report');
    }
    



}
