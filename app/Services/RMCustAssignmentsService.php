<?php
namespace App\Services;

use App\Consts;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\RMCustAssignmentsSQL;
use App\Services\Support\SMSNotificationService;
use Illuminate\Support\Facades\DB;
use Log;

class RMCustAssignmentsService

{ 

    public function rm_reassign($data)
    {
    
        $data['temporary_assign'] = array_key_exists('temporary_assign', $data) ? $data['temporary_assign'] : false;
        if($data['from_rm_id'] != $data['to_rm_id'])
        {
            if($data['assign_by'] == "territory")
            {
                $result = $this->rm_reassign_by_territory($data); //perm
            }
            elseif($data['assign_by'] == "rm")
            {
                $result = $this->rm_reassign_by_rm($data); // temp
            }
            elseif($data['assign_by'] == "customer")
            {
                $result = $this->rm_reassign_by_customer($data); //perm
            }   
            elseif($data['assign_by'] == "temp_assign")
            {
                $result = $this->rm_reassign_by_temp_rm_id($data); // perm
            }
        }   
        else
        {
            thrw("Should not reassign to the same RM.");

        }             
    }

    
    public function operations($data, $customer, $cust_id = null)
    {

        $rm_cust_repo = new RMCustAssignmentsSQL();
        $borr_repo = new BorrowerRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        
        try{
            DB::beginTransaction();
            $cust_id = isset($customer->cust_id) ? $customer->cust_id : $cust_id;
            $rm_cust_repo->update_existing_customer($cust_id);
            $borr_repo->update_model_by_code(['cust_id' => $cust_id, 'flow_rel_mgr_id' => $data['to_rm_id'] ]);  
            
            $to_rm_name = $person_repo->full_name($data['to_rm_id']);  
            $this->update_rm_in_loan_appls($cust_id,$data['from_rm_id'], $data['to_rm_id'], $to_rm_name);  
            $this->update_rm_in_loans($cust_id, $data['from_rm_id'], $data['to_rm_id'], $to_rm_name);
            $rm_array=[]; 
            $rm_array['country_code'] = session('country_code');
            $rm_array['cust_id'] = $cust_id;
            $rm_array['from_rm_id'] = $data['from_rm_id'];
            $rm_array['rm_id'] = $data['to_rm_id'];
            $rm_array['territory'] = $customer->territory;  
            $rm_array['status'] = "active";                            
            $rm_array['temporary_assign'] = false;
            $rm_array['from_date'] = date_db();
            $rm_array['to_date'] = null;
            $rm_array['reason_for_reassign'] = $data['reason_for_reassign'];
                     
            

            if($data['temporary_assign'] == false) // permanent assignment // by_territory
            { 
                $rm_cust_repo->insert_model($rm_array);
                    
                $from_rm_name = $person_repo->full_name($data['from_rm_id']);               
                $to_rm = $person_repo->get_person_name($data['to_rm_id']);
                $to_rm_name = full_name($to_rm);
                $cust_details = $person_repo->get_person_name($customer->owner_person_id);
                
                $notify_serv = new SMSNotificationService();
                if($data['reason_for_reassign'] == "RM is resigning")
                {
                $message = 'RM_CUST_ASSIGN_MSG';
                $notify_serv->send_notification_message(['country_code' => session('country_code'),
                                                    'disable_rm_name' => $from_rm_name,
                                                    'subs_rm_name' => $to_rm_name,
                                                    'subs_rm_mobile_num'=> $to_rm->mobile_num,
                                                    'cust_name' => $cust_details->first_name,
                                                    'cust_mobile_num' => $cust_details->mobile_num], $message);
                }
                elseif($data['reason_for_reassign'] == "other")
                {
                    
                $message = 'RM_CUST_OTHER_MSG';
                $notify_serv->send_notification_message(['country_code' => session('country_code'),
                                                    'disable_rm_name' => $from_rm_name,
                                                    'subs_rm_name' => $to_rm_name,
                                                    'subs_rm_mobile_num'=> $to_rm->mobile_num,
                                                    'cust_name' => $cust_details->first_name,
                                                    'cust_mobile_num' => $cust_details->mobile_num], $message);
                }     
            }                           
            else
            {
                $rm_array['temporary_assign'] = true;
                $rm_cust_repo->insert_model($rm_array);
            }
            DB::commit();
        }
        catch (Exception $e)
        {
            DB::rollback();
            if ($e instanceof QueryException){
              throw $e;
            }else{
              thrw($e->getMessage());
            }
           
        }   

    }

    public function reassign_customers($customers, $data)
    {
        foreach($customers as $customer)
        {   

            $this->operations($data, $customer);

        }
    }

    public function rm_reassign_by_territory($data)
    {
        if($data['territories'])
        {
            foreach($data['territories'] as $territory)
            {

                $borr_repo = new BorrowerRepositorySQL();
            
                $customers = $borr_repo->get_records_by_many(['flow_rel_mgr_id', 'territory', 'status'], [$data['from_rm_id'], $territory, "enabled"], ['cust_id','territory','owner_person_id']);

                $this->reassign_customers($customers, $data);        

            }      
        } 
        else
        {
            thrw("Please select territories");
        }     
    }

