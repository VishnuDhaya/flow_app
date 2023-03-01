<?php

namespace App\Http\Controllers;


use App\Services\FlowInternalService;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Log;
use DB;
use App\Services\Mobile\USSDService;
use App\Services\HeartBeatService;
use App\Services\Vendors\Whatsapp\WhatsappWebService;


class FlowInternalController extends ApiController{

    public function process_forwarded_otp(Request $req)
    {
        $data = $req->all();
        $serv = new FlowInternalService();
        $data['entity'] = 'mtn_agent';
        $data['mob_num'] = "MTNMobile";
        $data['vendor'] = 'UMTN';
        $serv->log_forwarded_otp($data);
        return $this->respondSuccess("");
    }

    public function configure_ussd_accounts(Request $req)
    {
        $data = $req->data;
        USSDService::configure_ussd_accounts($data);
        return $this->respondSuccess('');
    }

    public function process_ussd_response(Request $req)
    {
        $data = $req->data;
        USSDService::process_ussd_response($data);
        return $this->respondSuccess('');
    }

    public function process_heartbeat_response(Request $req)
    {
        HeartBeatService::receive_heartbeat_pulse($req->data);
        return $this->respondSuccess('');
    }

    public function connect(Request $request)
    {
        $whatsapp = new WhatsappWebService();
        $data = $request->data;
        $user_id = DB::selectOne("select email from app_users where id=?",[session('user_id')]);
        $data['userId'] = $user_id->email;
        $result = $whatsapp->connect($data);
        return $this->respondData($result);
    }

    public function send(Request $request)
    {
        $whatsapp = new WhatsappWebService();
        $data = $request->data;
        $result = $whatsapp->send_message($data);
        return $this->respondData($result);
    }

    public function logout(Request $request)
    {
        $whatsapp =  new WhatsappWebService();
        $data = $request->data;
        $result = $whatsapp->logout($data);
        return $this->respondData($result);
    }

    public function get_sessions(Request $request)
    {
        $whatsapp =  new WhatsappWebService();
        $data = $request->data;
        $user_id = DB::selectOne("select role_codes, email from app_users where id=?",[session('user_id')]);
        if ($user_id->role_codes == "it_admin"){
            $data['userId'] = "";
        }
        else {
            $data['userId'] = $user_id->email;
        }
        $result = $whatsapp->get_sessions($data);
        return $this->respondData($result);
    }

    public function check_session_status(Request $request)
    {
        $whatsapp =  new WhatsappWebService();
        $data = $request->data;
        $result = $whatsapp->check_session_status($data);
        return $this->respondData($result);
    }
     

    public function process_transaction_sms(Request $req){
        $data = $req->data;
        (new FlowInternalService)->process_transaction_sms($data);
        return $this->respondSuccess("");   
    }

     public function save_ussd_response(Request $req){
        $data = $req->data;
        USSDService::save_ussd_response($data);
        return $this->respondSuccess('');
     }

     public function email_insufficient_balance(Request $req){
        $data = $req->data;
        (new FlowInternalService)->email_insufficient_balance($data);
        return $this->respondSuccess('');
     }
}
