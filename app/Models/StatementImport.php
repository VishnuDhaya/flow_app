<?php

namespace App\Models;
use App\Models\Model;

class StatementImport extends Model
{

    const UPDATABLE = ['end_time', 'status', 'exception'];
    const INSERTABLE = ['account_id', 'country_code', 'start_time', 'status', 'acc_prvdr_code'];
    const TABLE = "float_acc_stmt_imports";

    	public function model(){
    	    return StatementImport::class;
	}
}
