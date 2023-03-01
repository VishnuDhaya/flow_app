<?php
 
 namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use App\Repositories\SQL\LoanEventRepositorySQL;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\LoanEvent;
use App\Consts;
use Log;
class LoanEventRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{
	
	public function __construct()
    {
      parent::__construct();

    }
    
	public function model(){
			return LoanEvent::class;
	}
	public function create_event($loan_doc_id, $event_type,$event_data = null, $loan_txn_id = null,$txn_date = null){
		Log::warning($txn_date);
		if ($txn_date == null){
			$txn_date = datetime_db();
		}

		$loan_event['loan_doc_id']= $loan_doc_id;
		$loan_event['event_type']= $event_type;
		$loan_event['created_at']= $txn_date; 
		$loan_event['event_data']= $event_data;  
		#$loan_event['loan_txn_id']= $loan_txn_id;  
        $loan_event_id = parent::insert_model($loan_event); 
        return $loan_event_id;
	}
 	
 	public function create($data){
	    throw new BadMethodCallException();	
	}

 	

 	public function update(array $req){
 	    throw new BadMethodCallException();	
	}
	public function delete($id){
		throw new BadMethodCallException();	
	}

	public function list($id){
	throw new BadMethodCallException();	
	}

}

 