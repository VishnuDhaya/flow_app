<?php
namespace App\Services;
use App\Models\Person;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\AgreementRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;

use App\Services\FileService;
use Carbon\Carbon;
//use App\Exceptions\FlowCustomException;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Consts;
use App\Repositories\SQL\LeadRepositorySQL;
use Aws\Waiter;
use PDF;
use File;
use Log;
use Illuminate\Database\QueryException;
class AgreementService{
  public function __construct()
  {
        $this->country_code = session('country_code');
  }

	public function inactivate_agreement($cust_id){
    $cust_aggr_repo = new CustAgreementRepositorySQL();
        $active_aggr = $cust_aggr_repo->get_record_by_many(['cust_id','status'], [$cust_id,'active'], ['id', 'aggr_doc_id']);

        
        if($active_aggr){
            $cust_aggr_repo->update_record_status(Consts::AGGR_INACTIVE, $active_aggr->id);
        }
  }
  private function update_brwr_aggr_to_lead($lead_id, $aggr,$witness_data =null){
    $lead_repo = new LeadRepositorySQL();
    $cust_reg_arr = $lead_repo->get_cust_reg_arr($lead_id);
    $cust_reg_arr['biz_info']['current_aggr_doc_id']['value'] = $aggr['aggr_doc_id'];
    $cust_reg_arr['biz_info']['aggr_valid_upto']['value'] = $aggr['valid_upto'];
    $cust_reg_arr['cust_id'] = $aggr['cust_id'];
    foreach($cust_reg_arr['agreements'] as $key => $value){
      if($cust_reg_arr['agreements'][$key]['aggr_doc_id'] ==  $aggr['master_aggr_doc_id'] ){
        $cust_reg_arr['agreements'][$key]['aggr_file_path'] = $aggr['aggr_file_rel_path'];
        $cust_reg_arr['agreements'][$key]['status']= "signed";
        $cust_reg_arr['agreements'][$key]['master_aggr_doc_id'] = $aggr['master_aggr_doc_id'];
        $cust_reg_arr['agreements'][$key]['aggr_doc_id'] = $aggr['aggr_doc_id'];
        $cust_reg_arr['agreements'][$key]['aggr_duration'] = $aggr['aggr_duration'];
        if($witness_data){
          $cust_reg_arr['agreements'][$key]['photo_witness_national_id'] = $witness_data['photo_witness_national_id'];
          $cust_reg_arr['agreements'][$key]['photo_witness_national_id_back'] = $witness_data['photo_witness_national_id_back'];
        }
       
        break;
      }    
    }

    $lead_repo = new LeadRepositorySQL;
    $lead_repo->update_cust_reg_json($cust_reg_arr,$lead_id);
  
  }

