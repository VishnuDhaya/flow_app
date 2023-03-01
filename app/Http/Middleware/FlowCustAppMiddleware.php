<?php

namespace App\Http\Middleware;

use App\Repositories\SQL\PersonRepositorySQL;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Response;
use Closure;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Config;

class FlowCustAppMiddleware
{
    public function handle($request, Closure $next)
    {
        Config::set('logging.default', 'cust_mobile_app');
        JWTAuth::getJWTProvider()->setSecret(env('CUST_JWT_SECRET'));
        $path = $request->path();
        $route = get_route($path);
        session()->put('log_prefix', $route);
        session()->put('channel', 'cust_app');
        $req_json = $request->json()->all();
        $request->data = $req_json;
        $user = auth()->user();
        $country_code = null;
        $mobile_num = null;
        $req_time = Carbon::now();
        if(isset($request->data['2l_country_code'])){
            $alt_country_code = $request->data['2l_country_code'];
            $country_code = DB::selectOne("select country_code from countries where alt_country_code = '{$alt_country_code}'")->country_code;
            unset($request->data['2l_country_code']);
        }
        if(isset($request->data['isd_code'])){
            $isd = $request['isd_code'];
            $country_code = get_country_code_by_isd($isd);
        }
        if(isset($request->data['country_code'])){
            $country_code = $request->data['country_code'];
        }
        if ($user) {
            $country_code = $user->country_code;
            $mobile_num = $user->mobile_num;
        }

        session()->put('country_code', $country_code);
        session()->put('username',$mobile_num);
        
        $should_log_resp = $this->should_log_resp($path);
        if($country_code){
            $request->data['country_code'] = $country_code;
            $req_time = log_api_req_in_db($request, $req_json, $country_code, false);
            if(auth()->user()){
                $lender_code = config('app.lender_code_config')[$country_code];
                session()->put('lender_code', $lender_code);
            }
        }


        $response = $next($request);

        $request->end = microtime(true);
        if($country_code) {
            log_api_resp_in_db($response, $req_time, $path, $should_log_resp);
        }
        log_req_resp($request, $response, $should_log_resp);

        return $response;
    }

    private function should_log_resp($path){
        return !in_array($path, ['app/cust_mob/get_faqs']);
    }

}