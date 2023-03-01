<?php


namespace App\Repositories\SQL;
//namespace App\Repositories;

use Illuminate\Support\Facades\DB;
//use App\Repositories\SQL\MasterDataKeysRepositorySQL;
use App\Consts;
use App\Models\MasterData;
use Log;

class MasterDataRepositorySQL extends BaseRepositorySQL{

	public function __construct()
  	{
      parent::__construct();

    }
    
	public function model(){
			return MasterData::class;
	}

	public function create(array $master_data){
		//$master_data['created_by'] = 1;
		//$master_data['status'] = Consts::ENABLED;
		return parent::insert_model($master_data);
	}


	public function get_cs_model_code(array $data){
		
		return parent::get_records_by('data_key', $data['data_key'], ['data_code']);
	}

	public function get_score_factors(array $data){
		
		return parent::get_records_by('data_key', $data['data_key'], ['data_code','data_value','parent_data_code']);
	}

	public function get_master_data_version()
	{
		$max_date_obj = DB::table('master_data')
		                    ->select(DB::raw('MAX(created_at) as created_at,MAX(updated_at) as updated_at'))
		                    ->where('data_type','common')
		                    ->whereIn('country_code',[$this->country_code, "*"])
		                    ->first();
		$max_date = null;                    
		if($max_date_obj->created_at > $max_date_obj->updated_at)
		{
			$max_date = $max_date_obj->created_at;
		}	
		else
		{
			$max_date =  $max_date_obj->updated_at;
		}	            
	    return date("Ymdhis",strtotime($max_date));
	}                   	                       
	

    /* public function get_master_data($data_code)
    {
        	$master_data_value = DB::table('master_data')
		                    ->select('data_value')
		                    ->where('data_code',$data_code)
		                    ->whereIn('country_code',[$this->country_code, "*"])
		                    ->first();
		     return $master_data_value;               
    } */  

    public function get_filtered_csf_types($factor_arr, $country_code){
    		return  DB::table('master_data')
    					->select('data_value as name', 'data_code as id')   					
    					->where('data_code','not like','meta_%')
    					->where('data_key','csf_type')
    					->where('country_code', $country_code)
    					->whereNotIn('data_code',$factor_arr)
    					->get();

    }

	
}