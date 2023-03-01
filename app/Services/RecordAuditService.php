<?php
namespace App\Services;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\RecordAuditRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;



class RecordAuditService{

	  public function __construct()
    {
        $this->country_code = session('country_code');
        // session()->put('data_prvdr_code', "UEZM");
        // session()->put('country_code', "UGA");
    }


	public function audit_borrower_status_change($data)  {
 
    $current_status= $data['borrowers']['status'];
    $reason = $data['borrowers']['status_reason'];
    $table_name = key($data);
    
      $data['record_id'] = $data['borrowers']['id'];
      $data['record_type'] = $table_name;
      $data['record_code'] = $data['borrowers']['cust_id'];
      $data['audit_type'] = "status_change";
      $data['country_code'] = $this->country_code;
      $data['remarks'] = $data['borrowers']['remarks'];

      $data_after = array('status' => $current_status, 
                          'reason' => $reason);
   
     	$data['data_after'] = json_encode($data_after);

     if($current_status == "enabled"){

     	$data_before = array('status' => "disabled" );
     	$data['data_before'] = json_encode($data_before);

     }else{
     	$data_before = array('status' => "enabled" );
     	$data['data_before'] = json_encode($data_before);
       
     }

     $record_audit_repo = new RecordAuditRepositorySQL();
     $record_audit_repo->insert_model($data); 
     
  }
}