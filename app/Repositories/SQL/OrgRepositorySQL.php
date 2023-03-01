<?php
namespace App\Repositories\SQL;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\Org;
use Log;
class OrgRepositorySQL  extends BaseRepositorySQL implements BaseRepositoryInterface
{

	public function __construct()
  	{
      parent::__construct();

    }
	function model(){
		return Org::class;
	}
	public function create(array $org, $biz_address_id = null){
			if($biz_address_id){
				$addr_id = $biz_address_id ;
			}
			else if($org['reg_address']){
				$addr_id = (new AddressInfoRepositorySQL())->create($org['reg_address']);
			}
		    $org["reg_address_id"] = $addr_id ;
			$org_id = parent::insert_model($org);
			return $org_id;
	}
	public function view($org_id, array $models = ["*"],array $columns = ["org" => ["*"], "address" => ["*"]]){
		$org = null;
		if(in_array("*", $models) || in_array("org", $models)){
			$org = parent::find($org_id,$columns["org"]);
		}
		if(in_array("*", $models) || in_array("address", $models)){ 
			$address_id = $org->reg_address_id;
			$address = (new AddressInfoRepositorySQL())->find($address_id,$columns["address"]);
			$org->reg_address = $address;
	
	    }
		return $org;		

	}
	public function update(array $org){
		return parent::update_model($org);
	}
	public function delete($id){
		throw new BadMethodCallException();
	}
	public function list($country_code){
		return parent::get_records_by_country_code();
	}
}
