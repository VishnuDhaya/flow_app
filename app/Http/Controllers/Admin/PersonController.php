<?php

namespace App\Http\Controllers\Admin;


use App\Models\FlowApp\AppUser;
use App\Models\InvestorUser;
use App\Services\CommonService;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Validators\FlowValidator;
use Log;
use App\Services\BorrowerService;
class PersonController extends ApiController
{

    public function update(Request $req){

        $data = $req->data;
        

        $check_validate = FlowValidator::validate($data, array("person"), __FUNCTION__);

        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
            
        }

        $borr_serv = new BorrowerService();

        $person = $borr_serv->update_person($data['person']);

//        $person = $person_repo->update($data['person']);
        if($data['person']['create_user']) {
            $person = (new PersonRepositorySQL)->find($data['person']['id']);
            $person->role = $data['person']['role'];
            $serv = new CommonService();
            $serv->create_user((array)$person);
        }

        if ($person) {
            return $this->respondSuccess('Person details updated successfully');
        }else{
            return $this->respondInternalError("Unknown Error");

        }

           
    }

    public function create_person(Request $req){
        $data = $req->data;
        $check_validate = FlowValidator::validate($data, array("person"), __FUNCTION__);
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate);
        }

        $serv = new CommonService();
        $response = $serv->create_person($data['person']);

        return $this->respondData($response);

    }

    public function view(Request $request){
        $data = $request->data;
        $person_repo = new PersonRepositorySQL();
        $person = $person_repo->find($data['person_id']);
        $person->user = null;
        if(AppUser::where('person_id', $person->id)->exists()){
            $person->user = 'app_user';
        }
        elseif(InvestorUser::where('person_id', $person->id)->exists()){
            $person->user = 'investor_user';
        }
        return $this->respondData($person);

   }

    public function list(Request $req){
        $data = $req->data;
        $person_repo = new PersonRepositorySQL();
        $persons = $person_repo->list($data);
        return $this->respondData($persons);
    }



}
