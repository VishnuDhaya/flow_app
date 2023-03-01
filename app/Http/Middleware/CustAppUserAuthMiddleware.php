<?php

namespace App\Http\Middleware;

use App\Repositories\SQL\BorrowerRepositorySQL;
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


class CustAppUserAuthMiddleware //extends Middleware
{ /**
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
        JWTAuth::getJWTProvider()->setSecret(env('CUST_JWT_SECRET'));
        $user = JWTAuth::parseToken()->authenticate();

        unset($request->data['token']);
        if($user->role_codes != 'customer'){
            thrw("You as a {$user->role_codes} can not login to customer app");
        }
            $market_info = (new MarketRepositorySQL())->get_market_info($user->country_code);
            if($market_info){
                $user->time_zone = $market_info->time_zone;
            }

        $cust = (new BorrowerRepositorySQL())->get_record_by('owner_person_id',$user->person_id,['cust_id']);

        session()->put('user_id', $user->id);
        session()->put('role_codes',$user->role_codes);
        session()->put('cust_id', $cust->cust_id);
        session()->put('channel', 'cust_app');

        unset($request->data['token']);
        unset($request->data['time_zone']);

        setPHPTimeZone($user->time_zone);


        return $next($request);


    }
}