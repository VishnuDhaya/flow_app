<?php
namespace App\Http\Controllers\CorePlatform;
use \App\Http\Controllers\Controller;
use JWTAuth;
use App\Models\CorePlatform\CoreUser;
use Illuminate\Http\Request;
use \Illuminate\Http\Response as Res;
use App\Services\BorrowerService;
use Response;
use Auth;
use Log;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
//use App\Http\Controllers\ApiController;
//https://github.com/tymondesigns/jwt-auth/wiki/Creating-Tokens

class MobileAppController extends ApiController
{
   public function get_user_profile(Request $request)
   {
        $data = $request->data;
        $borrower_serv = new BorrowerService();
        $cust_profile = $borrower_serv->get_cust_profile($data['cust_id']);
        return $this->respondData($cust_profile);
   }
	public function get_customer_details(Request $req)
	{
		$data = $req->data;
		$borrower_serv = new BorrowerService();
		$customer_details = $borrower_serv->get_customer_details($data['cust_id']);
		return $this->respondData($customer_details);  
	}
	public function get_rel_mgr(Request $req)
	{
		$data = $req->data;
		$market_repo = new MarketRepositorySQL();
		$person_repo = new PersonRepositorySQL();
		$market = $market_repo->get_market_info($data['country_code']);
		$rel_mgr_details = $person_repo->get_contact_rel_mgr($market->head_person_id);
		return $this->respondData($rel_mgr_details);  
	}
}