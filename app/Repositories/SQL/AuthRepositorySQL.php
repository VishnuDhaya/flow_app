<?php
namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use App\Consts;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\AccProvider;
/**
 * 
 */
class AuthRepositorySQL extends BaseRepositorySQL 
{
	public function __construct()
    {
      parent::__construct();

    }

	public function get_privileges($role_codes){

		//$result = DB::select("select  SUBSTRING_INDEX(priv_code , '/', 1 ) module, GROUP_CONCAT(priv_code) as priv_codes from app_role_privileges where role_code in ( ?) GROUP BY SUBSTRING_INDEX(priv_code , '/', 1 )", explode("," ,$role_codes));

		$result = DB::select("/*$this->api_req_id*/ select  SUBSTRING_INDEX(priv_code , '/', 1 ) module, GROUP_CONCAT(SUBSTRING_INDEX(priv_code , '/', -1 )) as priv_codes from app_role_privileges where role_code in ( ?) GROUP BY SUBSTRING_INDEX(priv_code , '/', 1 )", explode("," ,$role_codes));

		$final_result = [];
		foreach($result as $item){
			$final_result[$item->module] = $item->priv_codes;

		}
		return $final_result;

	}




}	