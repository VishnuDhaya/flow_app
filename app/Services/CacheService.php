<?php

namespace App\Services;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\MasterDataKeyRepositorySQL;
use App\Repositories\SQL\MasterDataRepositorySQL;
use Illuminate\Support\Facades\Cache;

class CacheService{
	 static $cache_grp = ['master_data'];

	public static function reload(){
		self::flush();
		self::load();
	}

	public static function load(){
		
        $common_repo = new CommonRepositorySQL();

        
        $country_list = $market_repo->get_country_list();
        $country_list = ["country_code" => 'UGA'];
        
        foreach ($country_list as $country->country_code) {
        	self::load_market($country_code);
        }
       
     } 

     public static function load_market($country_code){ 
        foreach (self::$cache_grp as $cache_grp_item) {
            self::load_cache_grp($country_code, $cache_grp_item);
        }   
    }   

	public static function load_cache_grp($country_code, $cache_grp_item){ 
		$data_list = self::get($country_code, $cache_grp_item);
        foreach ($data_list as $cache_key => $cache_data) {
            Cache::forever("{$country_code}_{$cache_key}", $cache_data);
        }
		
	}	

	

    public static function get($country_code, $cache_grp_item){ 
     	if($key == "master_data"){
           return self::get_master_data_list($country_code);
     		/*$common_repo = new CommonRepositorySQL();
            $data['country_code'] = $country_code;
	        return  $common_repo->get_master_data($data);*/
      	}else if (false){

      	}
    }

      	
    public static function flush(){ 
    	Cache::flush();
     }  
	public static function flush_market($country_code){ 
		foreach (self::$cache_grp as $cache_grp_item) {
        	self::flush_cache_grp($country_code, $cache_grp_item);
     	}
    }

    public static function flush_cache_grp($country_code, $cache_grp_item){ 
		$data_key_list = self::get_master_data_key_list($country_code);
        foreach ($data_key_list as $cache_key) {
            Cache::forget("{$country_code}_{$cache_key}");
        }
    }

    private static function get_master_data_list($country_code){
         
            $common_repo = new CommonRepositorySQL();
            $data_list = [];
            $data_key_list = self::get_master_data_key_list($country_code);
            foreach ($data_key_list as $data_key => $data_key_item) {
                $key = $data_key_item->data_key;
                $data_list[$key] = $common_repo->get_master_data(["country_code" => $country_code, "data_key" => $key]);
            }
            return $data_list;
    }

     private static function get_master_data_key_list($country_code){
        $mdk_repo = new MasterDataKeyRepositorySQL();
        return  $mdk_repo->list($country_code);
     }

}