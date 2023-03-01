<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\FieldVisitRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use Illuminate\Support\Str;
use App\Services\FieldVisitService;
use App\Services\Mobile\RMService;



use Log;

class FieldVisitController extends ApiController
{

	public function checkin(Request $req){
		$data = $req->data;
        
        $field_serv = new FieldVisitService();
        $checkin_status = $field_serv->submit_checkin($data);
        if($checkin_status){
            return $this->respondData($checkin_status);
        }else{
            return $this->respondInternalError("Unknown Error");
            }
	}

    public function checkout(Request $req){
        $data = $req->data;
        
        $field_serv = new FieldVisitService();
        $checkout_status = $field_serv->submit_checkout($data);
        if($checkout_status){
            return $this->respondData($checkout_status);
        }else{
            return $this->respondInternalError("Unknown Error");
            }
    }

public function list_field_visits(Request $req)
    {
    	$data = $req->data;
    	$field_serv = new FieldVisitService();
        $visit_data = $field_serv->get_field_visits($data);
       	return $this->respondData($visit_data);

    }

public function get_field_visit_details(Request $req)
    {
        $data = $req->data;
        $field_serv = new FieldVisitService();
        $visit_data = $field_serv->get_field_visit_details($data);
        return $this->respondData($visit_data);

    }

    public function create_reg_schedule(Request $req)
    {
        $data = $req->data;
        $rm_serv = new RMService();
        $data['sch_purpose'] = 'new_cust_reg';
        $visit_data = $rm_serv->create_schedule($data);
        $message = "Schedule has been registered on Flow App successfully";
        return $this->respondData($visit_data,$message);

    }

    public function allow_force_checkin(Request $req){
        $data = $req->data;
        $field_serv = new FieldVisitService();
        $result = $field_serv->allow_force_checkin($data);
        if($result){
            return $this->respondWithMessage("RM is now allowed to force check-in at this customer's location");
        }else{
            return $this->respondWithError("This customer is already allowed to force checkin for today.");
        }
    }

    

}