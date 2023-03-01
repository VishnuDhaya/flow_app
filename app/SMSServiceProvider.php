<?php
namespace App;

use \GuzzleHttp\Client;
use Log;
class SMSServiceProvider {
	
	public function __invoke($recipients, $message){	
		
		/*$client = new \GuzzleHttp\Client();

		$response = $client->request('GET', 'http://simplysms.com/getapi.php?email=info@flowglobal.net&password=gomatoke18&sender=8777&message=hello&recipients=9994870894');

		echo $response->getStatusCode(); # 200
		echo $response->getHeaderLine('content-type'); # 'application/json; charset=utf8'
		echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
*/
		# Send an asynchronous request.
		$client = new \GuzzleHttp\Client();
        $email = "info@flowglobal.net";
        $password = "gomatoke18";
        $sender = "8777";
       // $message = 'message';
        //$recipients = '9994870894';
	//$recipient = "256772656752";
	$this->recipients = $recipients;
        $url = "http://simplysms.com/getapi.php";

        $final_url = "{$url}?email={$email}&password={$password}&sender={$sender}&message={$message}&recipients={$recipients}";

		 $request = new \GuzzleHttp\Psr7\Request('GET', $final_url);


		// $request = new \GuzzleHttp\Psr7\Request('GET',$url.'email='.$email.'&password='.$password.'&sender='.$sender.'&message='.$message.'&recipients='.$recipients);
		$this->status = false;
		$promise = $client->sendAsync($request)->then(function ($response) {
		    $response = trim($response->getBody());
		   
		    $pattern = "1701|{$this->recipients},1777|,";
		
		    if($pattern == $response){
		    	$this->status = true;
		    }else{
		    	Log::error("UNABLE TO SEND SMS : $response");
		    }    
		});

		$promise->wait();
	
		return $this->status;			
	}	


}