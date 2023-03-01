<?php
namespace App\Http\Controllers\CorePlatform;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use Illuminate\Http\Request;
use \Illuminate\Http\Response as Res;
use Response;
use Auth;
use Log;

class CommonController extends ApiController{


public function get_market_list(Request $req){
  try{
      $data = $req->data;
      $market_repo = new MarketRepositorySQL();
      $data = $market_repo->get_market_countries();      
  return $this->respondData($data);
  }
    catch (FlowCustomException $e) {
    throw new FlowCustomException($e->getMessage());
  }
  }

    public function create_customer_enquiry(Request $req){
        try{

            $data = $req->data;          
            $common_repo = new CommonRepositorySQL();       
            $status = $common_repo->create_customer_enquiry($data);
          if ($status) {
            return $this->respondSuccess('success');
            }else{

            return $this->respondInternalError("Unknown Error");
            }
         }
         catch (FlowCustomException $e) {
                throw new FlowCustomException($e->getMessage());
        }                
    }   
   public function create_register_otp(Request $req) {
    try{
      
            $data = $req->data;
            $oto_repo = new MarketRepositorySQL();
            $data = $oto_repo->create_otp($data);       
            return $this->respondData($data);
       }
    catch (FlowCustomException $e) {
                throw new FlowCustomException($e->getMessage());
        }  
   } 
}

