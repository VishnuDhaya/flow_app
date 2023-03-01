<?php
namespace App\Services\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;
use App\Consts;
use App\Services\LoanService;
use App\Services\Support\ExcelExportService;
use App\Services\CommonService;
use App\Mail\EmailComments;
use App\Mail\EmailReports;
use Mail;

class EmailScheduleService{

    public function __construct(){
        //$this->country_code = session('country_code');
        $this->country_code = "UGA";
       
        
    }
    public function __invoke(){
        $this->send_loan_reports(); 
    }

    public function mail()
    {
        $data = array('name'=>"Our Code World");
        // Path or name to the blade template to be rendered
        $template_path = 'email_template';

        Mail::send(['text'=> $template_path ], $data, function($message) {
            // Set the receiver and subject of the mail.
            $message->to('anyemail@emails.com', 'Receiver Name')->subject('Laravel First Mail');
            // Set the sender
            $message->from('mymail@mymailaccount.com','Our Code World');
        });

        return "Basic email sent, check your inbox.";
    }

    
    public function send_loan_reports(){
       
        $due_file = $this->loan_report(Consts::LOAN_DUE);
        $over_due_file = $this->loan_report(Consts::LOAN_OVERDUE);
        $cmn_serv = new CommonService();
        
        $data = ["country_code" => $this->country_code, "priv_code" => Consts::PRIV_CODE_EMAIL, "status" => "enabled"];
        
        $app_users = $cmn_serv->get_users($data);
        
        $app_users = collect($app_users);
        $email_ids[] = $app_users->pluck('email')->all();
        //Mail::to($email_ids)->send(new EmailReports($due_file, $over_due_file));
        Mail::to(env('MAIL_USERNAME'))->send(new EmailReports($due_file, $over_due_file));
        
    }
    public function loan_report($status){

        $loan_serv = new LoanService();  
        //$loan_overdue = Consts::LOAN_OVERDUE;
        $loans = array();
        try{
            $loans = $loan_serv->loan_search(['status' => $status]);
        }
        catch(\Exception $e){

            //DB::rollback();   
            //throw new Exception($e);
        }



        $records_count = sizeof($loans);
        if($records_count > 0){
            
            //$header = Str::studly(array_keys((array)$loans[0]));
           
            $header[] = array_keys((array)$loans[0]);        
            //$header[] = array_map(Str::studly($headers));
            
        }else{
            $header[] = "No Records with $status status";
        }
        $file_name = (new ExcelExportService($loans, $header, "reports"))->export("Report_FA_$status");
        
        return [$file_name,$records_count];
    }
   
   

}