  private function get_borrower_from_lead($cust_reg_arr){
    $acc_prvdr_repo = new AccProviderRepositorySQL();
    $person_repo = new PersonRepositorySQL;
    $accont = $acc_prvdr_repo->get_acc_prvdr_name($cust_reg_arr['account']['acc_prvdr_code']);
    $flow_rel_mgr = $person_repo->find(session('user_person_id'), ["first_name", "middle_name", "last_name","mobile_num","id"]); 
    $borrower['biz_name'] =  $cust_reg_arr['biz_info']['biz_name'];
    $borrower['cust_name'] =  full_name((object)$cust_reg_arr['owner_person']);
    $borrower['cust_id'] =  $cust_reg_arr['cust_id'];
    $borrower['cust_mobile_num'] =  $cust_reg_arr['biz_identity']['mobile_num'];
    $borrower['cust_addr_text'] =  short_addr($cust_reg_arr['biz_address']);
    $borrower['acc_prvdr_name'] =  $accont->name;
    $borrower['acc_number'] =  $cust_reg_arr['account']['acc_number'];
    $borrower['acc_prvdr_code'] =  $cust_reg_arr['account']['acc_prvdr_code'];
    $borrower['flow_rel_mgr_name'] =  full_name($flow_rel_mgr);
    $borrower['flow_rel_mgr_mobile_num'] =  $flow_rel_mgr->mobile_num;
    $borrower['national_id'] =  $cust_reg_arr['owner_person']['national_id'];
    return $borrower;
  }
	public function save_agreement(array $data, $cust_reg_arr = null){
       
        $aggr = $this->process_common_agreement($data);  
          
        $data['aggr_doc_id'] = $aggr['aggr_doc_id'];
        
        $file_serv = new FileService($this->country_code);
        $data['rm_sign_req']['aggr_doc_id'] = $data['aggr_doc_id'];
        $data['cust_sign_req']['aggr_doc_id'] = $data['aggr_doc_id'];
        $data['witness_sign_req']['aggr_doc_id'] = $data['aggr_doc_id'];
        
        $rm_sign_file_det = $file_serv->create_file_from_data_url($data['rm_sign_req']);
        $cust_sign_file_det = $file_serv->create_file_from_data_url($data['cust_sign_req']);
        $witness_sign_file_det = $file_serv->create_file_from_data_url($data['witness_sign_req']);
        
        //$data['sign_file_path'] = flow_storage_path($sign_file_det['file_rel_path'].DIRECTORY_SEPARATOR.$sign_file_det['file_name']);
        $data['cust_sign_file_path'] = flow_storage_path($cust_sign_file_det['file_rel_path'].DIRECTORY_SEPARATOR.$cust_sign_file_det['cust_file_name']);
        $data['rm_sign_file_path'] = flow_storage_path($rm_sign_file_det['file_rel_path'].DIRECTORY_SEPARATOR.$rm_sign_file_det['rm_file_name']);
        $data['witness_sign_file_path'] = flow_storage_path($witness_sign_file_det['file_rel_path'].DIRECTORY_SEPARATOR.$witness_sign_file_det['witness_file_name']);
        $borr_serv = new BorrowerService($this->country_code);
        if($cust_reg_arr){           
          $data['borrower'] =$this->get_borrower_from_lead($cust_reg_arr);
          
        }else{
          $data['borrower'] = $borr_serv->get_borrower($data['cust_id'],false); 

        }

        $this->create_digitally_signed_pdf($data);
        $aggr_file_rel_path = $borr_serv->get_aggr_file_rel_path($data['cust_id'], $data['aggr_doc_id']);
        $aggr['aggr_file_rel_path'] = $aggr_file_rel_path;
        $aggr['acc_purpose'] = array_key_exists('acc_purpose', $data) ? $data['acc_purpose'] : 'float_advance';
        if($cust_reg_arr){
          $witness_data = [];
          if(array_key_exists('photo_witness_national_id',$data) && array_key_exists('photo_witness_national_id_back',$data) ){
            $witness_data['photo_witness_national_id'] = $data['photo_witness_national_id'];
            $witness_data['photo_witness_national_id_back'] = $data['photo_witness_national_id_back'];
          }
          $this->update_brwr_aggr_to_lead($data['lead_id'], $aggr,$witness_data);
              
        }else{
          $brwr_repo = new BorrowerRepositorySQL();
          $brwr_repo->update_brwr_aggr($aggr['cust_id'],$data['aggr_doc_id'], $aggr['valid_upto']);

        }
        
        
        return $aggr_file_rel_path ;

      
  }


