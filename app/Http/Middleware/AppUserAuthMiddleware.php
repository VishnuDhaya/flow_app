<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Carbon\Carbon;
use JWTAuth;
use App\Consts;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Exceptions\FlowKickOutException;

use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;


class AppUserAuthMiddleware //extends Middleware
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

      
        public function handle($request, Closure $next,  ...$guards)
        {
             if($request->is_mobile == true ){
                

               $user = auth('mobile')->parseToken()->authenticate();
             }else{
               $user = JWTAuth::parseToken()->authenticate();
             }
               unset($request->data['token']);
               
               if($user->status != 'enabled'){
                    throw new FlowKickOutException("Unable to authenticate user");
               
               }
               if((Str::contains($user->role_codes, "super")  || Str::contains($user->role_codes, "it_admin") )&& $user->country_code =="*")
               {
                  $user->time_zone = $request->data['time_zone'];
               }else{
                  if($user->country_code != session('country_code')){
                    throw new FlowKickOutException("Unable to authenticate user for country : ". session("country_code"));
                  }
                  $market_info = (new MarketRepositorySQL())->get_market_info($user->country_code);
                  if($market_info){
                    $user->time_zone = $market_info->time_zone;
                  }
               }
            
               session()->put('user_id', $user->id);
               session()->put('role_codes', $user->role_codes);
               session()->put('channel', 'web_app');

               session(['user_person_id' => $user->person_id]);

               unset($request->data['token']);
               unset($request->data['time_zone']);
               
               setPHPTimeZone($user->time_zone);
               
             
               return $next($request);
         
        }

    
        private function addTimeZoneCookie($user, $request, $response){
            if(! $request->cookie('timezone')){//} && ! is_null($user)){
                return $response->withCookie(cookie('timezone', $user->timezone, 120));
            }
            return $response;
        }
}
