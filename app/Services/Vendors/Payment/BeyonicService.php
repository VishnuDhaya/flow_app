<?php 

namespace App\Services\Vendors\Payment;
use App\Services\LoanService;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\DeadLettersRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Consts;
use DB;
use Illuminate\Support\Str;
use Log;

class BeyonicService{

    public function receive_payment_notification($req)
    {
        if($req['status'] == "successful"){

            #$payment_detail = json_decode($req);
            $data = $req['collection_request']; #collection request

            $currency_code = $req['currency'];

            if($currency_code == 'BXC'){
                $currency_code = 'UGX';
            }
            // session()->put('country_code', '*' );

            // $market_repo = new MarketRepositorySQL();
            // $market = $market_repo->get_market($currency_code);
            $market = DB::selectOne("select isd_code, country_code from markets where currency_code = ?", [$currency_code]);
            $this->isd_code = $market->isd_code;

            $this->country_code = $market->country_code;
            session()->put('country_code', $market->country_code );

            $this->currency_code = $currency_code;
            session()->put('currency_code', $currency_code );

            $loan_doc_id = null;
            $cust_id = null;

            if(array_key_exists('loan_doc_id', $data['metadata'])){
                $loan_doc_id = $data['metadata']['loan_doc_id'];
            }
            else if(array_key_exists('cust_id', $data['metadata'])){
                $cust_id = $data['metadata']['cust_id'];
                
            }
            # metadata
            
            if($loan_doc_id){
                //check if loan_doc_id is valid
                
                $loan_repo = new LoanRepositorySQL();
                $loan = $loan_repo-> get_os_loan($loan_doc_id);
                if(!$loan){
                    $loan_doc_id = null;
                }
            }else{
                #$cust_id = $data['metadata']['cust_id']; # metadata
                if($cust_id){
                    //check if cust_id is valid
                    $borrow_repo = new BorrowerRepositorySQL();
                    $cust = $borrow_repo->get_customer($cust_id);
                    if(!$cust){
                        $cust_id = null;
                    }
                }else{
                    $mob_num = $this->get_phone_num($req['phonenumber'], $market->isd_code);
                    if($mob_num){
                        $cust_id = $this->get_cust_id_by_phone_num($mob_num);
                        $cust_id = $cust_id->cust_id;
                    }else{

                    }
                    
                }
                
                if($cust_id){
                    $loan_repo = new LoanRepositorySQL();
                    $loan_doc_id_obj = $loan_repo->get_os_loan_doc_id($cust_id);
                    $loan_doc_id = $loan_doc_id_obj[0];
                }    
            }

            
            if($loan_doc_id){
                $this->capture_payment_against_loan($loan_doc_id, $req);

            }else{
                $this->add_to_dead_letter_queue($data);
            }
        
            return true;
        }
        else{

        }
    }

    function get_phone_num($payer, $isd_code){
        // Log::warning($payer);
        // if(count($payer) > 0){
        //     $payer = $payer[0]; #NA
        // }else{
        //     Log::warning("No phone number exist in the request");
        //     return null;
        // }
        #$loan_repo = new LoanRepositorySQL();
        
        #$isd_code = $market->isd_code;
        $isd_code = "+". $isd_code;
        $mob_num = null;
        if(Str::startsWith($payer, $isd_code)){
            #$mob_num = Str::of($payer)->replace($isd_code, '');
            $mob_num = str_replace($isd_code, '',$payer);
        }
        return $mob_num;
    }
    function capture_payment_against_loan($loan_doc_id, $data){

        $loan_serv = new LoanService();

        $collection_req = $data['collection_request'];
        $contact = $collection_req['contact'];

        $txn_date = $data['payment_date']; #data

        $loan_repo = new LoanRepositorySQL();
        $loan = $loan_repo->get_loan($loan_doc_id);
       
        #$txn_exec_by = $this->get_phone_num($data['phone_nos'], $this->isd_code);
        $txn_exec_by = $contact['first_name'] ." ". $contact['last_name'];  
        $remarks = $data['reference']; #data
        $txn_id = $collection_req['collection']; #NA
        $amount = $data['amount']; #data

        $os_loan = $this->check_loan_status($loan->status);

        
        if($os_loan ){
            $to_ac_id = $this->get_payment_ac_id($loan->lender_code, $loan->data_prvdr_code, 'UBNC'); #TODO

            $loan_txn = [
            
                    "loan_doc_id" => $loan_doc_id,
                    "txn_date" => $txn_date,
                    "paid_date" => $txn_date, 
                    'txn_exec_by' => $txn_exec_by,
                    'remarks' => $remarks,
                    "txn_id" => $txn_id,
                    "amount" => $amount,
                    "to_ac_id" => $to_ac_id,
                    "txn_mode" => "payment_gateway",
                    "send_sms" => true,
                    "is_part_payment" => true, # TODO should we allow part payment?
                    "waive_penalty" => true
                ];
                
            
            $loan_serv->capture_repayment($loan_txn);
            

        }else{
            $this->add_to_dead_letter_queue($data);
        }

        // Update your transaction status in the db where the external_ref = $response['external_ref']

    }

    function check_loan_status($status){
        if($status == Consts::LOAN_DUE || $status == Consts::LOAN_OVERDUE || $status == Consts::LOAN_ONGOING){
            return true;
        }
        
    }
    
    function get_payment_ac_id($lender_code, $data_prvdr_code, $acc_prvdr_code){
        $account_repo = new AccountRepositorySQL();
       
        $keys = ["lender_code",'lender_data_prvdr_code', 'acc_prvdr_code'];
        $values = [$lender_code, $data_prvdr_code, $acc_prvdr_code];
        
        $lender_accounts = $account_repo->get_accounts_by($keys, $values, ['id']);
        Log::warning($lender_accounts);
            /*$accounts = $acc_serv->get_lender_accounts();*/
        if(empty($lender_accounts) ){
            thrw("{$acc_prvdr_code} Payment account not configured for the lender : {$lender_code}");
        }
        if(sizeof($lender_accounts) > 1 ){
            thrw("More than one {$acc_prvdr_code} payment account configured for the lender : {$lender_code}");
        }
        return $lender_accounts[0]->id;
        
    }
    function get_cust_id_by_phone_num($mob_num){
        if($mob_num){

            $person_repo = new PersonRepositorySQL();
            $person_id = $person_repo->get_person_id_by_mobile_num($mob_num);
            $person_id =(string) $person_id[0]->id;

            $borrow_repo = new BorrowerRepositorySQL();
            $cust_id = $borrow_repo->get_cust_id($person_id);
            // DB::select("select cust_id from borrowers where contact_person_id = ? or owner_person_id = ?",[$person_id,$person_id]);
        
           return $cust_id;
        }
    }

    function add_to_dead_letter_queue($data){
        // $dead_let->$acc_prvdr_code = 'UBNC';
        // $dead_let->$country_code = $this->$country_code;
        // $dead_let->$notify_json = json_encode($data);
        $dead_let = array('acc_prvdr_code' => 'UBNC', 
                          'country_code' => $this->country_code, 
                          'notify_json' => json_encode($data));

        $dead_letter_repo = new DeadLettersRepositorySQL();
        $dead_letter = $dead_letter_repo->insert($dead_let);
        

    }
}