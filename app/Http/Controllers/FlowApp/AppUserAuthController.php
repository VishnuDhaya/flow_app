<?php

namespace App\Http\Controllers\FlowApp;
use App\Notifications\CreatePasswordNotify;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use JWTAuth;
use App\Http\Controllers\ApiController;
use App\Models\FlowApp\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use \Illuminate\Http\Response as Res;
use Response;
use App\Consts;
//use App\Http\Controllers\ApiController;
//https://github.com/tymondesigns/jwt-auth/wiki/Creating-Tokens

class AppUserAuthController extends ApiController
{
    public function register(Request $request)
    {
        $user = AppUser::create([
             'email'    => $request->email,
             'password' => bcrypt($request->password),
             'role_codes' => $request->role_codes,
             'belongs_to' => $request->belongs_to,
             'belongs_to_code' => $request->belongs_to_code,
             'country_code' => $request->country_code
       ]);

        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    public function login(Request $request)
    {
        $data = $request->data;
   
        $credentials = ["email" => $data["email"], "password" => $data["password"] ];
        //dd($credentials);

        if (! $token = auth()->attempt($credentials)) {
            //return response()->json(['error' => 'Username or Password is incorrect'], 401);
            return $this->respondWithError("Username or Password is incorrect");
        }

        if(auth()->user()->role_codes == 'relationship_manager' && env('APP_ENV') == 'production'){
            return $this->respondWithError("RMs are not authorised to access WEB portal. Please use RM MOBILE APP");
        }
        
       
        if(auth()->user()->status != 'enabled'){
            return $this->respondWithError("Your Flow App account is disabled");
        }
        session()->put('country_code', auth()->user()->country_code);
        return $this->respondWithToken($token, $request->master_data_version);
    }

    public function logout(Request $req)
    {
        auth()->logout();
        return parent::respondWithMessage('Successfully logged out');
        //return response()->json(['message' => 'Successfully logged out']);
    }

     /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    public function me()
    {
        return response()->json(auth()->user());
    }

    public function sendSetPasswordEmail($data)
    {
        $token = Str::random(64);

        DB::table('password_resets')->updateOrInsert(['email' => $data['email_id']],
            ['token' => Hash::make($token),
             'created_at' => Carbon::now()]
        );

        Notification::route('mail', [$data['email_id'] => $data['first_name']])->notify(new CreatePasswordNotify($token, $data['first_name'], route('password.reset',$token)));
    }

   
 
}
