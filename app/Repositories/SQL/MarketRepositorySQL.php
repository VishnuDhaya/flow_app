<?php
namespace App\Repositories\SQL;
use App\Repositories\SQL\OrgRepositorySQL;
use App\Repositories\SQL\AddressRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\Market;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Exceptions\FlowCustomException;
use Illuminate\Database\QueryException;

class MarketRepositorySQL  extends BaseRepositorySQL implements BaseRepositoryInterface
{
	public function __construct()
  	{
  	  //$this->add_country_code(false);
      parent::__construct();
    }
	
	public function model(){
		return Market::class;
	}

	public function create(array $market_arr){
		try       
      	{
			DB::beginTransaction();	
			if(isset($market_arr['org']['id']))
			{
				$org_id  = $market_arr['org']['id'];
			}
			else
			{
				$org_id = (new OrgRepositorySQL())->create($market_arr['org']);
			}
			$head_person_id =(new PersonRepositorySQL())->create($market_arr['head_person']);
			$market_arr['org_id'] = $org_id;
			$market_arr['head_person_id'] = $head_person_id;
			$time_zone = (new CommonRepositorySQL())->get_time_zone($market_arr['country_code']);
			$market_arr['time_zone'] = $time_zone;
			parent::insert_model($market_arr);
			DB::commit();

            }
		catch (\Exception $e) {
			DB::rollback();
			Log::warning($e->getTraceAsString());
			if ($e instanceof QueryException){
				throw $e;
			}else{
			thrw($e->getMessage());
			}
		}	
		
		return $market_arr['country_code'];
	  
	}

	public function currency_code($market_id){
		$market = parent::find($market_id);
		return $market->currency_code;
	}

	public function view($country_code){
		$marketRepo = new MarketRepositorySQL();  	
	 	$market = $marketRepo->find_by_code($country_code);
	 	$org_id = $market->org_id;
		$org_details =  (new OrgRepositorySQL())->view($org_id); 
		$person_id = $market->head_person_id;
		$person_repo = new PersonRepositorySQL();
		$person_details = $person_repo->find($person_id);
		$market->org = $org_details;
		$market->head_person = $person_details;
		return $market;
	 }

	public function list($id){
		return DB::select("/*$this->api_req_id*/  SELECT m.status, m.id as market_id, m.country_code, currency_code,time_zone,name from markets m, orgs o left join addresses a on o.reg_address_id = a.id where m.org_id = o.id");
	}

	public function update(array $data){
		//dd($data);
		//$market_details = parent::find($data['id']);
		//$org_id = $market_details->org_id;
		//dd($data['org']);
		//$data['org']['id'] = $org_id;
		//dd($data['org']);
		$resp = parent::update_model($data);
		//$resp = (new OrgRepositorySQL())->update($data['org']); 
		//dd($resp);
		return $resp;
	}

	public function delete($id){
		throw new BadMethodCallException();
	}

	public function get_market_info($country_code){
		return parent::find_by_code($country_code, ['country_code', 'currency_code', 'time_zone','head_person_id', 'isd_code']);
	}

	public function get_isd_code($country_code){
		return DB::selectOne("/*$this->api_req_id*/  select isd_code from markets where country_code = ? limit 1", [$country_code]);
	}

	public function get_markets(){
		
		return DB::select("/*$this->api_req_id*/  select country_code, time_zone from markets where status = 'enabled'");
	}
	
	public function get_market($currency_code){
		
		return  DB::selectOne("/*$this->api_req_id*/  select isd_code, country_code from markets where currency_code = ?", [$currency_code]);
	}	

	public function get_market_countries(){

	    return DB::table('countries')
          ->select('countries.country','countries.country_code')
          ->join('markets','markets.country_code','=','countries.country_code')
          ->get();	
	       }

	public function create_otp($data){                 
           $mob_num = $data['mob_num']; 
           $otp_type=$data['otp_type'];                                
           $otp = rand(100000, 999999);          
           $message = "Your One Time Password is " . $otp;
        try{            
           $resp = DB::insert('insert into otp(otp,otp_type,mob_num)
	          values(?,?,?)',[$otp,$otp_type,$mob_num]);	     	 
	    return $resp;
           }
           catch (FlowCustomException $e) {
            throw new FlowCustomException($e->getMessage());
           }   
    }

}



