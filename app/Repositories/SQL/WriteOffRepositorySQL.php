<?php
namespace App\Repositories\SQL;

use App\Models\LoanWriteOff;

class WriteOffRepositorySQL  extends BaseRepositorySQL
{

	public function __construct(){
            parent::__construct();
            $this->class = LoanWriteOff::class;

    }
        
    public function model(){        
        return $this->class;
    }
}