<?php

namespace App\Scripts;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\BorrowerService;
use App\Repositories\SQL\RelationshipManagerRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use Illuminate\Support\Facades\DB;
use App\Exceptions\SkipRecordException;
use Log;

class UgaEzmDataImport{
	public function __invoke($file_name, $action = "validate"){
		//$file_name = "/home/sateesh/Documents/Data Import sheet_Tech1.xlsx";

		//$action = "import"; //"validate";

		$import = new BorrowerImport($action);
		//$import->onlySheets('Sheet3');
		if($action == 'validate'){
				print_r("Beginning Txns for validation");
				DB::beginTransaction();	
		}
		elseif($action == 'import'){
				print_r("Beginning Txns for IMPORT");
				DB::beginTransaction();	
		}

		try{
			Excel::import($import, $file_name);

			if($action == 'validate'){
					print_r("Rolling back Txns for validation");
					DB::rollback();	
			}elseif($action == 'import'){ 
					print_r("Committing Txns for IMPORT");
					DB::commit();	
			}		
		}catch (\Exception $e) {
			print_r("Catching Exception");
			$this->result[] = $e->getMessage();
			print_r($e->getTraceAsString());
			print_r("Rolling back Txns for validation");
			DB::rollback();	
			
		}

		print_r("----------------------------");
		print_r($file_name);
		print_r("----------------------------\n");
		print_r($import->getResult());
		print_r("--------------------------- END OF FILE -----------------------------\n");

	
	}
}

class BorrowerImport implements OnEachRow, WithHeadingRow
{
	  public function __construct($action = "validate"){
	  	$this->action = $action;
        $this->result = [];
        $this->cust_ids = [];
    }

    public function getResult(){
    	
    	return array_values($this->result);
    }
	public function onRow(Row $row){
		
		$this->index = $row->getIndex();
		$row = $row->toArray();
		Log::warning("");
		Log::warning("---------------NEW ROW[$this->index] -------------");
		/*try{*/
			$this->borrower_service = new BorrowerService();
			
			$biz_address = extract_array($row, ['region', 'district', 'county', 'sub_county', 'parish', 'village', 'landmark']);
			$biz_address['country_code'] = 'UGA';
			$owner_person = extract_array($row, ['first_name', 'middle_name', 'last_name', 'mobile_num', 'national_id']);

			$this->check_names($owner_person);
			$borrower = extract_array($row, ['data_prvdr_cust_id', 'biz_name']);
			
			$borrower['biz_address'] = $biz_address;
			$borrower['owner_person'] = $owner_person;


			$check_result = $this->borrower_service->check_duplicate_borrower($borrower,  $this->cust_ids);

			if($check_result["is_duplicate"]){
				$this->result[] = "ROW[$this->index] : 	{$check_result['message']}";
			}

			/*if($this->action == 'import'){
				DB::beginTransaction();	
			}*/
			$this->fill_borrower($borrower, $row);
			$cust_id = $this->borrower_service->create($borrower, false, true);
			
			$this->cust_ids[$cust_id] =  $this->index;
			if(!$check_result["is_duplicate"]){
				if($this->action == 'validate'){
					$this->result[] = "ROW[$this->index] : VALIDATED FOR IMPORT";
					//DB::rollback();
				}else if($this->action == 'import'){
					$this->result[] = "ROW[$this->index] : QUEUED FOR IMPORTED - New Cust ID {$cust_id}";
					//DB::commit();
				}else{
					thrw("Unknown Action : {$this->action}");
				}
			}
		/*}	
		catch (SkipRecordException $e) {
				
				$this->result[] = $e->getMessage();
				if($this->action == 'import'){
					throw new \Exception("IMPORT HALTED DUE TO ERROR:\n {$e->getMessage()}");
					
				}
		}catch (\Exception $e) {
				print_r("Catching Exception");
				$this->result[] = $e->getMessage();
				if($this->action == 'import'){
					throw new \Exception("IMPORT HALTED DUE TO ERROR:\n {$e->getMessage()}");
				}
		}*/
	}

	private function check_names(&$owner_person){
		if($owner_person['first_name'] == '' && $owner_person['middle_name'] == '' 
				&&  $owner_person['last_name'] == ''){
			$owner_person['first_name'] = 'UNKNOWN';
			$owner_person['last_name'] = 'UNKNOWN';
			$owner_person['middle_name'] = 'UNKNOWN';
			$this->result[] = "ROW[$this->index] : CUSTOMER NAME NOT AVAILABLE. ADDING 'UNKNOWN' AS NAMES";
		}
		

	}
	public function headingRow() : int{
		return 1;
	}

