<?php

namespace App\Scripts\php;
use DB;
Use \Carbon\Carbon;
use Log;
use App\Repositories\SQL\BorrowerRepositorySQL;



class UpdateLastVisitDateScript{

	public function UpdateLastVisitDate(){
		session()->put('country_code','UGA');
		$brwr_repo = new BorrowerRepositorySQL;
		$today_date = date('Y-m-d');
		$visits = DB::select("select visit_end_time,cust_id from field_visits where visit_end_time like('{$today_date}%')");

		foreach($visits as $visit){
			$brwr_repo->update_model_by_code(["cust_id" => $visit->cust_id,"last_visit_date" => $visit->visit_end_time]);

		}

	}

}