  private function create_digitally_signed_pdf($data){  
    $borrower_repo = new BorrowerRepositorySQL();     
    $acc_prvdr_repo = new AccProviderRepositorySQL(); 
    $lead_repo = new LeadRepositorySQL();    
    $country_code = session('country_code');
    
    $pdf_rel_path = get_file_rel_path($data['cust_id'], "agreement", "pdf");
        
    //$pdf_rel_file_path = $pdf_rel_path.DIRECTORY_SEPARATOR.$data['aggr_doc_id']."pdf";
   
    
    //$split_cust_id = explode("-",$data['cust_id']);
    // $aggr_doc_id = $data['aggr_doc_id']."-".$split_cust_id[1];
    // $data['aggr_doc_id'] = $data['aggr_doc_id']."-".$split_cust_id[1].".pdf";
    
    $aggr_doc_id = $data['aggr_doc_id'];
    $data['aggr_doc_id'] = $data['aggr_doc_id'].".pdf";
  
    $pdf_abs_path =  flow_file_path().$pdf_rel_path;
    $pdf_rel_file_path = separate([$pdf_rel_path, $data['aggr_doc_id']]);

    $pdf_abs_file_path = flow_file_path().$pdf_rel_file_path;

    if(!File::exists($pdf_abs_path)){
      create_dir($pdf_abs_path);

    }

    # update status of borrower column rm_feedback_due
    $record =  $borrower_repo->get_records_by('cust_id', $data['cust_id'], ['id']);
    if(!empty($record)){
      $borrower_repo->update_model_by_code(['rm_feedback_due' => true, 'cust_id' => $data['cust_id']]);
    }

    $cust_aggr_repo = new CustAgreementRepositorySQL();
    $valid_from = $cust_aggr_repo->get_record_by('aggr_doc_id',$aggr_doc_id,['valid_from']);
    $view_data = [];
    // $acc_purpose = array_key_exists('acc_purpose', $data) ? $data['acc_purpose'] : 'float_advance';
    $acc_purpose = 'float_advance';
    $agrmt_file = "agreements.{$country_code}.{$acc_purpose}";
    if($acc_purpose == "terminal_financing"){
      $lead_data = $lead_repo->find($data['lead_id'],['product_json','product']);
      $loan_repo = new LoanProductRepositorySQL();
      $product_json = $loan_repo->get_tf_product($lead_data->product);
      $currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;
      $data['borrower']['duration_in_months'] = $product_json['duration']." Months";
      $data['borrower']['flow_fee'] = $product_json['flow_fee']." ".$currency_code;
      $data['borrower']['loan_amount'] = $product_json['amount'];
      $paper_size = 'legal';
    }else{
      $paper_size = 'A4';

    }
    $date_format = $country_code == 'RWA' ? 'd-m-Y' : 'd-M-Y';
    //$dp_logo = null;
     //if($data['aggr_type'] == "Draft"){
        //$dp_code = $borrower->data_prvdr_code;
        
        

     //}
    //$dp_logo = flow_storage_path(get_file_rel_path());
    $this->create_pdf($agrmt_file, 
                  ['borrower' => $data['borrower'],
                //'loan_products' => $aggr_details['loan_products'],
                  'valid_from' => date($date_format,strtotime($valid_from->valid_from)),
                 // 'valid_upto' => $valid_upto,
                  // 'dp_logo' => $dp_logo,
                  'cust_sign_file_path' => $data['cust_sign_req']['file_data'],
                  'rm_sign_file_path' => $data['rm_sign_req']['file_data'],
                  'witness_sign_file_path' => $data['witness_sign_req']['file_data'],
                  'aggr_file_name' => $aggr_doc_id,
                  'witness_name' => $data['witness_name'],
                  'witness_mobile_num' => $data['witness_mobile_num']
                  // 'cs_num' => config('app.customer_success')[$data['borrower']['acc_prvdr_code']]
                  ], $pdf_abs_file_path,$paper_size);
             
    return [$pdf_rel_file_path, $aggr_doc_id];
  }

