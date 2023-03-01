<?php
namespace App\Console\Commands;

use App\Consts;
use App\Jobs\StatementImportJob;
use App\Mail\FlowCustomMail;
use App\Models\Account;
use App\Services\FlowInternalService;
use Webklex\IMAP\Commands\ImapIdleCommand;
use Webklex\PHPIMAP\Message;
use Exception;
use Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ImapListenerCommand extends ImapIdleCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:listen';

    /**
     * Holds the account information
     *
     * @var string|array $account
     */
    protected $account = "default";

    /**
     * Holds the country code
     *
     * @var string $country_code
     */
    protected $country_code = "";
    
    /**
     * Holds the account provider code
     *
     * @var string $acc_prvdr_code
     */
    protected $acc_prvdr_code = "";

    /**
     * Get all sender emails
     * @param Message $message
     */
    private function get_sender_emails(Message $message) {
        
        $senders = [];
        $addresses = $message->getSender()->toArray();
        foreach($addresses as $address) {
            $sender = $address->toArray();
            $senders[] = $sender['mail'];
        }
        return $senders;
    }   

    /**
     * Callback used for the idle command and triggered for every new received message
     * @param Message $message
     */

    public function onNewMessage(Message $message){

        try
        { 
            $subject = $message->getSubject();
            Log::warning($subject);
            if(Str::contains($subject ,"BK")) {
                
                $this->country_code = 'RWA';
                $this->acc_prvdr_code = Consts::BOK_AP_CODE;
                set_app_session($this->country_code);
                
                if(Str::contains($subject ,"BK Online Banking") ){
                    $this->handle_rbok_otps($message);
                }
                elseif(Str::contains($subject,"BK INTERNET BANKING STATEMENT")){
                    $this->saveAttachment($message);
                }  
            }
            elseif(Str::contains($subject, "Channel Domain Transaction Report")) {
                
                // $body = $message->getTextBody();
                // if(Str::contains($body, 'Airtel Money')) {

                $senders = $this->get_sender_emails($message);
                $email = 'airtelmoney@rw.airtel.com';
                
                if(in_array($email, $senders)) {
                    $this->country_code = 'RWA';
                    set_app_session($this->country_code);
                    $this->acc_prvdr_code = Consts::RATL_AP_CODE;
                    $this->handle_ratl_statements($message);
                }
            }
        }
        catch(Exception $e){
            $data['country_code'] = $this->country_code;
            $data['acc_prvdr_code'] = $this->acc_prvdr_code;
            $data['failed_at'] = Carbon::now();
            $data['subject'] = $subject;
            $data['message'] = ($e->getMessage());
            $data['exception'] = ($e->getTraceAsString());
            send_email('imap_email_process_error', [config('app.app_support_email')], $data);
        }
    }

    /**
     * Extract MSISDN from RATL Statement
     * @param string $file_path
     * @param int $line_to_check
     */
    private function get_ratl_msisdn(string $file_path, int $line_to_check) {
        $lines = file($file_path);
        if(empty($lines)) return null;
		foreach($lines as $index => $line) {
			$line_num = $index+1;
			if($line_num == $line_to_check) break;
			if ($line_num > $line_to_check) return null;
		}
		preg_match("/Mobile Number : \d+,/", $line, $matches);
		if(!empty($matches)) {
			preg_match("/\d+/", $matches[0], $msisdn);
			if(!empty($msisdn)) return $msisdn[0];
		}
		return null;
	}	

    /**
     * Handle the mail containing RATL Statement
     * @param Message $message
     */
    private function handle_ratl_statements(Message $message) {
        
        $attachments = $this->saveAttachments($message, $this->country_code, $this->acc_prvdr_code);
        foreach ($attachments as $attachment) {
            $msisdn = $this->get_ratl_msisdn($attachment, 2);
            if($msisdn == null) thrw("MSISDN not found in Airtel Statement");

            $account_info = (new Account)->get_record_by_many(['acc_number', 'to_recon', 'status'], [$msisdn, true, 'enabled'], ['id', 'acc_prvdr_code', 'country_code', 'web_cred', 'acc_number', 'disb_int_type', 'stmt_int_type', 'acc_purpose', 'network_prvdr_code']);
            $queue = get_stmt_import_queue($account_info);
            $account_info->stmt_path = $attachment;
            StatementImportJob::dispatch($account_info)->onQueue($queue);
        }
    }

    /**
     * Handle the mail containing RBOK OTPs
     * @param Message $message
     */
    private function handle_rbok_otps(Message $message) {
        
        $body = $message->getHTMLBody();
        preg_match("/OTP:\s*\d+/", $body, $matches);
        if(sizeof($matches) > 0){
            $temp_otp =  filter_var($matches[0], FILTER_SANITIZE_NUMBER_INT);
            if(strlen((string)$temp_otp) == 6){
                $otp =  filter_var($matches[0], FILTER_SANITIZE_NUMBER_INT);
                $intenal_serv =  new FlowInternalService;
                $data['otp'] = $otp;
                $data['received_at'] = datetime_db();
                $data['agent_id'] = '100077653265';
                $data['entity'] = 'bok_agent';
                $data['mob_num'] = "BOKMobile";
                $data['vendor'] = Consts::BOK_AP_CODE;
                $intenal_serv->log_forwarded_otp($data);
            }else{
                thrw("{$temp_otp} does not appear to be a OTP");
            }
        }else{
            thrw("{$matches} does not contain OTP");
        }
    }
    
    private function saveAttachments(Message $message, string $country_code, string $acc_prvdr_code){
       
        $attachment_exists = $message->hasAttachments();
        if($attachment_exists){
            $attachment_paths = [];
            $attachments = $message->getAttachments();
            
            foreach($attachments as $attachment){
                $date_time = now()->format("Y-m-d_H:m:s_");

                $path = ($acc_prvdr_code == Consts::BOK_AP_CODE) ? "$acc_prvdr_code/" : "files/statements/$country_code/stmts/$acc_prvdr_code/";
                $path = flow_storage_path($path);
                if (!is_dir($path)) {
                    create_dir($path);
                }
                $attachment->save("$path/$date_time");
                $attachment_paths[] = "$path/$date_time". $attachment->getName();
            }
            return $attachment_paths;
        }else{
            thrw("Attachment not found in the statement email");
        }
    }
}
