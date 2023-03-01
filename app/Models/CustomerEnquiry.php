<?php

namespace App\Models;
use App\Models\Model;

class CustomerEnquiry extends Model
{
	
	const INSERTABLE = ["market_code","data_provider_code","mob_num","acc_number","message"];
	const TABLE = "customer_enquiry";
} 