  private function process_common_agreement($data){
    $cust_aggr_repo = new CustAgreementRepositorySQL();
    $aggr_repo = new AgreementRepositorySQL();
    $master_aggr_details = $aggr_repo->get_record_by("aggr_doc_id", $data['aggr_doc_id'], ['country_code','aggr_doc_id','aggr_duration','aggr_type', 'duration_type']);
    $master_aggr_arr = (array)$master_aggr_details;
    $master_aggr_arr['cust_id'] = $data['cust_id'];
    $master_aggr_arr['witness_name'] = $data['witness_name'];
    $master_aggr_arr['witness_mobile_num'] = $data['witness_mobile_num'];
    if(array_key_exists('photo_witness_national_id',$data) && array_key_exists('photo_witness_national_id_back',$data) ){
      $master_aggr_arr['photo_witness_national_id'] = $data['photo_witness_national_id'];
      $master_aggr_arr['photo_witness_national_id_back'] = $data['photo_witness_national_id_back'];
    }
    // $master_aggr_arr['acc_number'] = $data['account_num'];
    $master_aggr_arr['status'] = Consts::AGGR_ACTIVE;
    $master_aggr_arr['valid_from'] = carbon::now()->toDateTimeString();

    if($master_aggr_arr['duration_type'] == 'days'){
      $master_aggr_arr['valid_upto'] = carbon::now()->addDays($master_aggr_arr['aggr_duration'])->toDateTimeString();
    }
    else{
      $master_aggr_arr['valid_upto'] = null;
    }
    $split_cust_id = explode('-',$data['cust_id']);
    #$master_aggr_arr['aggr_doc_id'] = $data['aggr_doc_id']."-".$split_cust_id[1];
      $date = aggr_date();
      $master_aggr_arr['master_aggr_doc_id'] = $master_aggr_arr['aggr_doc_id'];
      // $acc_purpose = array_key_exists('acc_purpose', $data) ? $data['acc_purpose'] : 'float_advance';
      $acc_purpose = 'float_advance';
      if($acc_purpose == 'terminal_financing'){
          $master_aggr_arr['aggr_doc_id'] = "AGRT-{$split_cust_id[0]}-TF-$date-{$split_cust_id[1]}";
      }
      else{
          $master_aggr_arr['aggr_doc_id'] = "AGRT-{$split_cust_id[0]}-$date-{$split_cust_id[1]}";
      }
      $cust_aggr_repo->insert_model($master_aggr_arr);
      $master_aggr_arr['cust_id'] = $data['cust_id'];
      return $master_aggr_arr;
  } 

  private function process_cust_agreement($data){
    $cust_aggr_repo = new CustAgreementRepositorySQL();
    $aggr = $cust_aggr_repo->get_cust_aggr($data);
    if($aggr){
      if($aggr->status == 'draft'){
          $cust_aggr_repo->update_aggr_status([$aggr->id], Consts::AGGR_ACTIVE);
      }else{
       thrw("No draft agreement found for Cust ID {$data['cust_id']}.");
      }
    }else{
        thrw("Agreement {$data['aggr_doc_id']} is in {$aggr['status']} status. Can not save.");
    }
    return $aggr;    
  }


  private function create_signed_pdf($data){
    $split_cust_id = explode("-", $data['cust_id']);
    $pdf_rel_path = get_file_rel_path($data['cust_id'], "agreement", "pdf");
    $pdf_abs_path = flow_file_path().$pdf_rel_path;
    if($data['aggr_type'] == "Master"){
      $pdf_rel_file_path = separate([$pdf_rel_path, $data['aggr_doc_id']."-".$split_cust_id[1].".pdf"]);
    }else{
      $pdf_rel_file_path = separate([$pdf_rel_path, $data['aggr_doc_id'].".pdf"]);
    }
    
    $pdf_abs_file_path = flow_file_path().$pdf_rel_file_path;
    
    if(!File::exists($pdf_abs_path)){
      create_dir($pdf_abs_path);
    }

    
    if($data['sign_type'] == "upload" || $data['file_type'] == "image"){
      $img_file_path = get_file_rel_path("", "agreement", "");
      $img_abs_path = flow_file_path().$img_file_path;
      //$split_file_name = explode(".",$data['file_type']);
      //$file_name = $split_file_name[0];
  
     $img_file_path = flow_file_path().$img_file_path.DIRECTORY_SEPARATOR.$data['file_type'];
     //$tilt_img = $img_file_path->rotate(90);
      if(!File::exists($img_abs_path)){
        create_dir($img_abs_path);
      }
     
      $this->create_pdf('agreements.img_to_pdf', ['img_src' => $img_file_path], $pdf_abs_file_path);  
    }else{
      if(File::exists($pdf_abs_file_path)){
        return true;
      }else{
        thrw("Unable to create or find {$pdf_rel_file_path}");
      }
    }

    return $pdf_rel_file_path;
    
  }
 
