<?php
namespace App\Repositories\Eloquent;

use Illuminate\Database\QueryException;
use Prettus\Repository\Eloquent\BaseRepository;

class OrgRepository extends BaseRepository
{

	function model(){
		return "App\\Models\\Org";
	}


	function createOrg(array $org_arr){
	    try {
  	          //$org_arr['created_by'] = 1;		 
		  $res = $this->create($org_arr);
		  return $res->id;
	        } catch (QueryException $e) {
        	    throw new \Exception($e);
	        }		


	}

	function create_org_for($child_obj, $org_arr){
		$org = new \App\Models\Org($market_arr['org']);
        $market->org()->save($org);


	}

}



