<?php

namespace App\Http\Middleware;


use Closure;
use App\Exceptions\FlowCustomException;
use Log;
use App\Http\Controllers\ApiController;
use App\Services\Partners\FlowCrypt;
use Exception;
use Config;
use Symfony\Component\HttpFoundation\ParameterBag;

class PartnerEncryptMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api_controller = new ApiController();
        Config::set('logging.default', 'flow_partner');

        try {

            $password = config('app.partner_enc_pass');
            $flow_crypt = new FlowCrypt($password);
            $payload = $request->getContent();
            $decrypted = $flow_crypt->decrypt($payload);

            $payload = new ParameterBag($decrypted);
            Log::warning("PARTNER REQ DECRYPTED");
            Log::warning(json_encode($payload));
            $request->setJson($payload);

            $response = $next($request);
        } catch (FlowCustomException $e) {
            $response = $api_controller->respondWithResponseMessage($e->getMessage(), $e->response_code);
        } catch (Exception $e) {
            $response = $api_controller->respondWithResponseMessage($e->getMessage());
        } finally {
            $resp_data = $response->getData();
            Log::warning("PARTNER RESP BEFORE ENCRYPT");
            Log::warning(json_encode($resp_data));
            $resp_data = $flow_crypt->encrypt($resp_data);
            $response->setData($resp_data);

            $request->end = microtime(true);
            log_req_resp($request, $response);
            return $response;
        }
    }
}
