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

class FlowWalletAppAuthMiddleware extends Middleware
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
            $allowed_roles = config('app.wallet_app_allowed_roles');
            $user = JWTAuth::parseToken()->authenticate();
            
            $request->data = $request->all();
            unset($request->data['token']);
            if(!in_array($user->role_codes, $allowed_roles)){
                thrw("You as a {$user->role_codes} can not login to mobile app");
            }

            session()->put('country_code', $user->country_code);

            $market_info = (new MarketRepositorySQL())->get_market_info($user->country_code);
                
            session()->put('user_id', $user->id);
            session()->put('role_codes', $user->role_codes);

            session(['user_person_id' => $user->person_id]);

            unset($request->data['token']);
               
            setPHPTimeZone($market_info->time_zone);
               
             
            $response = $next($request);
        
            $request->end = microtime(true);
            //$this->api_response($response, $req_time);
            $this->log($request,$response);
        
            
            
            return $response;
         
         
        }

    protected function log($request,$response = null)
    {
        $duration = $request->end - $request->start;
        $url = $request->fullUrl();
        $method = $request->getMethod();
        $ip = $request->getClientIp();

        $log = "{$ip}: {$method}@{$url} - {$duration}ms \n".
        "******************************** Request ***************************\n : $request \n\n\n".
        "---------------------------------------------------------------------------------------\n\n\n".
        "\n$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ Response $$$$$$$$$$$$$$$$$$$$$$$$$$\n : $response ".
        "---------------------------------------------------------------------------------------\n\n\n";

        
        Log::info($log);
    }
}