  private function create_draft_pdf($data){
    
    $valid_from = $data['valid_from'];
    if(array_key_exists('valid_upto', $data)){
      $valid_upto = $data['valid_upto'];
    }else{
      $valid_upto = null;
    }
    $acc_prvdr_repo = new AccProviderRepositorySQL();
    $borrower_service= new BorrowerService($this->country_code);
    $borrower = $borrower_service->get_borrower($data['cust_id'],false);
   
    $aggr_details = $this->get_aggr_details($data['aggr_type'], $data['aggr_doc_id'], $this->country_code);
    
    $data['borrower'] = $borrower;  
    
    $pdf_rel_path = get_file_rel_path($data['cust_id'], "agreement", "unsigned_pdf");
        
    //$pdf_rel_file_path = $pdf_rel_path.DIRECTORY_SEPARATOR.$data['aggr_doc_id']."pdf";
    $pdf_abs_path =  flow_file_path().$pdf_rel_path;
    
    $pdf_rel_file_path = separate([$pdf_rel_path, $data['aggr_doc_id'].".pdf"]);
    $pdf_abs_file_path = flow_file_path().$pdf_rel_file_path;

    if(!File::exists($pdf_abs_path)){
      create_dir($pdf_abs_path);
    }
     //$dp_logo = null;
     //if($data['aggr_type'] == "Draft"){
        //$dp_code = $borrower->data_prvdr_code;
     //}

    $this->create_pdf('agreements.cust_agreement', ['borrower' => $data['borrower'],
                                                    'loan_products' => $data['loan_products'],
                                                    'aggr_doc_id' => $data['aggr_doc_id'],
                                                    // 'dp_logo' => $dp_logo,
                                                    'valid_from' => $valid_from,
                                                    'valid_upto' => $valid_upto], $pdf_abs_file_path);
   
    return separate(["files", $pdf_rel_file_path]);
  }

  private function create_master_agr_pdf($data){

    $acc_prvdr_repo = new AccProviderRepositorySQL();
    $valid_upto = null;
    $country_code = session('country_code');
    /*$contract_name = $result->contract_name;
    $dp_logo_name = $result->data_prvdr_logo;
    $data_prvdr_name = $result->name;*/
   
    $pdf_rel_path = get_file_rel_path(null,"master_agreements");
    $pdf_abs_path = flow_file_path().$pdf_rel_path;
    $pdf_rel_file_path = separate([$pdf_rel_path, $data['aggr_doc_id'].".pdf"]);
    $pdf_abs_file_path = flow_file_path().$pdf_rel_file_path;

    if(!File::exists($pdf_abs_path)){
      create_dir($pdf_abs_path);
    }

    $paper_size = $data['acc_purpose'] == 'terminal_financing' ? 'legal' : 'A4';
   
    $this->create_pdf("agreements.{$country_code}.{$data['acc_purpose']}", ['borrower' => null,
                    'aggr_file_name' => $data['aggr_doc_id']
                     ],$pdf_abs_file_path,$paper_size );
                    
    return $pdf_rel_file_path;
  }

  /*public function generate_new_cust_agreement($data){
    try
    {
        DB::beginTransaction();

          $cust_aggr_repo = new CustAgreementRepositorySQL();
          $aggr  = $cust_aggr_repo->create_agreement($data['cust_agreement']);
         
          //$aggr_details = $cust_aggr_repo->get_aggr_details($aggr_id);
          $aggr['aggr_type'] = "cust_agreement";
          //$aggr_serv = new AgreementService($data['country_code']);
          $aggr['aggr_file_rel_path'] = $this->create_draft_pdf($aggr);
         
        DB::commit();
        return $aggr;
        
    }catch (\Exception $e) {

        DB::rollback();  
        Log::warning($e->getTraceAsString());     
       if ($e instanceof QueryException){
          throw $e;
        }else{
        thrw($e->getMessage());
        }
    }
  }*/

  public function generate_new_master_agreement($data){

    try
    {
        DB::beginTransaction();
          $aggr_repo = new AgreementRepositorySQL();
          $aggr = $aggr_repo->create($data['master_agreement']);

          #$aggr['aggr_type'] = "common_aggr";
          //$aggr_serv = new AgreementService($data['country_code']);
          $aggr['aggr_file_rel_path'] = $this->create_master_agr_pdf($aggr);
        DB::commit();
        return $aggr;
        
    }catch (\Exception $e) {

        DB::rollback();      
        Log::warning($e->getTraceAsString()); 
        if ($e instanceof QueryException){
          throw $e;
        }else{
        thrw($e->getMessage());
        }
    }
  } 

