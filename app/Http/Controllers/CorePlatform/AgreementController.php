<?php

namespace App\Http\Controllers\CorePlatform;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;
use App\Consts;
use App\Services\AgreementService;
use Illuminate\Support\Facades\Log;
class AgreementController extends ApiController
{
	public function __construct()
	{
	$this->aggr_serv = new AgreementService();
	}
  
    public function get_mobile_agreement(Request $req)
    {
        $data = $req->data;
        $application = $this->aggr_serv->get_mobile_agreement($data['cust_id']);
        return $this->respondData($application);  
    }
   public function save_agreement(Request $req){
        $data = $req->data;
        $update_status = $this->aggr_serv->save_agreement($data);
        return $this->respondData($update_status);
    }
   
}