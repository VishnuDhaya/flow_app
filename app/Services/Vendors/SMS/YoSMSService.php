<?php
namespace App\Services\Vendors\SMS;
use DB;
use Log;
use Illuminate\Http\Request;


class YoSMSService {
    
    
    public function get_message(Request $req){
            $username = $req->username;
            $password = $req->password;
            $authenticated = auth_vendor('UYOU', 'SMS-IB', ['username' => $username, 'password' => $password]);
            if (!$authenticated) {
                thrw('Unable to authenticate vendor');
            }

            [$mobile_num, $isd_code] = split_mobile_num($req->sender);


            return ['mobile_num' => $mobile_num, 'message' => $req->message, 'isd_code' => $isd_code];

    }
    
    
   
}
        