  public function list_master_agreements($data){
      $aggr_repo = new AgreementRepositorySQL();
      $agreements = $aggr_repo->list_master_agreements($this->country_code, session('acc_prvdr_code'));

      foreach($agreements as $agreement){
        if($agreement->aggr_doc_id){
     
          $aggr_folder = separate([get_file_rel_path( null, "master_agreements"),$agreement->aggr_doc_id.".pdf"]);
      
          if(File::exists(flow_file_path().$aggr_folder)){
            $agreement->aggr_file_rel_path = separate(['files', $aggr_folder]); 
          }else{
            $agreement->aggr_file_rel_path = null;
          }
         
        }
      }

      return $agreements;
  }     
  
  private function create_pdf($view_name, $view_data, $pdf_abs_file_path, $paper_size = 'A4'){
    PDF::loadView($view_name, $view_data)->setPaper($paper_size,'portrait')->save($pdf_abs_file_path);
    
    if(File::exists($pdf_abs_file_path)){
      
      return true;
    }else{
      thrw("Unable to create or find {$view_data['file_name']}");
    }

  }

  public function load_aggrs_to_upload($data){
    //$cust = $data['cust_id']; // TODO
    $brwr_repo = new BorrowerRepositorySQL();
    $person_repo = new PersonRepositorySQL();
    $cust = $brwr_repo->get_record_by('cust_id', $data['cust_id'], ['lender_code','category','owner_person_id','flow_rel_mgr_id','prob_fas','aggr_valid_upto', 'acc_purpose']);
    
    $aggr_repo = new AgreementRepositorySQL();
    $field_names = ['lender_code', 'status','aggr_type', 'acc_purpose'];
    $aggr_expiry_days = config('app.aggr_expiry_thers_days');

    $use_onboarded = ($cust->aggr_valid_upto != null && $cust->aggr_valid_upto < Carbon::now()->addDays($aggr_expiry_days)) ? true : false;

    if($cust->category == 'Probation' && $cust->prob_fas <= 1){
        $aggr_type = 'onboarded';
    }
    else if(($cust->category != 'Condonation' && $cust->category != 'Probation') || $cust->prob_fas == 1 || $use_onboarded){
        $aggr_type = 'onboarded';
    }else{
        $aggr_type = $cust->category;
    }

    $field_values = [$cust->lender_code, 'enabled',strtolower($aggr_type), 'float_advance'];
    
    $master_aggr = $aggr_repo->get_record_by_many($field_names , $field_values , ["name", "aggr_doc_id", "aggr_duration","aggr_type","duration_type"]);

    if(!$master_aggr){
      thrw("No master agreement configured for customer's category (".ucwords($aggr_type).")");
    }
    #s$result["master_agreements"] = get_select_obj_arr([$master_aggr], 'aggr_doc_id', 'name');

    $result["master_agreement"] = $master_aggr;

    $result['master_aggr_folder_rel_path'] = separate(['files', get_file_rel_path(null, "master_agreements")]);;
      
    $result['cust_name'] = $person_repo->full_name($cust->owner_person_id);
    $result['rm_name'] = $person_repo->full_name($cust->flow_rel_mgr_id);
    

    return $result;
  }


  private function get_aggr_details($aggr_type, $aggr_doc_id, $country_code)
  {
    $agree_type = array("onboarded", "condonation", "probation");
      
    if(in_array($aggr_type, $agree_type)){     
      $agr_repo =  new AgreementRepositorySQL( MasterAgreement::class);
     }
    else{ 
      $agr_repo =  new CustAgreementRepositorySQL( CustAgreement::class);
     }
    $aggr = $agr_repo->get_record_by('aggr_doc_id',$aggr_doc_id,['aggr_duration']);
    
   
    return ["loan_products" => $loan_products, "valid_from" => $aggr->valid_from, "valid_upto" => $aggr->valid_upto];
  }



  /*public function get_agrmt_products($aggr_doc_id){
      $agr_repo = new AgreementRepositorySQL();
      return $agr_repo->get_record_by('aggr_doc_id', $aggr_doc_id, ['product_id_csv'], $this->country_code);
  }*/

