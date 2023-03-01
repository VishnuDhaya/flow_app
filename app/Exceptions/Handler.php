<?php

namespace App\Exceptions;
use \Illuminate\Http\Response as Res;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\FlowCustomException;
use App\Exceptions\NotFoundFlowException;
use App\Exceptions\FlowKickOutException;
use \Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
       // App\Exceptions\FlowCustomException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
      
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $ex)
    {
        Log::info("INSIDE HANDLER");
        if(str_starts_with($request->path(), 'inv/')){
            $err_msg = $ex->getMessage();
            if ($ex instanceof QueryException ){
                $error = $ex->errorInfo;
                $json =  $this->handle_sql_error($error);
                $err_msg = $json->original['message'];
             }
            return response()->view('investorsite.error',['status_code' => Res::HTTP_INTERNAL_SERVER_ERROR,
                                                               'err_msg' => $err_msg]);
        }
         if ($ex instanceof QueryException ){
            $error = $ex->errorInfo;
            return $this->handle_sql_error($error);
         }
        else if ($ex instanceof TokenInvalidException ||
                  $ex instanceof TokenExpiredException ||
                  $ex instanceof JWTException){

            session(['auth_failed' => true]);  
            session(['user_id' => 0]); 
            return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_UNAUTHORIZED,      // 401
            'message' =>  "Token expired. Please login again"
             ], Res::HTTP_UNAUTHORIZED);
        }
         else if ($ex instanceof FlowKickOutException) {
            return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_UNAUTHORIZED,  // 401
            'message' =>  $ex->getMessage()
            ], Res::HTTP_UNAUTHORIZED);
        }
        else if ($ex instanceof NotFoundFlowException) {
            return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_NO_CONTENT,  // 204
            'message' =>  $ex->getMessage()
            ], Res::HTTP_NO_CONTENT);
        }
        else if ($ex instanceof FlowCustomException) {
            Log::warning("FlowCustomException HANDLER");
            return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_PRECONDITION_FAILED,  // 412
            'message' =>  $ex->getMessage()
            ], Res::HTTP_PRECONDITION_FAILED);
        }else if ($ex instanceof FlowSystemException) {
            return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_INTERNAL_SERVER_ERROR,  // 500
            'message' =>  $ex->getMessage()
            ], Res::HTTP_INTERNAL_SERVER_ERROR);
        }else if ($ex instanceof FlowValidationException) {
            return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_BAD_REQUEST, // 400
            'message' =>  $ex->getMessage()
        ], Res::HTTP_BAD_REQUEST);
        }else if ($ex instanceof MethodNotAllowedHttpException) {
            return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_NOT_FOUND,      // 404
            'message' =>  "CHECK API URL : {$request->getPathInfo()}"
             ], Res::HTTP_NOT_FOUND);
        }else {
            return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_INTERNAL_SERVER_ERROR,   // 500
            'message' =>  "INTERNAL SERVER ERROR : CHECK LOGS AT ROOT/storage/logs/ \n {$ex->getMessage()}"
             ], Res::HTTP_INTERNAL_SERVER_ERROR);
        }
        return parent::render($request, $exception);
    }

    private function respond($data, $status_code){
        $this->set_session($data);
        $headers = get_header(request()->headers->get('origin'));
        return Response::json($data, $status_code);
    }

    private function handle_sql_error($error_info){
        if(sizeof($error_info) == 3){
            $error_code = $error_info[0];
            $error_number = $error_info[1];
            $error_msg = $error_info[2];
            if($error_code == '23000' && $error_number == 1062){
                $message = "You are trying to insert duplicate record";
            }else{
                $message = $error_info[2];
            }
        }else{
            $message = "DB Exception occured";
        }
         return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_INTERNAL_SERVER_ERROR,      // 500
            'message' =>  $message
             ], Res::HTTP_INTERNAL_SERVER_ERROR);
    }
    public function set_session($resp){
        if(isset($resp['message'])){
            $msg = $resp['message'];
        }else{
            $msg = null;
        }
        session([
                'status_code' => $resp['status_code'],
                'message' => $msg,
                'status' => $resp['status']
                ]);
    }


}
