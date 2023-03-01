<?php

namespace App;
use App\Consts;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use DB;
use Log;
class UpdateLoans 
{
 	public function make_holiday($dates, $country_code, $data_prvdr_code, $real_run = false){
		$date_conds = array();
		foreach($dates as $date){
			if(!in_array($date, Consts::HOLIDAYS[$country_code])){
				thrw("$date not in holiday list");
			}
			$date_conds[] =  "'$date' BETWEEN  disbursal_date and due_date";
		}
		$date_conds = implode(" or ", $date_conds);
		
		 $sql = "select loan_doc_id, status, disbursal_date, date(due_date) as due_date,
		 date(paid_date) as paid_date, status, duration from loans
		 where ($date_conds) 
			and ((status = 'settled' and date(paid_date) > date(due_date)) or   status !='settled' )
			and country_code = '{$country_code}' ";
		if($data_prvdr_code){
			$sql .= " and data_prvdr_code = '{$data_prvdr_code}'";
		}
	                
       $loans = DB::select($sql);
		Log::warning("Total Loans :". sizeof($loans));
	
		foreach($loans as $loan){
				Log::warning('here');
			$due_date = getDueDate($loan->duration, $loan->disbursal_date, Consts::DB_DATETIME_FORMAT);
			
			$status = $loan->status;
			Log::warning($status);
			if($status!= 'settled'){
				if(date_db() > $due_date){
					$status = 'overdue';
				}else if(date_db() == $due_date){
					$status = 'due';
				}else{
					$status = 'ongoing';
				}
			}
			
			Log::warning($status);
			Log::warning("\nFA {$loan->loan_doc_id} | Old due date {$loan->due_date} | Old  status {$loan->status}");
			Log::warning("FA {$loan->loan_doc_id} | New due date {$due_date} | New status {$status}");
			
			if($real_run){
				DB::update("update loans set status = ?, due_date = ? where loan_doc_id = ?" , 
								[$status, $due_date , $loan->loan_doc_id]);
			}
		
    	}
	}
}

