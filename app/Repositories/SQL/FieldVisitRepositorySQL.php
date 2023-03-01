<?php
 
namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Models\FieldVisit;
use App\Consts;
use Exception;
use Carbon\Carbon;

class FieldVisitRepositorySQL extends BaseRepositorySQL{
	public function __construct()
    {
      parent::__construct();
      $this->class = FieldVisit::class;
    }
	
	public function model(){
			
			return $this->class;
	}

	
	public function list(array $data){
	    throw new BadMethodCallException();
		
	}

	
	public function delete($id){
		throw new BadMethodCallException();
	}

	
} 

 