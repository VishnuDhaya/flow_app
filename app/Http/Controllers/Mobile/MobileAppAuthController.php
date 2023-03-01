<?php

namespace App\Http\Controllers\Mobile;
use App\Http\Controllers\ApiController;
use JWTAuth;
use App\Models\CorePlatform\CoreUser;
use App\Models\FlowApp\AppUser;
use Illuminate\Http\Request;
use \Illuminate\Http\Response as Res;
use App\Repositories\SQL\AuthRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\MasterDataRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Services\BorrowerService;
use App\Services\Mobile\RMService;
use Response;
use Auth;
use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

//https://github.com/tymondesigns/jwt-auth/wiki/Creating-Tokens

class MobileAppAuthController extends ApiController
{
     const GAURD = 'app';

     public function login(Request $request)
     { 
        $allowed_roles = ['super_admin', 'relationship_manager', 'market_admin', 'operations_admin', 'it_admin'];

        $data = $request->data;
        $credentials = ["email" => $data["email"], "password" => $data["password"] ];
        if (! $token = auth(self::GAURD)->attempt($credentials)) {
            //return response()->json(['error' => 'Username or Password is incorrect'], 401);
            return $this->respondWithError("Username or Password is incorrect");
        }
        $user_obj = auth(self::GAURD )->user();
        
        if($user_obj->status != 'enabled'){
            return $this->respondWithError("Your Flow App account is disabled");
        }
       
       if(!in_array($user_obj->role_codes, $allowed_roles)){
            thrw("You as a {$user_obj->role_codes} can not login to mobile app");
        }

        session()->put('country_code', $user_obj->country_code);

        if(array_key_exists('messenger_token' , $data)){
            AppUser::where('id',$user_obj->id)->update(['messenger_token' => $data['messenger_token']]);
        }

        $person_repo = new PersonRepositorySQL();

        $rm_photo_file_name = $person_repo->get_record_by('id',  $user_obj->person_id, ['photo_pps']);
        $rm_photo_pps_path = get_file_path("persons",$user_obj->person_id,"photo_pps");
        $rm_pps = ['file_name' => $rm_photo_file_name->photo_pps, 'file_rel_path' =>  $rm_photo_pps_path];

        $rm_serv = new RMService();
        $count = DB::selectOne("select count(*) as cust_count from borrowers where flow_rel_mgr_id = ?",[$user_obj->person_id]);
        $addl_data_arr = ['rm_cust_count' => $count->cust_count,"mobile_config" => get_mobile_config(), 'rm_pps' => $rm_pps ];
        $pending_checkout = $rm_serv->get_pending_checkout_visits($user_obj->person_id);
        if($pending_checkout){
            $addl_data_arr['pending_checkout'] = $pending_checkout; 
        }




        return $this->respondWithToken($token, $request->master_data_version, self::GAURD, $addl_data_arr  );
    }
    public function logout()
    {
        auth(self::GAURD )->logout();
        return parent::respondWithMessage('Successfully logged out');
    }
     /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth(self::GAURD )->refresh());
    }
    public function me()
    {
        return response()->json(auth(self::GAURD )->user());
    }

    public function init_check(Request $request)
    {
        $country_code = session("country_code");
        $lastest = config("app.rm_app_version");
        $crnt = $request["app_version"];
        $update = "";
        $level = check_for_update_level($lastest, $crnt);
        if($level == 0){
            $update = "nill";
        }
        elseif ($level == 1){
            $update = "tweak";
        }
        elseif ($level > 1){
            $update = "major";
        }

        return $this->respondData(["force_upgrade" => $update]);
    }

}
