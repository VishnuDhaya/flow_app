<?php



namespace App\Scripts\php;

use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use Log;
use App\Consts;

class CustNameScript{

    
    public static function getCustName(){

        session()->put('country_code','UGA');
        
        $borrower_repo = new BorrowerRepositorySQL();
        $borrowers = $borrower_repo->get_cca_customers('CCA');

        foreach($borrowers as $borrower){
            $cust_name[] = (new static)->getcustomer($borrower);

        }
        Log::warning($cust_name);
        return collect($cust_name);
    }

    private static function getcustomer($borrower){
        
        $client = new \GuzzleHttp\Client();
        $person_repo =  new PersonRepositorySQL();
        $market_repo = new MarketRepositorySQL();

        $market = $market_repo->get_isd_code(session('country_code'));

        $url = "http://20.80.160.35:7601/api/thirdparty/getcustomerdetail";
        $person_contact = $person_repo->get_person_name($borrower->owner_person_id);
        

        $request = [
            "username" => "FLOW_GLOBAL",
            "password" => "CN0ri9VbdJff",
            "phonenumber" => $market->isd_code. $borrower->data_prvdr_cust_id
        ];
        
		$response = $client->post($url, ['debug' => false, \GuzzleHttp\RequestOptions::JSON => $request 
		]);
		
		$response = json_decode($response->getBody(), true);
        
        $response['cust_id'] = $borrower->cust_id;
        $response['biz_name'] = $borrower->biz_name;
        $response['dp_cust_id'] = $borrower->data_prvdr_cust_id;
        $response['first_name'] = $person_contact->first_name;
        $response['last_name'] = $person_contact->last_name;
        
        return $response;
        
    }
}