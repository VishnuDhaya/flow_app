<?php
namespace App\Repositories\SQL;
//namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Consts;
use App\Models\ScoreModal;
use App\Exceptions\FlowCustomException;
use Exception;
use Log;

class CreditScoreRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{

	public function __construct()
    {
      parent::__construct();

    }
    
	public function model(){
			return ScoreModal::class;
	}

   public function get_credit_score($country_code)
   {
     return parent::get_records_by('country_code',$country_code,['model_name','model_code']);
   
   }
    public function update(array $org){
    return parent::update_model($org);
  }

  public function create(array $org){
    return parent::update_model($org);
  }
  public function delete($id){
    throw new BadMethodCallException();
  }
  public function list($country_code){
    return parent::get_records_by_country_code();
  }
}