	private function fill_borrower(&$borrower, $row){
		$borrower['data_prvdr_code'] = 'UEZM'; // TO FILL
		$borrower['lender_code'] = 'UFLW'; // TO FILL
		$borrower['country_code'] = 'UGA'; // TO FILL
		$borrower['biz_type'] = 'Individual'; // TO FILL
		
		$borrower['dp_rel_mgr_id'] = $this->get_dp_rel_mgr_id($row, $borrower['data_prvdr_code'], $borrower['country_code']);
		$borrower['flow_rel_mgr_id'] = $this->get_flow_rel_mgr_id($row);

		$borrower['account'] = $this->get_account($row, $borrower);

	}

	 private function get_account($row, $borrower){
	 	$account = array();
	 	$account['acc_number'] = $row['acc_number'];
	 	$account['country_code'] = $borrower['country_code'];
	 	$account['acc_prvdr_name'] = 'EzeeMoney'; // TO FILL 
	 	$account['acc_prvdr_code'] = 'UEZM'; // TO FILL
	 	$account['type'] = 'Wallet'; // TO FILL
	 	$person = (object) $row;
	 	$account['holder_name'] = full_name($person);
	 	$account['is_primary_acc'] = true;

	 	return $account;

	 }

	private  function get_dp_rel_mgr_id($row, $data_prvdr_code, $country_code){
		$rel_mgr_repo = new RelationshipManagerRepositorySQL();
		$person = array();
		$person['mobile_num'] = $row['dp_rel_mgr_mobile_num'];
		$person['associated_with'] = 'data_prvdr';
		$person['associated_entity_code'] = $data_prvdr_code ;
		$person['country_code'] = $country_code;
		
		if($person['mobile_num']){
			$dp_rel_mgrs = $rel_mgr_repo->get_records_by_many(array_keys($person), array_values($person));

			if(sizeof($dp_rel_mgrs) > 0){
				return  $dp_rel_mgrs[0]->id;
			}
		}
		elseif($row['dp_rel_mgr_name'] && $person['mobile_num']) {
			$person_name = $this->get_person_name($row['dp_rel_mgr_name']);
			$person = array_merge($person_name, $person);
			return $rel_mgr_repo->create($person);
		//	$this->result[] = "ROW[$index] : ADDED - new Rel. Mgr {$row['dp_rel_mgr_name']} for {$borrower['data_prvdr_code'] }";
		}else{
			$person['mobile_num'] = '256755212080'; // TO FILL
			$dp_rel_mgrs = $rel_mgr_repo->get_records_by_many(array_keys($person), array_values($person));
			if(sizeof($dp_rel_mgrs) > 0){
				return  $dp_rel_mgrs[0]->id;
			}else{
					$this->result[] = "ROW[$this->index] : SKIPPED - dp_rel_mgr_mobile_num is empty";
					skip_record("ROW[$this->index] : SKIPPED - dp_rel_mgr_mobile_num is empty");
			}
			
		}
	}


	private  function get_flow_rel_mgr_id($row){
		$person = array();
		$rel_mgr_repo = new RelationshipManagerRepositorySQL();
		$person['mobile_num'] = $row['flow_rel_mgr_mobile_num'];
		$person['associated_with'] = 'flow';
		if(!$person['mobile_num']){
			skip_record("ROW[$this->index] : SKIPPED - flow_rel_mgr_mobile_num is empty");
		}
		$flow_rel_mgrs = $rel_mgr_repo->get_records_by_many(array_keys($person), array_values($person));
		
		if(sizeof($flow_rel_mgrs) > 0){
			  return $flow_rel_mgrs[0]->id;
		}
		else{
			$person_name = $this->get_person_name($row['flow_rel_mgr_name']);
			$person = array_merge($person_name, $person);
			return $rel_mgr_repo->create($person);
			$this->result[] = "ROW[$index] : ADDED - new Rel. Mgr {$row['flow_rel_mgr_name']} for FLOW}";
		}
		
	}	

	private function get_person_name($rel_mgr_name){
		$rel_mgr_name = explode(' ', $rel_mgr_name);
			$person = array();
			if(sizeof($rel_mgr_name) == 1){
				$person['first_name'] = $rel_mgr_name[0];
			}elseif (sizeof($rel_mgr_name) == 2){
				$person['first_name'] = $rel_mgr_name[0];
				$person['last_name'] = $rel_mgr_name[1];
			}elseif (sizeof($rel_mgr_name) == 3){
				$person['first_name'] = $rel_mgr_name[0];
				$person['middle_name'] = $rel_mgr_name[1];
				$person['last_name'] = $rel_mgr_name[2];
			}
			return $person;
	}
}