<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Response;
use \Illuminate\Http\Response as Res;
use Log;
use DB;
use App\Consts;
use App\Repositories\SQL\AuthRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\MasterDataRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use Illuminate\Support\Str;
class ApiController extends Controller
{
     /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth1');
      //  $this->beforeFilter('auth', ['on' => 'post']);

    }
    /**
     * @var int
     */
    protected $statusCode = Res::HTTP_OK;
    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    /**
     * @param $message
     * @return json responserespondData
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function respondCreated($message, $data=null){
        
        return $this->respond([
            'status' => 'success',
            'status_code' => Res::HTTP_CREATED,
            'message' => $message,
            'data' => $data
        ]);
        
    }
  
    public function respondData($data, $message = null,  $headers = []){
       
        return $this->respond([
                    'status' => 'success',
                    'status_code' => Res::HTTP_OK,
                    'message' => $message,
                    'data' => $data,
                    'server_time' => datetime_db(),
                    //'user_id' => 0
                ], $headers);
       
        
    }


    /**
     * @param Paginator $paginate
     * @param $data
     * @return mixed
     */
    protected function respondWithPagination(Paginator $paginate, $data, $message){
        
        $data = array_merge($data, [
            'paginator' => [
                'total_count'  => $paginate->total(),
                'total_pages' => ceil($paginate->total() / $paginate->perPage()),
                'current_page' => $paginate->currentPage(),
                'limit' => $paginate->perPage(),
            ]
        ]);
        return $this->respond([
            'status' => 'success',
            'status_code' => Res::HTTP_OK,
            'message' => $message,
            'data' => $data,
            'api_req_id' => session('api_req_id') 
        ]);
        
    }

    public function respondNotFound($message = 'Not Found!'){
       
        return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_NOT_FOUND,
            'message' => $message,
        ]);
        
    }

    public function respondInternalError($message){
       
        return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_INTERNAL_SERVER_ERROR,
            'message' => $message,
        ]);
        
    }
    public function respondValidationError($errors, $message = "Validation Error" ){
      
        return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_BAD_REQUEST, //HTTP_UNPROCESSABLE_ENTITY,
            'message' => $message,
            'data' => $errors
        ]);
        
    }

    public function respond($data){
        $headers = get_header(request()->headers->get('origin'));
        return Response::json($data, $data['status_code'], $headers);
    }

      public function set_session($resp){
       
        if(isset($resp['message'])){
            $msg = $resp['message'];
        }else{
            $msg = null;
        }
        session([
                'status_code' => $resp['status_code'],
                'message' => $msg,
                'status' => $resp['status']
                ]);
        

    }

    public function respondSuccess($message){
       
        return $this->respond([
                    'status' => 'success',
                    'status_code' => Res::HTTP_OK,
                    'message' => $message,
                    'server_time' => datetime_db(),
                ]);
        
    }


    public function respondWithError($message, $http_code = 422){
        $status_code = ($http_code == 422) ? Res::HTTP_UNPROCESSABLE_ENTITY : Res::HTTP_INTERNAL_SERVER_ERROR;

        return $this->respond([
            'status' => 'error',
            'status_code' => $status_code,
            'message' => $message,
        ]);
        
    }

    public function respondWithErrorAndData($message,array $data, $http_code = 429){
        $status_code = ($http_code == 429) ? Res::HTTP_TOO_MANY_REQUESTS : Res::HTTP_INTERNAL_SERVER_ERROR;

        return $this->respond([
            'status' => 'error',
            'status_code' => $status_code,
            'message' => $message,
            'data' => ['attempts' => $data['attempts'],
            'time_remaining' => $data['time_remaining']]

        ]);

    }
    
    public function respondWithMessage($message){
   
        return $this->respond([
            'status' => 'success',
            'status_code' => Res::HTTP_OK,
            'message' => $message,
            //'user_id' => 0
        ]);
        
    }

    public function respondWithResponseData($data){
        $resp_code = 1000;
       
        return $this->respond([
            'status' => 'success',
            'status_code' => Res::HTTP_OK,
            'response_code' => $resp_code,
            'response_message' => Consts::PARTNER_RESP_CODES[$resp_code],
            'data' => $data
        ]);
        
    }

    public function respondWithResponseMessage($message, $resp_code=9999){
       
        return $this->respond([
            'status' => 'success',
            'status_code' => Res::HTTP_OK,
            'response_code' => $resp_code,
            'response_message' => Consts::PARTNER_RESP_CODES[$resp_code],
            'message' => $message
        ]);
        
    }
    
    protected function respondWithToken($token, $master_data_version, $guard = null, $addl_data = [])
    {
        $user_obj = auth($guard)->user();
        $country_code = $user_obj->country_code;

        $user['role_codes'] = $user_obj->role_codes;

        $user['privileges'] = (new AuthRepositorySQL())->get_privileges($user_obj->role_codes);
        
        if($user_obj->country_code == "*" && (Str::contains($user_obj->role_codes, "super_admin") || Str::contains($user_obj->role_codes, "it_admin"))){
            
            $user['market_list'] = (new CommonRepositorySQL())->get_country_list();
            
        }else{

            $user['market'] = (new MarketRepositorySQL())->get_market_info($country_code);
            
        }



        if(Str::contains($user_obj->role_codes, 'investor') || Str::contains($user_obj->role_codes, "market_admin") || Str::contains($user_obj->role_codes, "ops_admin") || Str::contains($user_obj->role_codes, "operations_auditor")){

            $user['market']->country = (DB::selectOne("select country from countries where country_code = ?", [$country_code]))->country;
            $user['market_list'] = (new CommonRepositorySQL())->get_country_list();
        }

        $master_data =  $this->sync_master_data($user_obj->country_code, $master_data_version);

        $user['role_codes'] = $user_obj->role_codes;

        $person_repo = new PersonRepositorySQL();
        $user_name = $person_repo->full_name_by_sql($user_obj->person_id);
         
        
        $expires_in = auth()->factory()->getTTL() * 60;
        $data = $addl_data + ['access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $expires_in,
            'master_data' => $master_data['master_data'], 
            'master_data_version' => $master_data['master_data_version'],           
            'app_version' =>  env('FLOW_APP_VERSION', "20190708"),
            'user' => $user,
            'user_id' => $user_obj->person_id,
            'user_name' => $user_name,
            'app_config' => get_web_ui_config(),

        ];

        $resp = [
            'status' => 'success',
            'status_code' => Res::HTTP_OK,
            'data' => $data,
            'user_id' => $user_obj->id
            
        ];
       
        session(['user_id' => $user_obj->id]);
        session(['user_person_id' => $user_obj->person_id]);
        return response()->json($resp);
    
    }

    public function respondWithRefreshToken($token,$guard = null)
    {
        $user_obj = auth($guard)->user();

        $expires_in = auth()->factory()->getTTL() * 60;
        $data = ['access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => $expires_in,
                'user_id' => $user_obj->person_id,
            ];

        $resp = [
            'status' => 'success',
            'status_code' => Res::HTTP_OK,
            'data' => $data,
            'user_id' => $user_obj->id

        ];

        session(['user_id' => $user_obj->id]);
        session(['user_person_id' => $user_obj->person_id]);
        return response()->json($resp);
    }

    protected function respondWithTokenOnly($token, $data, $guard = null, $addl_data = [])
    {
        $user_obj = auth($guard)->user();
        $country_code = $user_obj->country_code;

        $expires_in = auth()->factory()->getTTL() * 60;
        $data = $addl_data + ['access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => $expires_in,
                'default' => $data,
                'user_id' => $user_obj->person_id,
            ];

        $resp = [
            'status' => 'success',
            'status_code' => Res::HTTP_OK,
            'data' => $data,
            'user_id' => $user_obj->id

        ];

        session(['user_id' => $user_obj->id]);
        session(['user_person_id' => $user_obj->person_id]);
        return response()->json($resp);

    }

   

    public function get_master_data(Request $req){
        $data =  $this->sync_master_data($req->country_code, $req->master_data_version);
        

      return $this->respondData($data);
    }

    private function sync_master_data($country_code, $ui_master_data_version){
        
         $master_data_repo = new MasterDataRepositorySQL();
         $master_data = null;
         $db_master_data_version = $master_data_repo->get_master_data_version();
        if( $ui_master_data_version != $db_master_data_version)
        {
              $master_data = (new CommonRepositorySQL())->get_master_data(["country_code" => $country_code,'data_type' => 'common'], true);
                $ui_master_data_version = $db_master_data_version;
        }

         $data = [
            'master_data' => $master_data, 
            'master_data_version' => $ui_master_data_version,    
            
        ];
        return $data;
    }
    
    public function getAuthenticatedUser()
    {
     try{

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
      
}
