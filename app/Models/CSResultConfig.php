<?php

namespace App\Models;
use App\Models\Model;
use Log;

class CSResultConfig extends Model
{
	
	const INSERTABLE = ["country_code","csf_model","score_result_code","score_from","score_to"];
	const TABLE = "cs_result_config";

	public function model(){
        return CSResultConfig::class;
    }


}