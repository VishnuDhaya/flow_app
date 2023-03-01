<?php
namespace App\Services\Vendors\Payment;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Services\AccountService;
use \GuzzleHttp\Client;
use Log;
use App;
use Illuminate\Support\Arr;
use Exception;

class ChapChapService {
	public function __construct($acc_prvdr, $api_cred){
        $this->country_code = session('country_code');
        $this->api_cred = $api_cred;
        $this->acc_prvdr = $acc_prvdr;
		// $this->url = "http://52.40.167.195:9097"."/api/third_party/third_party_transaction/";
		// $this->headers = [
		// 		    'username' => 'gtb_bank',        
		// 		    'token'    => 'nA4J87BcD1b4889js8',
		// 			];
	}
	
	public function transfer_money($to_acc_num, $amount){
        
        $request = json_decode($this->api_cred);
        $acc_prvdr_code = $this->acc_prvdr->acc_prvdr_code;

        $txn_ref = uniqid("TXN-{$acc_prvdr_code}-");
        
        if(App::environment('production')){
            $digital_signature = $this->generate_public_key_authentication_signature($amount, $request->service, $txn_ref);

        }else{
            //$digital_signature = $this->generatedigitalsignature($amount, $request, $txn_ref);

            $digital_signature = $this->generate_public_key_authentication_signature($amount, $request->service, $txn_ref);
            Log::warning($digital_signature);
    
    
            // $secret = file_get_contents('./certificate_pub.crt');
            // Log::warning('secret');
            // Log::warning($secret);
            // $data = $amount. $txn_ref. $request->service;
            // $concat_str = base64_encode($data);

            // $signature = hash_hmac('SHA1', $concat_str, $secret, true);
            // $digital_signature = base64_encode($signature);
            // Log::warning('digital_signature');
            // Log::warning($digital_signature);
        }
		
		$merchant = $this->getcustomerdetail($request);
        Log::warning($merchant);

        $request->MerchantNumber = $merchant['merchantNumber'];
      
        $request->DigitalSignature = $digital_signature;
        
        $transaction = $this->posttransaction($to_acc_num, $amount, $txn_ref, $request);
        Log::warning('transaction');
        Log::warning($transaction);
        // // TODO Wait 5 sec
        
       
        

        if($transaction['message'] == 'PROCESSING' && $transaction['status'] == 'PENDING'){
            $request->TranRef = $transaction['chapChapID'];
           
            static $number = 1;
            $retry_multiplier = 2;
            $retry_times = 6;
            $delay = 1;
            while($retry_times){
                $delay = $delay * $retry_multiplier;
                sleep($delay);
                $txn_status_resp = $this->TransactionStatus($request);
                
                if($txn_status_resp['status'] == 'SUCCESS'){
                    $response = ["status" => 'success',
                    "txn_id" => $txn_status_resp['tranRef'], # ID with slash comes here
                    "message" => $txn_status_resp['reason'],
                    "amount" => $amount
                    ];
                    break;
                }
                $retry_times--;
            }
        }
        else {
            $response = ["status" => $transaction['status'],
                        "txn_id" => $transaction['chapChapID'],
                        "message" => $transaction['message'],
                        "traceback" => $transaction['transactionRef']
                        ];
        }
        
        // $response = ["status" => 'FAILED',
        //                 "txn_id" => null,
        //                 "message" => 'DUPLICATE TRANSACTION REF',
        //                 "traceback" => $transaction['transactionRef']
        //                 ];              
        
        return $response;

    }

    private function generatedigitalsignature($amount, $request, $txn_ref){
        
        $client = new \GuzzleHttp\Client();

        $data = $amount. $txn_ref. $request->service;
        $api_method = "/generatedigitalsignature";
        $url = $this->acc_prvdr->api_url. $api_method;
        

        $final_url = "{$url}?data={$data}&biller={$request->username}";
        
        $response = $client->request('GET', $final_url);

        $signature = $response->getBody()->getContents();
      
        return $signature;
	}
	
	private function getcustomerdetail($request){
        
        $client = new \GuzzleHttp\Client();

        $api_method = "/getcustomerdetail";
        $url = $this->acc_prvdr->api_url. $api_method;
        $request->phonenumber = '256772656752';

		$response = $client->post($url, ['debug' => false, \GuzzleHttp\RequestOptions::JSON => $request 
		]);
		Log::warning($response->getBody());
		$response = json_decode($response->getBody(), true);
    
        return $response;
        
    }

