<?php

namespace App\Models;
use App\Models\Model;

class ApiRequest extends Model
{
	const TOUCH = false;
	const INSERTABLE = ["country_code","h_url","h_page","h_user_agent","request_json","request_time"];
	const TABLE = "api_request";
}