<?php

namespace App\Models;
use App\Models\Model;

class ApiResponse extends Model
{
	const TOUCH = false;
	const INSERTABLE = ["api_req_id","req_user_id","response_code","response_msg","response_time","response_status","response_json","ms"];
	const TABLE = "api_response";
} 