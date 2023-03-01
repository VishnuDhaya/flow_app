<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\Support\SMSService;
use Log;

class SMSController extends ApiController
{
    public function search_sms_logs(Request $req){
        $data= $req->data;
        $serv = new SMSService;
        $result = $serv->search_sms_logs($data);
        return $this->respondData($result);

    }

}