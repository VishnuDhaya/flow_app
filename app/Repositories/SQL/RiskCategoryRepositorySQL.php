<?php
 
namespace App\Repositories\SQL;
use App\Models\RiskCategory;

class RiskCategoryRepositorySQL extends BaseRepositorySQL{

    public function __construct()
    {
      parent::__construct();
      $this->class = RiskCategory::class;
    }
	
	public function model(){
			
		return $this->class;
	}

}