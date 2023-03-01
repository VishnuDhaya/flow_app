<?php
 
namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Models\InboundVoiceCallLogs;
use App\Consts;
use Exception;
use Carbon\Carbon;

class InboundVoiceCallLogsRepositorySQL extends BaseRepositorySQL{

    public function __construct()
    {
      parent::__construct();
      $this->class = InboundVoiceCallLogs::class;
    }
	
	public function model(){
			
			return $this->class;
	}
}