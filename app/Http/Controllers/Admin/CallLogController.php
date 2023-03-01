<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\CallLogService;

use Log;

class CallLogController extends ApiController
{

	public function start_call_log(Request $req){
		$data = $req->data;
		$log_serv = new CallLogService();
		$log = $log_serv->start_call_log($data);
		return $this->respondData($log);
	}
	
	public function complete_call_log(Request $req){
		$data = $req->data;
		$log_serv = new CallLogService();
		$log_serv->complete_call_log($data);
		return $this->respondSuccess("The call log has been registered on Flow App successfully");
	}
	
	public function list_call_logs(Request $req){
		$data = $req->data;
		$log_serv = new CallLogService();
		$logs = $log_serv->list_call_logs($data);
		return $this->respondData($logs);
	}

	public function cancel_call_log(Request $req){
		$data = $req->data;
		$log_serv = new CallLogService();
		$message = $log_serv->cancel_call_log($data);
		return $this->respondSuccess($message);
	}

	

	public function get_call_log_details(Request $req){
		$data = $req->data;
		$log_serv = new CallLogService();
		$log_details = $log_serv->get_call_log_details($data);
		return $this->respondData($log_details);
	}

	public function get_cs_rosters(Request $req){
		$data = $req->data;
		$log_serv = new CallLogService();
		$cs_devices = $log_serv->get_cs_rosters($data);
		return $this->respondData($cs_devices);
	}

	public function update_cs_status(Request $req){

		$data = $req->data;
		$log_serv = new CallLogService;
		$resp = $log_serv->update_cs_status($data);
		return $this->respondWithMessage($resp);
	}


}