<?php
 
namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Models\CsDevices;
use App\Consts;
use Exception;
use Carbon\Carbon;

class CsDevicesRepositorySQL extends BaseRepositorySQL{

    public function __construct()
    {
      parent::__construct();
      $this->class = CsDevices::class;
    }
	
	public function model(){
			
			return $this->class;
	}
}