<?php
namespace App\Http\Middleware;
use App\Repositories\SQL\PersonRepositorySQL;
use Illuminate\Support\Facades\DB;
use Log;
use Response;
use Illuminate\Support\Facades\Session;
use JWTAuth;
use Closure;
use Config;
class FlowMiddleware 
{
  public function handle($request, Closure $next)
    {

        $req_json = $request->json()->all();
        $path = $request->path();
        $route = get_route($path);
        Log::warning($req_json);
        if(isset($req_json['2l_country_code'])) {
            $alt_country_code = $req_json['2l_country_code'];
            $country_code = DB::selectOne("select country_code from countries where alt_country_code = '{$alt_country_code}'")->country_code;
            unset($req_json['2l_country_code']);
            session()->put("country_code", $country_code);
        }
        if(str_contains($path,'api/mobile')){
            Config::set('logging.default', 'rm_mobile_app');
            $route = get_route($path, 2);
        }
        session()->put('log_prefix', $route);
        if(array_key_exists('acc_prvdr_code', $req_json)){
            session()->put('acc_prvdr_code', $req_json['acc_prvdr_code']);
            unset($req_json['acc_prvdr_code']);      
        }
        if(array_key_exists('ignore_nat_check', $req_json)){
            session()->put('ignore_nat_check', $req_json['ignore_nat_check']);
            unset($req_json['ignore_nat_check']);      
        }
        $request->data = $req_json;
        $this->should_log_resp = $this->should_log_resp($path);
        if(auth()->user()){
          if($request->is_mobile == true ){
            $user = auth('mobile')->parseToken()->authenticate();
          }else{
            $user = JWTAuth::parseToken()->authenticate();
          }

          if($user->country_code != '*'){
            $country_code = $user->country_code;
          }else{
            $country_code = get_country_code($req_json);
          }
          session()->put('country_code', $country_code);
          $email = $user->email;
          $username = get_username_from_email($email);
          \session()->put('username',$username);
          $request->data['country_code'] = $country_code;
          $lender_code = config('app.lender_code_config')[$country_code];
          session()->put('lender_code', $lender_code);
        }
        $log_prefix = $route ." | ".session('username');
        \session()->put('log_prefix', $log_prefix);
        
        // if(isset($country_code)){
        //     session()->put('country_code', $country_code); 
        //     Log::warning("COUNTRY CODE IN SESSION : " . session('country_code'));

        //     if ($country_code != '*') {
        //       $lender_code = config('app.lender_code_config')[$country_code];
        //       session()->put('lender_code', $lender_code);
        //     }
        //     else{
        //       Log::warning("### NO LENDER CODE FOR * ###");
        //     }              
        // }else{
        //     Log::warning("### NO COUNTRY CODE EXISTS IN THE REQUEST ###");
        // }


//        $req_time = log_api_req_in_db($request, $req_json, $country_code, false);
        
        if(str_contains($path,'api/mobile/call_log')){
                $req_time = log_api_req_in_db($request, $req_json, $country_code, false);
        }
  
        $response = $next($request);
        
        $request->end = microtime(true);
//        log_api_resp_in_db($response, $req_time, $path,  $this->should_log_resp);
        log_req_resp($request,$response, $this->should_log_resp);

    

        
        return $response;
    }

   private function should_log_resp($path){
    return !in_array($path, config('app.exempted_log_url'));
   }


    /* public function set_session($resp , $user_id){
          Log::warning($resp->message);
       
        session([
          'status_code' => $resp->status_code,
          'message' => $msg,
          'status' => $resp->status,
          'user_id' => $user_id
        ]);

      }*/
}
