<?php
 
namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Models\CapitalFund;
use App\Consts;
use Exception;
use Carbon\Carbon;

class CapitalFundRepositorySQL extends BaseRepositorySQL{
	public function __construct()
    {
      parent::__construct();
      $this->class = CapitalFund::class;
    }
	
	public function model(){
			
			return $this->class;
	}

	
	public function list(array $data){
	    throw new BadMethodCallException();
		
	}

	
	public function delete($id){
		throw new BadMethodCallException();
	}

	public function get_default_fund($lender_code){
		$fund = $this->get_record_by_many(['lender_code', 'is_lender_default'], [$lender_code, true], ['fund_code']);
		if($fund){
			return $fund->fund_code;
		}else{
			thrw("No default fund configured for {$lender_code}");
		}
		
	}
	public function get_fund_details($fund_code){
	
		$fund = $this->get_record_by('fund_code', $fund_code, ['status', 'total_alloc_cust','current_alloc_cust','alloc_amount', 'os_amount']);
		
		if($fund == null || $fund->status != 'active'){
			#TODO Use the lender default fund if the customer is not allocated with a fund
			thrw("Fund {$fund_code} not available/enabled under lender ");
			
		}
		return $fund;
	}
	
	
} 