  public function get_aggr_file_rel_path($data){
    
    if(array_key_exists('aggr_doc_id', $data) && $data['aggr_doc_id'] != null){
        if($data['aggr_type'] == "Master"){
          $aggr_folder = get_file_rel_path(null, "master_agreements").DIRECTORY_SEPARATOR.$data['aggr_doc_id'].".pdf";
          
        }else if($data['aggr_type'] == "Draft"){
          $aggr_folder = get_file_rel_path($data['cust_id'], "agreement", "unsigned_pdf").DIRECTORY_SEPARATOR.$data['aggr_doc_id'].".pdf";           
        }

        if(File::exists(flow_file_path().$aggr_folder)){
          $data['aggr_file_rel_path'] = $aggr_folder; 
        }
        return $data;
    }else{
        thrw("Agreement Doc ID is required.");
    }

  }

  public function inactivate_aggr($aggr_id, $cust_id){
     try
    {
        DB::beginTransaction();
          $cust_aggr_repo = new CustAgreementRepositorySQL();
          $cust_aggr_repo->update_record_status(Consts::AGGR_INACTIVE, $aggr_id);
         
          $borrower_repo = new BorrowerRepositorySQL();  
          $borrower_repo->update_model(['cust_id' => $cust_id, 'aggr_status' => 'inactive', 'current_aggr_doc_id' => null], 'cust_id');  
          
        DB::commit();
        
    }catch (\Exception $e) {

        DB::rollback();  
        Log::warning($e->getTraceAsString());     
        if ($e instanceof QueryException){
          throw $e;
        }else{
        thrw($e->getMessage());
        }
    }
    }
    public function check_mobile_agreement($cust_id)
    {
          $cust_aggr_repo = new CustAgreementRepositorySQL();
          $country_code = $this->country_code;
          $cust_aggrs = $cust_aggr_repo->get_existing_aggr($cust_id, " and status in('draft','active')");
          $aggr_type = null;
          $aggr_obj = null;
          foreach ($cust_aggrs['agreements'] as $cust_aggr) {
            if($cust_aggr->status == "draft"){
              $aggr_type = 'CustomerDraft';
               break;
            }
            $is_valid = is_aggr_valid($cust_aggr->valid_from, $cust_aggr->valid_upto);
            if($cust_aggr->status == "active" && $is_valid == true){
              $aggr_type = 'CustomerActive';
            }
          }
          if($aggr_type == null){
            $aggr_repo = new AgreementRepositorySQL();
            $master_aggr = $aggr_repo->get_recent_master_agreement();
            $aggr_type = 'Master';
          }
          $view_aggr = false;
          $sign_aggr = false;

          if($aggr_type == 'CustomerActive'){
            $view_aggr = true;
          }else{
            $sign_aggr = true;
          }
          return ['view_aggr' => $view_aggr, "sign_aggr" => $sign_aggr ];      
    }
    public function get_mobile_agreement($cust_id)
    {
          $cust_aggr_repo = new CustAgreementRepositorySQL();
          $country_code = $this->country_code;
          $cust_aggrs = $cust_aggr_repo->get_existing_aggr($cust_id, " and status in('draft','active')");
          $aggr_type = null;
          $aggr_obj = null;
          foreach ($cust_aggrs['agreements'] as $cust_aggr) {
            if($cust_aggr->status == "draft"){
              $aggr_type = 'CustomerDraft';
              $aggr_obj = $cust_aggr;
               break;
            }
            $is_valid = is_aggr_valid($cust_aggr->valid_from, $cust_aggr->valid_upto);
            if($cust_aggr->status == "active" && $is_valid == true){
              $aggr_type = 'CustomerActive';
              $aggr_obj = $cust_aggr;
            }
          
          }
          if($aggr_type == null){
            Log::warning("master_agreements");
            Log::warning($cust_aggr->status);
            $aggr_repo = new AgreementRepositorySQL();
            $master_aggr = $aggr_repo->get_recent_master_agreement($cust_aggr->valid_upto);
            $aggr_type = 'Master';
            $aggr_obj = $master_aggr;
          }
            $product_ids = explode(",",$aggr_obj->product_id_csv);
            $loan_product_repo = new LoanProductRepositorySQL();  
            $loan_products = $loan_product_repo->get_products($product_ids);
          return ['agreement' => $aggr_obj, 'loan_products' => $loan_products, 'aggr_type' => $aggr_type ];      
    }


}
