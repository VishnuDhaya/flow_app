<?php

namespace App\Models;
use App\Models\Model;
use Log;

class ScoreModel extends Model
{
	
	const INSERTABLE = ["country_code","model_name","model_code"];
	const TABLE = "score_models";

	public function model(){
        return ScoreModel::class;
    }


}