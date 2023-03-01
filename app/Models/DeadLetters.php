<?php

namespace App\Models;
use App\Models\Model;

class DeadLetters extends Model
{
	
	const INSERTABLE = ["country_code","acc_pvdr_code","notify_json"];
	const TABLE = "dead_letters";
} 