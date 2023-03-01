<?php

namespace App\Http\Middleware;


use Closure;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;

class AccNumberValidationMiddleware //extends Middleware
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

    public function validate_acc_number($req_param) {

        $repo = new AccountRepositorySQL();
        $cust_data = $repo->get_record_by_many(['acc_number', 'acc_prvdr_code'],[$req_param, session('acc_prvdr_code')],['status']);

        if(isset($cust_data)) {
            if ($cust_data->status != 'enabled') {
                thrw("Agent Profile is not enabled", 1001);
            }
        }
        else {
            thrw("Agent Profile doesn't exist", 1002);
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
        $req_body = $request->all();
        
        if(array_key_exists('acc_number', $req_body)) {
            $this->validate_acc_number($req_body['acc_number']);
        }
                        
        $response =  $next($request);   
        return $response;
    }
}