<?php
namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use App\Consts;
use App\Repositories\SQL\OrgRepositorySQL;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\AccProvider;
use Illuminate\Support\Facades\Log;

/**
 * 
 */
class AccProviderRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface
{
	public function __construct()
    {
      parent::__construct();

    }
	//Insert into the account provider table
	public function model(){
			return AccProvider::class;
	}
	public function create(array $acc_prvdr)
	{

		$org_repo = new OrgRepositorySQL();
		
		if(isset($acc_prvdr['org']['id']))
		{
			$org_id = $acc_prvdr['org']['id'];
		}
		else
		{
			$org_id = $org_repo->create($acc_prvdr['org']);
		}
		$acc_prvdr['org_id'] = $org_id;
		//$acc_prvdr['reg_address_id'] = $addr_id;
		//dd($acc_prvdr);
		return  parent::insert_model($acc_prvdr);
	}
	//Updating the account provider table
	public function update(array $acc_prvdr)
	{
		return parent::update_model($acc_prvdr);
	}

	 public function delete($id){
        throw new BadMethodCallException();

    }

   public function list($data,array $required_values = ["*"]){
	  
		return parent::get_records_by_many(array_keys($data),array_values($data), $required_values);
	}

	public function get_acc_prvdr_name($account_prvdr_code)
	{
        return parent::get_record_by('acc_prvdr_code',$account_prvdr_code,['name']);
	}
	public function get_integration_type($account_prvdr_code)
	{	
		$acc = parent::get_record_by('acc_prvdr_code',$account_prvdr_code,['int_type']);
		// if($acc){
		// 	return $acc->int_type;
		// }
		return $acc ? $acc->int_type : null;
		
	}

    public function get_all_acc_prvdr_name_by_country($country_code){
        $acc_prvdrs_query = parent::get_records_by('country_code',$country_code,['acc_prvdr_code','name']);
        $acc_prvdrs = array();
        foreach($acc_prvdrs_query as $acc_prvdr){
            $acc_prvdrs[$acc_prvdr->acc_prvdr_code] = $acc_prvdr->name;
        }

        return $acc_prvdrs;

    }

}

?>