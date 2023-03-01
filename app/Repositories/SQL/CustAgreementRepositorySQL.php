<?php
namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;
use App\Models\CustAgreement;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\SQL\AgreementRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use Carbon\Carbon;
use Log;
use File;
class CustAgreementRepositorySQL extends BaseRepositorySQL{

        public function __construct(){
            parent::__construct();
            $this->class = CustAgreement::class;
        }
        
        public function model(){        
            return $this->class;
        }

        public function create_agreement(array $data){

            $aggr['country_code'] = $data['country_code'];
    		$aggr['cust_id'] = $data['cust_id'];
    		$aggr['status'] = "draft";
    		$date = aggr_date();
    		$aggr['aggr_doc_id'] = "AGRT-{$data['cust_id']}-$date";   
    		if(!empty($data['product_ids'])){
    			$product_id_csv = implode(',', $data['product_ids']) ;
    		}else{
    			thrw("Choose products to generate Agreement");
    		}
             $date = Carbon::now();
             $today = date("Y-m-d",strtotime($date));
            if(array_key_exists('valid_from', $data)){ 
                if($data['valid_from'] == $today)
                {
                  $aggr['valid_from'] = $data['valid_from'];
                }
               else{
                thrw("Valid from date must be current date");
                }
            }
    		if(array_key_exists('valid_upto', $data)){ 
    			if($data['valid_upto'] > Carbon::now()){
                    $aggr['valid_upto'] = Carbon::createFromFormat('Y-m-d', $data['valid_upto'])->endOfDay()->toDateTimeString();
                    
                    // a$ggr['valid_upto'] = $data['valid_upto']->endOfDay();
    			}else{
    				thrw("Valid upto date must be a future date");
    			}
    		}
    		$aggr['product_id_csv'] = $product_id_csv;
            
            parent::insert_model($aggr);    

    		return $aggr;
                	
        }
         public function get_customer_agreement($cust_id)
        {
                $cust_aggr = DB::table('cust_agreements')
                            ->select('aggr_doc_id','product_id_csv','valid_from','valid_upto','status')
                            ->where('cust_id',$cust_id)
                            ->whereIn('status',["active","draft"])
                            ->first();
                return $cust_aggr;             
        }
        public function get_existing_aggr($cust_id,$addl_condition = null){
            //$addl_condition = "and status in('draft','active')";
            if($addl_condition == null){
                $addl_condition = "";    
            }
            
            $agreements = parent::get_records_by_many(['cust_id'],[$cust_id],['id','country_code','aggr_doc_id','valid_from','valid_upto','status','product_id_csv','aggr_type', 'duration_type'],"", $addl_condition,false);
            //$agreements['existing_agreements'] = $existing_agreements;
            $prompt_inactivate = false;
            foreach($agreements as $agreement){
                if($agreement->aggr_doc_id){
                  $folder = "pdf";      
                  if($agreement->status == "draft"){
                    $folder = "unsigned_pdf";
                  }  
                  $aggr_folder = get_file_rel_path($cust_id, "agreement", $folder).DIRECTORY_SEPARATOR.$agreement->aggr_doc_id.".pdf";

                  
                  if(File::exists(flow_file_path().$aggr_folder)){
                    $agreement->aggr_file_rel_path = separate(['files', $aggr_folder]);
                    
                  }
                  if($agreement->status == "draft" || $agreement->status == "active"){
                  	$prompt_inactivate = true;
                  }
                 }
            }
            return ['agreements' => $agreements, "prompt_inactivate" => $prompt_inactivate];
        }

        public function update_aggr_status($id, $status){
            
            return DB::table('cust_agreements')->where('id',$id)->update(['status' => $status]);
        }


       public function get_cust_aggr(array $data){
            $aggr = DB::table('cust_agreements')->select('id','status','valid_upto','cust_id')
                    ->where([
                                ['cust_id',$data['cust_id']],
                                ['aggr_doc_id',$data['aggr_doc_id']],
                            ])->first();
                    #->whereNotIn('status',['inactive'])   
            return $aggr;
        }

        public function get_active_cust_aggr(array $data){
            $aggr = DB::table('cust_agreements')->select('aggr_doc_id')
                    ->where([
                                ['country_code',$this->country_code],
                                ['cust_id',$data['cust_id']],
                                ['status','active'],
                            ])->first();
                    #->whereNotIn('status',['inactive'])
            return $aggr;
        }

        public function get_cust_draft_aggr(array $data){
            $aggr = DB::table('cust_agreements')->select('id','status','valid_from','valid_upto','aggr_doc_id')
                    ->where([
                            ['country_code',$this->country_code],
                            ['cust_id',$data['cust_id']],
                            ['status','draft']
                        ])->first();
            return $aggr;
        }
     
        public function get_aggr_details($id){
            return parent::find($id, ['country_code','aggr_doc_id','product_id_csv','cust_id','status','valid_from','valid_upto']);
        }
        public function get_cust_agreement_status($aggr_doc_id)
        {
            return DB::selectOne("/*$this->api_req_id*/ select status from cust_agreements where aggr_doc_id = ? and country_code = ? limit 1",[$aggr_doc_id, $this->country_code]);  
        }
       
        
        public function inactivate_agreement($cust_id, $check_aggr_type = true)
        {
            $cust = (new BorrowerRepositorySQL)->find_by_code($cust_id, ['category','tot_loans']);
            $aggr_type = strtolower($cust->category);

            if($check_aggr_type == false){
                $cust_agrmnt = $this->get_record_by_many(['cust_id', 'status'], [$cust_id, 'active'], ['id','aggr_type']);
            }
            elseif(($aggr_type != 'probation' || $cust->tot_loans >= config('app.default_prob_fas') ) && $aggr_type != 'condonation'){
                $cust_agrmnt = $this->get_record_by_many(['cust_id', 'status', 'aggr_type'], [$cust_id, 'active', 'probation'], ['id','aggr_type']);
            }
            if ($cust_agrmnt) {
                $borr_repo = new BorrowerRepositorySQL();
                $this->update_record_status('inactive', $cust_agrmnt->id);
                $borr_repo->inactivate_current_aggr($cust_id);
            }

        }

    }

