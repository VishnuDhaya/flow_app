<?php

namespace App\Http\Middleware;


use Closure;
use App\Exceptions\FlowCustomException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Exceptions\FlowKickOutException;
use App\Exceptions\FlowValidationException;
use App\Http\Controllers\ApiController;
use Exception;

class PartnerAuthMiddleware //extends Middleware
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
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next,  ...$guards)
    {   
        $api_controller = new ApiController();
        
        try {

            $request->data = $request->json()->all();

            if (!$request->headers->has('username') || 
                !$request->headers->has('token') || 
                !$request->headers->has('partnercode')) 
            {
                thrw('Username or token or partnercode not set in the request header', 6001);
            }
            
                $username = $request->header('username');
                $token = $request->header('token');
                $partner_code = $request->header('partnercode');

                $creds = config("app.partner_creds");

            if(!Arr::exists($creds, $partner_code)) {
                thrw("Invalid partnercode in the request header", 6002);
            }

                session()->put('acc_prvdr_code', $partner_code);
                session()->put('channel', 'partner');

                $APP_ENV = config('app.env');
                $APP_ENV = ($APP_ENV == 'local') ? 'test' : $APP_ENV; //For testing

                $partner_creds = $creds[$partner_code][$APP_ENV];
                set_app_session($partner_creds['country_code']);
                
            if ( $request->is('api/partner/mock/*') && $APP_ENV == 'production' ) {
                thrw("Mock route not available in $APP_ENV environment");
            }

            if($partner_creds['username'] != $username || $partner_creds['token'] != $token) {
                thrw("Invalid username or token in the request header", 6002);
            }
                    
                $response =  $next($request);
                $e = $response->exception;
            
            if (!empty($e)) {
                if ($e instanceof FlowCustomException) {                                 
                    $code = $e->response_code;  
                    
                    return $api_controller->respondWithResponseMessage($e->getMessage(), $e->response_code);
                }
                elseif ($e instanceof FlowValidationException) {
                    return $api_controller->respondWithResponseMessage($e->getMessage(), 6003);
                } 
                elseif ($e instanceof Exception) {
                    return $api_controller->respondWithResponseMessage($e->getMessage());
                }           
            }
            
            return $response;
        }
        catch(FlowCustomException $e) {
            return $api_controller->respondWithResponseMessage($e->getMessage(), $e->response_code);
        }
        catch(Exception $e) {
            return $api_controller->respondWithResponseMessage($e->getMessage());
        }
    }
}