    public function rm_reassign_by_rm($data)
    {
       
        $borr_repo = new BorrowerRepositorySQL();       
       
        $customers = $borr_repo->get_records_by_many(['flow_rel_mgr_id', 'status'], [$data['from_rm_id'], "enabled"], ['cust_id','territory','owner_person_id']);

        $rm_cust_repo = new RMCustAssignmentsSQL(); 

        $temp_assign_check = $rm_cust_repo->get_records_by_many(['status', 'rm_id', 'temporary_assign'], ["active", $data['from_rm_id'], true], ['cust_id']);               
                                    
        if($temp_assign_check)
        {                             
            thrw("Cannot able to reassign because some customers is temporarily assigned ");                                         
        }
        else
        {
            $this->reassign_customers($customers, $data);
        } 

    }

    public function rm_reassign_by_customer($data)
    {     
        $borr_repo = new BorrowerRepositorySQL();       

        $cust_id = $data['cust_id'];
        $customer = $borr_repo->get_record_by_many(['cust_id', 'status'], [$cust_id, "enabled"], ['cust_id', 'territory', 'owner_person_id']);

        if($customer){
            $this->operations($data, $customer);
        }
        else{
            thrw("Cannot able to reassign this customer because this customer is in disabled status");
        }
        
    }


    public function rm_reassign_by_temp_rm_id($data)
    {     
        $borr_repo = new BorrowerRepositorySQL();       
        $rm_cust_repo = new RMCustAssignmentsSQL();    
       
        $temp_rm_id = (new RMCustAssignmentsSQL())->get_temp_assigned_rm($data);  
        $customers = $rm_cust_repo->get_records_by_many(['from_rm_id', 'rm_id', 'status', 'temporary_assign'], [$data['from_rm_id'], $temp_rm_id, 'active', true], ['cust_id']);   
        
        foreach($customers as $customer)
        {
            $cust_detail = $borr_repo->get_record_by('cust_id', $customer->cust_id, ['cust_id', 'territory','owner_person_id']);    
        
            $this->operations($data, $cust_detail);

        }                
    }

    public function get_rm_and_terri_details($data)
    {       
        $repo = (new RMCustAssignmentsSQL());
        $person_repo = new PersonRepositorySQL();
        $borr_repo = new BorrowerRepositorySQL();
        $rm_array = [];
        $rm_data = [];
    
        $rm_name = $person_repo->full_name($data['from_rm_id']);
        $territories = $repo->get_rm_territories($data);        

        foreach ($territories as $territory){
            
            $data['territory'] = $territory->territory;
            $teri_cust_count = $repo->get_terri_cust_count($data);
            $rm_data[] = array('territory' => $data['territory'], 
                          'ter_cust_count' => $teri_cust_count->count);
        }
        
        $cust_count = $repo->get_total_cust_count($data); 
        $rm_array['rm_name'] = $rm_name;
        $rm_array['rm_data'] = $rm_data; 
        $rm_array['total_cust_count'] = $cust_count->count; 
        return $rm_array;
    
    }  
    
    public function temp_assigned_rms($data)
    {
        $results = (new RMCustAssignmentsSQL())->get_temp_assigned_rms($data);
        $person_repo = new PersonRepositorySQL();
        $rm_data = [];

        foreach ($results as $result)
        {
            $temp_rm_name = $person_repo->full_name($result->rm_id);
            $from_rm_name = $person_repo->full_name($result->from_rm_id);

            $rm_data[] = array('id' => $result->from_rm_id, 
                           'rm_name' => $from_rm_name." -> ".$temp_rm_name." (temp)",
                           'from_rm_name' => $from_rm_name);
        }             
                return $rm_data; 
        
      
    }

    public function update_rm_in_loan_appls($cust_id, $from_rm_id, $to_rm_id, $to_rm_name) {

        $loan_appl_repo = new LoanApplicationRepositorySQL;
        DB::update("update loan_applications set loan_approver_name = ?, loan_approver_id = ?, flow_rel_mgr_id = ? where cust_id = ? and status = ? and flow_rel_mgr_id = ? ",[$to_rm_name, $to_rm_id, $to_rm_id, $cust_id, Consts::LOAN_APPL_PNDNG_APPR, $from_rm_id]);
    }

    public function update_rm_in_loans($cust_id, $from_rm_id, $to_rm_id, $to_rm_name) {

        $loan_repo = new LoanRepositorySQL;

        $disb_loan_status = Consts::DISBURSED_LOAN_STATUS;
        $addl_status = [Consts::LOAN_PNDNG_DSBRSL, Consts::LOAN_HOLD];

        $loan_status = array_merge($disb_loan_status, $addl_status);

        $status_to_check = csv($loan_status);

        DB::update("update loans set loan_approver_name = ?, loan_approver_id = ?, flow_rel_mgr_id = ? where cust_id = ? and status in($status_to_check) and flow_rel_mgr_id = ? ",[$to_rm_name, $to_rm_id, $to_rm_id, $cust_id, $from_rm_id]);
    }
}