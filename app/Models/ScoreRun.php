<?php

namespace App\Models;
use App\Models\Model;
use Log;

class ScoreRun extends Model
{
	
	const INSERTABLE = ["country_code", "acc_number", "run_id", "score", "result_code", "cust_txn_file_name"];
	const TABLE = "score_runs";

}