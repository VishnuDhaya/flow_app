<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use DB;
class UpdatePerson 
{
 	public function read_json(){
		$jsondata = file_get_contents('store_json.json');
		$json = json_decode($jsondata,true);
		if(array_key_exists('borrower', $json))
		{
			$borrower_val = $json['borrower'];
			$dp_cust_id = $borrower_val['data_prvdr_cust_id'];
			$owner_array = $borrower_val['owner_person'];	
		}else{

		}

    	$person_repo = new PersonRepositorySQL();
       	$person_id = $person_repo->create($owner_array);
       	
       	DB::update("update borrowers set  owner_person_id = {$person_id} where data_prvdr_cust_id = '{$dp_cust_id}'");
       	/*$borrower_repo = new BorrowerRepositorySQL();

       	$arr = ['owner_person_id' => $person_id, 'data_prvdr_cust_id', $dp_cust_id];
       	$borrower_repo->update_model($arr, 'data_prvdr_cust_id');*/
    }
}


namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use DB;
class UpdatePerson 
{
 	public function read_json(){
		$jsondata = file_get_contents('785193988.json');
		$json = json_decode($jsondata,true);
		if(array_key_exists('borrower', $json))
		{
			$borrower_val = $json['borrower'];
			$dp_cust_id = $borrower_val['data_prvdr_cust_id'];
			$owner_array = $borrower_val['owner_person'];	
		}else{

		}

    	$person_repo = new PersonRepositorySQL();
       	$person_id = $person_repo->create($owner_array);
       	
       	DB::update("update borrowers set prob_fas = 5, owner_person_id = {$person_id} where data_prvdr_cust_id = '{$dp_cust_id}'");
       	/*$borrower_repo = new BorrowerRepositorySQL();

       	$arr = [ 'owner_person_id' => $person_id, 'data_prvdr_cust_id', $dp_cust_id];
       	$borrower_repo->update_model($arr, 'data_prvdr_cust_id');*/
    }
}
