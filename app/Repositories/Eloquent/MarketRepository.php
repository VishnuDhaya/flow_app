<?php
namespace App\Repositories\Eloquent;

use Illuminate\Database\QueryException;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Eloquent\OrgRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Market;
use App\Models\Org;
use App\Models\Address;

class MarketRepository extends BaseRepository
{
	
	protected $orgRepo;
	public function __construct(OrgRepository $orgRepo)
    {
		//parent::__construct(app());
        	//$this->orgRepo = $orgRepo;
	}
	
	function model(){
		return "App\\Models\\Market";
	}


	function get_market($market_id){
		$market = Market::find($market_id)->with('org')->get();
		//$market->org->with('address')->get();
		return $market;
	}

	function create_market1(array $market_arr){
	     
        $market_arr['org_id'] = 0;
        $market = Market::create($market_arr);
        $this->orgRepo->create_org_for($market, $market_arr['org']);
       

	}


	function create_market(array $market_arr){

			
			//$org = new Org($market_arr['org']);
           //$addr = new Address($market_arr['org']['reg_address']);

			
			
			//$org = Org::create($market_arr['org']);	
			$org = new Org($market_arr['org']);			
			$addr = Address::create($market_arr['org']['reg_address']);
			$org->address()->associate($addr)->save();
			$queries = DB::getQueryLog();
        	return $queries;
			/*$org = new Org($market_arr['org']);
			//$org = Org::create($market_arr['org']);	
			$addr = Address::create($market_arr['org']['reg_address']);
			$addr->org()->save($org);
			*/
	}



}



