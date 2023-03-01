<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;

class AfricasVoiceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request,Closure $next)
    {
        $mobile_num = $request -> callerNumber;
		[$mobile_num, $isd_code] = split_mobile_num($mobile_num);
		set_country_n_timezone_by_isd($isd_code);
        $request->mobile_num = $mobile_num;
        return $next($request);
    }
}
