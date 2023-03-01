<?php

namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Exceptions\FlowKickOutException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use Illuminate\Support\Str;

class MobileAppAuthMiddleware extends Middleware
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
            $allowed_roles = ['super_admin', 'relationship_manager', 'market_admin', 'operations_admin', 'it_admin'];
            $user = JWTAuth::parseToken()->authenticate();
            

             unset($request->data['token']);
            if(!in_array($user->role_codes, $allowed_roles)){
                thrw("You as a {$user->role_codes} can not login to mobile app");
            }

              if(Str::contains($user->role_codes, "super") && $user->country_code =="*")
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
               session()->put('channel', 'rm_app');

               session(['user_person_id' => $user->person_id]);

               unset($request->data['token']);
               unset($request->data['time_zone']);
               
               setPHPTimeZone($user->time_zone);
               
             
               return $next($request);
         
         
        }
}
