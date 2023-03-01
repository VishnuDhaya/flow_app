<?php

namespace App\Traits;
use Illuminate\Support\Str;

trait ClassDetector
{
	public function getClassName(){
		return __CLASS__;
	}	

	public function model(){
		return Str::plural(Str::snake(__CLASS__));
	}	

}