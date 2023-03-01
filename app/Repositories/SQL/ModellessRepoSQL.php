<?php
namespace App\Repositories\SQL;


class ModellessRepoSQL {

	 public function __construct()
    {
    	
    	$this->country_code = session('country_code');
		$this->add_country_code(true);    	
    	$this->api_req_id = session('api_req_id_n_path');

    	//Log::warning("BaseRepositorySQL");
    	//Log::warning($this->api_req_id);
    	//Log::warning($this->country_code);

    	if(!$this->country_code){
    		thrw("ERROR: COUNTRY CODE IS NULL");
    	}
    }

	
}