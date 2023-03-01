<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\CommonRepositorySQL;

class AfricasSMSMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($req, Closure $next)
    {
        
        $mobile_num = (isset($req->phoneNumber)) ? $req->phoneNumber : $req->from;
		[$mobile_num, $isd_code] = split_mobile_num($mobile_num);
		set_country_n_timezone_by_isd($isd_code);
        return $next($req);
    }
}