    private function posttransaction($to_acc_num, $amount, $txn_ref, $request){

        $market_repo = new MarketRepositorySQL();

        $market = $market_repo->get_isd_code($this->country_code);
        
        $client = new \GuzzleHttp\Client();

        $api_method = "/posttransaction";
        $url = $this->acc_prvdr->api_url. $api_method;

        $body = (object)[   "username" => $request->username,
                            "password" => $request->password,
                            "phonenumber" => $market->isd_code. $to_acc_num,
                            "MerchantNumber" => $request->MerchantNumber,
                            "TranRef" => $txn_ref,
                            "ServiceCode" => $request->service,
                            "TranAmount" => $amount,
                            "DigitalSignature" => $request->DigitalSignature

                        ];
        Log::warning(json_encode($body));
        $response = $client->post($url, ['debug' => false, \GuzzleHttp\RequestOptions::JSON => $body 
        ]);
        Log::warning($response->getBody());
        $response = json_decode($response->getBody(), true);
        
        return $response;
        
	}
	
    private function TransactionStatus($request){

        $client = new \GuzzleHttp\Client();

        $api_method = "/TransactionStatus";
        $url = $this->acc_prvdr->api_url. $api_method;
        
		$response = $client->post($url, ['debug' => false, \GuzzleHttp\RequestOptions::JSON => $request 
		]);
		Log::warning($response->getBody());
		$response = json_decode($response->getBody(), true);
    
        return $response;
    }
    public function check_status($retry_times, $retry_multiplier){
        static $number = 1;
        $number = $number * $retry_multiplier;
        $retry_times--;
        return [$retry_times, $number];
    }
    // private function ValidatePhone($url){
    //     $client = new \GuzzleHttp\Client();

    //     $api_method = "/ValidatePhone";
    //     $url = $url. $api_method;
    //     $phonenumber = '256772656752';

    //     $final_url = "{$url}?data={$phonenumber}";
        
    //     //$request = new \GuzzleHttp\Psr7\Request('GET', $final_url);
    //     $response = $client->request('GET', $final_url);
    //     return $response->getBody();

    // }

    public function generate_public_key_authentication_signature($amount, $service_code, $txn_ref){
        
        $private_key_file_location = base_path()."/private1.key"; #"/certificate_pub.crt"; #"/flow_cca_private_key";
      
        $fh = fopen($private_key_file_location, 'r');
        if($fh === FALSE) {
            throw new Exception('Private key file could not be opened. Confirm your file location '.$private_key_file_location);
        }

        $private_key = fread($fh, 8192);
        fclose($fh);
        echo $private_key;
        $pkeyid = openssl_pkey_get_private($private_key);
        echo $pkeyid;
        if(is_bool($pkeyid)) {
            throw new Exception('Private key is invalid');
        }

        $data = $amount. $txn_ref. $service_code;
        openssl_sign($data, $signature, $pkeyid, 'sha256WithRSAEncryption');

        $signature_base64 = base64_encode($signature);

        openssl_free_key($pkeyid);

        return $signature_base64;
    }


	public function disburse_loan($acc_num, $amount, $loan_doc_id){	
		$api_method = "payment/sync/confirm";
		
		$client = new \GuzzleHttp\Client();
		
		$url = $this->url . $api_method;
		
		$req = ["phone_number" => $acc_num,
				"amount" => $amount,
				"reason" => "loan",
				"loan_reference" => $loan_doc_id];
		return "12345";	
		$response = $client->post($url, ['debug' => false,  'headers' => $this->headers,
		    \GuzzleHttp\RequestOptions::JSON => $req // or 'json' => [...]
		]);
		Log::warning($response->getBody());
		$response = json_decode($response->getBody(), true);
	
		if(Arr::has($response, ['status', 'request_id']) && $response['status']){
			return $response['request_id'];
		}else if (Arr::has($response, ('message'))){
			thrw($response['message']);
		}else{
			thrw("WARNING : Unable to check the disbursal status. DO NOT try again!\n". $response->getBody());
		}
		
    }
    
}
