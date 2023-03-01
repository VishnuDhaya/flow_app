<?php
namespace App\Services\Vendors\SMS;

use \GuzzleHttp\Client;
use Log;
use Illuminate\Support\Str;
class SimplySMSService {
	
	public function __invoke($recipients, $message, $country_code = 'UGA'){
		
		$client = new \GuzzleHttp\Client(); 
        $email = "info@flowglobal.net";
        $password = "password123";
		$sender = "8777";
    
		$this->recipients = $recipients;
        $url = "http://simplysms.com/getapi.php";

        $final_url = "{$url}?email={$email}&password={$password}&sender={$sender}&message={$message}&recipients={$this->recipients}";
		Log::warning('$final_url');
		Log::warning($final_url);
		$request = new \GuzzleHttp\Psr7\Request('GET', $final_url);
		
		$this->status = false;
		$promise = $client->sendAsync($request)->then(function ($response) {
		    $response = trim($response->getBody());
		    $pattern = "1701|{$this->recipients},1777|,";
		    Log::warning("SENT SMS : $response");
		    if($pattern == $response){
		    	$this->status = true;
		    }else{
		    	Log::error("UNABLE TO SEND SMS : $response");
		    }    
		});

		$promise->wait();
	
		return ['status' => $this->status ? 'delivered' : 'send_failed'];
	}


}
