<?php

namespace App\Http\Controllers\Admin;
use Exception;

use App\Repositories\SQL\MarketRepositorySQL;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Model\Market;
use App\Http\Requests;
use App\Validators\FlowValidator;
use JWTAuth;
use Response;

//use Illuminate\Http\Response as Res;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class MarketController extends ApiController
{

    // protected $marketRepo;	
    
    // public function __construct()
    // {
	   // $this->marketRepo = new MarketRepositorySQL();

    // }
    
    public function list(Request $request){
         $marketRepo = new MarketRepositorySQL();
        $markets = $marketRepo->list(null);
        return $this->respondData($markets);
    }

    public function create(Request $request){

    	$data = $request->data;	
        $fv = new FlowValidator();
        $check_validate = $fv->validate($data, array("market","org","head_person", "reg_address" ),__FUNCTION__);

        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
            
        }
       $marketRepo = new MarketRepositorySQL();  
        $currency_code = $marketRepo->create($data['market']);
            
        if ($currency_code) {
             return $this->respondCreated("New market created successfully",$currency_code);
        }else{
            return $this->respondInternalError("Unknown Error");

        }
      
   }

    public function view(Request $request){
  
        $data = $request->data;
        $marketRepo = new MarketRepositorySQL();
        $market = $marketRepo->view($data['country_code']);

        return $this->respondData($market);

   }

   public function update(Request $request){
        $data = $request->data;
        //dd($data);
        $check_validate = FlowValidator::validate($data, array("market"),__FUNCTION__);

        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
            
        }
        $marketRepo = new MarketRepositorySQL();
        $update_status = $marketRepo->update($data['market']);
        if($update_status){
            return $this->respondSuccess("Market updated successfully");
        }else{

            return $this->respondInternalError("Unknown Error");
        }

   }

}
