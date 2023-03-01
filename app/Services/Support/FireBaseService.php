<?php

namespace App\Services\Support;

use App\Jobs\SendFCMJob;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use App\Models\FlowApp\AppUser;
use Illuminate\Support\Facades\Log;




class FireBaseService {
// define the scopes for your API call

    public function __invoke(array $data, string $messenger_token, bool $async = true, array $addl_options = []){
        $connection = $async ? config('queue.default') : 'sync';
        $data['country_code'] = session('country_code');
        SendFCMJob::dispatch($data, $messenger_token, $addl_options)->onConnection($connection);

    }

    public function send_message($data, $messenger_token, $addl_options = []){
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        // create middleware
        $project_id = env('MSGR_APP_PROJ_ID');
        $cred_path = env('FLOW_KEYS_PATH')."/{$project_id}.json";
        putenv("GOOGLE_APPLICATION_CREDENTIALS=$cred_path");

        $middleware = ApplicationDefaultCredentials::getMiddleware($scopes);
        $stack = HandlerStack::create();
        $stack->push($middleware);

        // create the HTTP client
        $client = new Client([
        'handler' => $stack,
        'base_uri' => 'https://www.googleapis.com',
        'auth' => 'google_auth'  // authorize all requests
        ]);

        
        $request = $this->get_notification_request($data, $messenger_token);
        $this->set_addl_options($request, $addl_options);
        
        // make the request
        //PROJECT_ID is found in Firebase Console Settings -> General tab
        $response = $client->post("https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send",
                           [ \GuzzleHttp\RequestOptions::JSON => $request]);

    }


    

    Public function get_notification_request($data, $messenger_token){

        $request = [
            "message"=> [
              "token" => $messenger_token,
              "data"=> $data,
              "android"=>[
                "priority"=> "high"
              ]
            ]
        ];
        return $request;
    }


    private function set_addl_options(&$request, $addl_options){
      if(isset($addl_options['ttl'])){
        $request['message']['android']['ttl'] = $addl_options['ttl'];
      }
    }

}
