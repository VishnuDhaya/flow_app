<?php

namespace App\Http\Controllers\CorePlatform;
use \App\Http\Controllers\Controller;
use JWTAuth;
use App\Models\CorePlatform\CoreUser;
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
use Response;
use Auth;
use Log;
//use App\Http\Controllers\ApiController;
//https://github.com/tymondesigns/jwt-auth/wiki/Creating-Tokens

class CoreUserAuthController extends Controller
{
    
     public function login(Request $request)
     { 
        $data = $request->data;
        $credentials = ["email" => $data["email"], "password" => $data["password"] ];
      if (! $token = auth('core')->attempt($credentials)) {
            //return response()->json(['error' => 'Username or Password is incorrect'], 401);
            return $this->respondWithError("Username or Password is incorrect");
        }
        $user_obj = auth('core')->user();
        $borrower_repo = new BorrowerRepositorySQL();      
        $borrower = $borrower_repo->get_cust_id($user_obj->person_id); 
        return $this->respondWithToken($token, $request->master_data_version,$user_obj, $borrower->cust_id);
    }
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
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

     public function getAuthenticatedUser()
          {
          try {

                    if (! $user = JWTAuth::parseToken()->authenticate()) {
                            return response()->json(['user_not_found'], 404);
                    }

                } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                        return response()->json(['token_expired'], $e->getStatusCode());

                } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                        return response()->json(['token_invalid'], $e->getStatusCode());

                } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                        return response()->json(['token_absent'], $e->getStatusCode());

                }

                  return response()->json(compact('user'));
          }
          
    /*protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }*/

    protected function respondWithToken($token, $master_data_version, $user_obj, $cust_id)
    {
        
        $country_code = $user_obj->country_code;
        $user['role_codes'] = $user_obj->role_codes;
        $user['privileges'] = (new AuthRepositorySQL())->get_privileges($user_obj->role_codes);
        $user['market'] = (new MarketRepositorySQL())->get_market_info($country_code);
       
        //$master_data =  $this->sync_master_data($user_obj->country_code, $master_data_version);
        $user['role_codes'] = $user_obj->role_codes;
        $person_repo = new PersonRepositorySQL();
        $user_name = $person_repo->full_name($user_obj->person_id);
        $expires_in = auth()->factory()->getTTL() * 60;
        $resp = [
            'status' => 'success',
            'status_code' => Res::HTTP_OK,
            'user_id' => $user_obj->id,
            'data_prvdr_code' => $user_obj->data_prvdr_code,
            'cust_id' => $user_obj->cust_id,
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $expires_in,
            //'master_data' => $master_data['master_data'], 
           // 'master_data_version' => $master_data['master_data_version'],           
            'app_version' =>  env('FLOW_APP_VERSION', "20190708"),
            'user' => $user,
            'user_id' => $user_obj->person_id,
            'user_name' => $user_name,
            'cust_id' => $cust_id,
            'country_code'=>$country_code
            
        ];
       
        session(['user_id' => $user_obj->id]);
        return response()->json($resp);
    
    }
     public function respondWithError($message){
        return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $message,
        ]);
    }
    public function respond($data){

        $headers = get_header(request()->headers->get('origin'));
        return Response::json($data, $data['status_code'], $headers);
    }
}