<?php

namespace App\Http\Controllers\Mobile;
use App\Http\Controllers\ApiController;
use JWTAuth;
use Illuminate\Http\Request;
use \Illuminate\Http\Response as Res;
use Response;
use Auth;
use Log;

//https://github.com/tymondesigns/jwt-auth/wiki/Creating-Tokens

class FlowWalletAppAuthController extends ApiController
{
     const GUARD = 'app';
     public function login(Request $request)
     { 
        $allowed_roles = config('app.wallet_app_allowed_roles');
        
        $data = $request->all();
        $credentials = ["email" => $data["email"], "password" => $data["password"] ];
        if (! $token = auth(self::GUARD)->setTTL(100000000)->attempt($credentials)) {
            //return response()->json(['error' => 'Username or Password is incorrect'], 401);
            return $this->respondWithError("Username or Password is incorrect");
        }
        $user_obj = auth(self::GUARD )->user();
        
        if($user_obj->status != 'enabled'){
            return $this->respondWithError("Your Flow App account is disabled");
        }
       
       if(!in_array($user_obj->role_codes, $allowed_roles)){
            thrw("You as a {$user_obj->role_codes} can not login to mobile app");
        }

        session()->put('country_code', $user_obj->country_code);



        return $this->respond([
            'status' => 'success',
            'status_code' => Res::HTTP_OK,
            'access_token' => $token,
        ]);
    }
     

}
