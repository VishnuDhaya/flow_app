<?php

namespace App\Http\Middleware;


use Closure;
use Carbon\Carbon;
use App\Consts;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Exceptions\FlowKickOutException;

class InternalAPIUserAuthMiddleware //extends Middleware
{
   /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return response()->json(['status' => 'Not authenticated.']);
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    private function should_allow_mobile($path){
    return !in_array($path, [

    ]);
    }

    public function handle($request, Closure $next,  ...$guards)
    {

        $req_json = $request->json()->all();  
        
        if(array_key_exists('country_code', $req_json)){
            set_app_session($req_json['country_code']);  
        }

        $username = $request->header('username');
        $token = $request->header('token');
        $purpose = $request->header('purpose');
        
        $creds = config("app.internal_api_creds");
        
        if(Arr::exists($creds, $purpose)){

            $internal_api_creds = $creds[$purpose];

            if($internal_api_creds['username'] == $username && $internal_api_creds['token'] == $token) {
                return $next($request);
            }
            else{
                throw new FlowKickOutException("Invalid username or token in the request header");
            }
        }
        else{
            throw new FlowKickOutException("Purpose missing in the request header");
        }
    }
}
