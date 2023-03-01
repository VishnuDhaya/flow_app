<?php

namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Vendors\Payment\YoService;
use App\Services\Vendors\SMS\AitSMSService;
use App\Services\Vendors\SMS\YoSMSService;
use App\Services\BorrowerService;
use App\Services\LoanService;
use App\Services\Vendors\Payment\BeyonicService;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Services\Support\SMSService;
use App\Services\VendorService;
use Log;

use App\Services\Vendors\Partners\YoUganda\YoAPI;


class VendorController extends ApiController
{
    
    public function receive_yo_payment_notification(Request $req)
    {
        $yo_serv = new YoService();
        $status = $yo_serv->receive_payment_notification($req);
        
    	if($status){
            return $this->respondSuccess("Notification updated successfully");
        }else{
            return $this->respondInternalError("Unknown Error");
        }
    }
    public function receive_beyonic_payment_notification(Request $req)
    {
        $beyonic_serv = new BeyonicService();
        $status = $beyonic_serv->receive_payment_notification($req);

        if($status){
            return $this->respondSuccess("Notification updated successfully");
        }else{
            return $this->respondInternalError("Unknown Error");
        }
    }
    public function get_os_amt(Request $req)
    {
       
        
        session()->put('country_code', 'global');
        //session()->put('global', true);
        #session()->put('data_prvdr_code', '*');
        $borrow_serv = new BorrowerService();
       
        try{
            return  $borrow_serv->get_cust_os($req->cust_id);
        }
        catch(Exception $ex){
            return ["status" => "failed" , "message" => $ex->getMessage()];
        }
        
    }



}
