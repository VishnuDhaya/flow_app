<?php

namespace App\Models;
use App\Models\Model;

class Task extends Model
{
    const JSON_FIELDS = ['device_info_json'];
    const UPDATABLE = ['approval_json', 'status'];
    const INSERTABLE = ['remarks', 'approval_json', 'task_json', 'device_info_json', 'status', 'loan_doc_id', 'cust_id', 'lead_id', 'task_type', 'country_code'];
    const TABLE = "tasks";

    	public function model(){
    	    return Task::class;
	}
}
