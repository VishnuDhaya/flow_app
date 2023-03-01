<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FlowCustomMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $data;
    public $view;
    public $tries =1;
    
    public function __construct($view, $data)
    {
        $this->data = $data;
        $this->view = $view;
    }
    
    public function attachments_for_etl(array $mail_attachments) {
        
        if (isset($mail_attachments['dfs'])) {
            foreach($mail_attachments['dfs'] as $df) {
                $csv = array_to_csv(json_decode($df['df'], true));
                $this->attachData($csv, "{$df['file_name']}.csv");
            } 
        }
        if (isset($mail_attachments['err_info'])) {
            $err_info = $mail_attachments['err_info'];
            $data = array_to_txt($err_info['errors']);
            $this->attachData($data, "{$err_info['file_name']}.txt");
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $country_code = $this->data['country_code'] ?? null;
        $attachment = null;
        $attachment_as = null;
        $mime = null;
        $env = get_env();
        $subject = "[$country_code$env] - ";
        $this->data['app_url'] = rtrim(env('APP_URL'),'/');
        
        if($this->view == 'repeat_loan_failure'){
            $subject .= "REPEAT FA FAILED for {$this->data['cust']->biz_name} - ({$this->data['cust']->cust_id})";
        }
        elseif($this->view == 'repeat_acc_stmt_req_failure') {
            $subject .= "REPEAT ACC STMT REQ FAILED for {$this->data['acc_number']}";
        }
        elseif($this->view == 'process_util_otp'){
            $subject .= "FAILED to Process OTP from UTILITY APP for {$this->data['agent_id']}";
        }
        elseif($this->view == 'stmt_import_failure') {
            if(Str::contains($this->data['exception'], "OTP Not Received")){
                $msg = "OTP Not Received";
            }elseif(Str::contains($this->data['exception'], "Incorrect OTP") || Str::contains($this->data['exception'], "One-time Password authentication failed")){
                $msg = "Incorrect OTP";
            }elseif(Str::contains($this->data['exception'], "Login failed") || Str::contains($this->data['exception'], "password is incorrect")){
                $msg = "Login Failed or Authentication failed";
            }else{
                $msg = "Statement Import Failed";
            }
            $subject .= "{$this->data['network_prvdr_code']} {$this->data['acc_prvdr_code']} {$this->data['acc_number']} - {$msg}";
        }
        elseif($this->view == 'sms_send_failure'){
            $subject .= "IMPORTANT [SMS VENDOR OUTAGE] UNABLE TO SEND SMS USING '{$this->data['vendor_code']}'";
        }
        elseif($this->view == 'duplicate_profile_sms'){
            $subject .= "DUPLICATE PROFILES found for {$this->data['mobile_num']}";
        }
        elseif($this->view == 'repeat_kyc_notify'){
            $subject .= "Repeat KYC initiated for customer  {$this->data['cust_id']}";
        }
        elseif($this->view == 'reassign_lead_notify'){
            $subject = "KYC of {$this->data['biz_name']} assigned to you";
        }
        elseif($this->view == 'force_checkin') {
            $subject .= "[Force Checkin] - {$this->data['user_name']} - {$this->data['cust_id']}";
        }
        elseif($this->view == 'tf_transaction_import_failure'){
            $subject .= "TF Transactions Import Failed for {$this->data['cust_id']}";
        }
        elseif($this->view == 'capture_tf_repayment_failure'){
            $subject .= "Capture TF Repayment Failed for {$this->data['cust_id']}";
        }
        elseif($this->view == 'ussd_capture_failed'){
            $subject .= "USSD Disbursal Capture Failed for {$this->data['cust_id']} - {$this->data['loan_doc_id']}";
        }
        elseif($this->view == 'imap_email_process_error'){
            if(isset($this->data['subject']) and $this->data['subject'] == "BK Online Banking"){
                $subject .= "Failed to receive OTP for {$this->data['acc_prvdr_code']}";
            }else{
                $subject .= "Failed to get Attachment for {$this->data['acc_prvdr_code']}";
            }
        }
        elseif($this->view == 'util_otp_not_rcvd'){
            $subject .= "Failed to receive OTP for {$this->data['account']['acc_prvdr_code']} - {$this->data['account']['acc_number']}";
        }
        elseif($this->view == 'notification_failed'){
            $subject .= "Failed to send Notification to {$this->data['recipient_name']}";
        }
        elseif($this->view == 'lead_rm_assign'){

            if(array_key_exists('biz_name', $this->data) || array_key_exists('acc_prvdr_code', $this->data) ){
                $msg =  "{$this->data['acc_prvdr_code']} - {$this->data['account_num']}";
            }else {
                $msg =  "{$this->data['cust_name']}";
            }

            $subject .= "Action Needed - Pending RM Assignment $msg";
        }
        elseif($this->view == 'auto_capture_failure'){
            $subject .= "Auto Capture Failed for {$this->data['loan_doc_id']}";
        }
        elseif($this->view == 'auto_capture_review_pending'){
            $subject .= "Can not Capture Payment for {$this->data['loan_doc_id']}"." (".dd_value($this->data['review_reason']).")";
        }        
        elseif($this->view == 'approval_notification'){
            $subject .= "{$this->data['date']} - Pending FA approval for {$this->data['cust_name']}";
        }
        elseif($this->view == 'pre_approved_fas'){
            $subject .= "Pre-approved FAs today - {$this->data['date']}";
        }
        elseif($this->view == 'pre_appr_remove_notification'){
            $subject .= "{$this->data['date']} - Pre-approval Removed for {$this->data['cust_name']}";
        }
        elseif($this->view == 'daily_metrics'){
            $subject .= "{$this->data['yesterday_date']} - Daily {$this->data['country_code']} Metrics";
        }
        elseif($this->view == 'duplicate_fa'){
            $subject .= "Duplicate FA found for {$this->data['cust_id']}";
        }
        elseif($this->view == 'bypassed_fas_report'){
            $subject .= "Bypassed for 3 or more FAs - {$this->data['start_date']} to  {$this->data['end_date']}";
        }
        elseif($this->view == 'weekly_sms_issues_report'){
            $subject .= "Weekly report for SMS related issues - {$this->data['start_date']} to  {$this->data['end_date']}";
        }
        elseif($this->view == 'fa_approval_for_rm_call_failed'){
            $subject .= "[NEED ACTION] FA Approval reminder call to {$this->data['first_name']} failed.";
        }
        elseif($this->view == 'reminder_call_failed_alert'){
            $subject .= "[NEED ACTION] Reminder call failed alert {$this->data['reason']}";
        }
        elseif($this->view == 'updated_locations'){
            $subject .= "New Locations added from {$this->data['start_date']} to  {$this->data['end_date']}";
        }
        elseif($this->view == 'visit_suggestions'){
            $subject .= "List of customer needs visit as on -  {$this->data['date']}";
        }
        elseif($this->view == 'rm_distant_checkin_checkout_report'){
            $subject .= "{$this->data['rm_name']} - Distant Check-in & Check-out Report for last week ({$this->data['start_date']} to {$this->data['end_date']})";
        }
        elseif($this->view == 'extract_national_id'){
            $subject .= "National ID extraction for {$this->data['biz_name']} - {$this->data['account_num']}";
        }
        elseif($this->view == 'cust_app_lockout'){
            $subject .= "[CUSTAPP] Customer App Authentication Failed for {$this->data['cust_name']} - {$this->data['mobile_num']}";
        }
        elseif($this->view == 'skip_txn_id_notification'){
            $subject .= "Skipped Txn ID check to capture {$this->data['txn_type']} for FA {$this->data['loan_doc_id']}";
        }
        elseif($this->view == 'reject_kyc_notification'){
            $subject .= "Lead {$this->data['biz_name']} - {$this->data['account_num']} was rejected by {$this->data['auditor_name']}";
        }
        elseif($this->view == 'redemption_notification'){
            $subject .= "Redemption Notification - Txn Date - {$this->data['stmt_txn_date']}";
        }
        elseif($this->view == 'unknown_txn_email'){
            $yesterday = Carbon::now()->yesterday()->toDateString();
            $subject .= "Unknown transactions for the date '$yesterday'";
        }
        elseif($this->view == "internal_integrity_warning"){
            $mismatch_type = $this->data['mismatch_type'];
            $subject .= ucfirst($mismatch_type)." Transaction Mismatch Found";         
        }
        elseif($this->view == 'duplicate_disbursal'){
            $subject .= "Duplicate Disbursal made for FA - {$this->data['loan_doc_id']}";
        }
        elseif($this->view == 'dup_disb_reversed'){
            $subject .= "Duplicate Disbursal Reversal Captured for FA - {$this->data['loan_doc_id']}";
        }
        elseif($this->view == 'profile_update_request'){
            $subject .= "[CUSTAPP] {$this->data['cust_name']} ({$this->data['mobile_num']}) has requested correction in KYC";
        }
        elseif($this->view == 'alt_acc_num_not_config'){
            $subject .= "[{$this->data['app']}]Alternate A/C Number has not been configured for {$this->data['cust_name']} ({$this->data['mobile_num']}) ";
        }
        elseif($this->view == 'missing_comms_notification'){
            $subject .= "Missing Commission while reassessing {$this->data['acc_prvdr_code']} customers";
        }
        elseif($this->view == 'product_limit_adjustment_notification'){
            $subject .= "Product Limit Adjustment while reassessing {$this->data['acc_prvdr_code']} customers";
        }
        elseif($this->view == 'notify_balance_below_threshold'){
            $subject .= "IMMEDIATE ACTION NEEDED - RUNNING OUT OF BALANCE FOR {$this->data['acc_prvdr_code']} A/C # {$this->data['acc_number']}";
        }
        elseif($this->view == 'notify_balance_above_threshold'){
            $subject .= "IMMEDIATE ACTION NEEDED - EXCESS BALANCE FOR {$this->data['acc_prvdr_code']} A/C # {$this->data['acc_number']}";
        }
        elseif($this->view == 'waiver_request_notification'){
            $subject = "Waiver Request for FA - {$this->data['loan_doc_id']}";
        }
        elseif($this->view == 'fa_upgrade_request'){
            $subject = "Need action - {$this->data['biz_name']}({$this->data['mobile_num']}) has requested for FA upgrade";
        }elseif($this->view == 'tp_ac_owner_notification'){
            $subject .= "Third Party A/C Owner Details added for Customer - {$this->data['cust_name']} - {$this->data['acc_number']}";
        }
        elseif($this->view == 'rm_target_not_assigned'){
            $subject .= "NEED ACTION - RM Targets is not Assigned";
        }
        elseif($this->view == 'inbound_call_failed_alert'){
            $subject .= "[NEED ACTION] Inbound call failed alert {$this->data['reason']}";
        }
        elseif($this->view == 'inbound_missed_call_alert'){
            $subject .= "[NEED ACTION] Missed Call from {$this->data['mobile_num']} ({$this->data['cust_id']})";
        }

        elseif($this->view == 'reconciliation_failure'){
            $subject .= "Reconciliation Failed for {$this->data['acc_prvdr_code']} {$this->data['acc_number']}";;
        }

        elseif($this->view == 'fa_repeat_queue'){
            $subject = "FA repeat queue failure - {$this->data['cust_id']} ({$this->data['mobile_num']})";
        }
        elseif($this->view == 'missing_translation'){
            $subject = "Missing translation of language for {$this->data['country_code']}";
        }
        elseif($this->view == 'etl_failure_notification') {
            $mail_configs = [
                'etl_api' => [
                    'subject' => 'API',
                    'action' => 'invoking API'
                ],
                'etl_process' => [
                    'subject' => 'Process',
                    'action' => 'performing ETL'
                ],
                'etl_db' => [
                    'subject' => 'DB',
                    'action' => 'inserting data to DB'
                ],
            ];
            $notify_type = $this->data['notify_type'];
            $this->data['mail_config'] = $mail_configs[$notify_type];
            
            $subject .= "[ETL] - {$this->data['mail_config']['subject']} Error on {$this->data['function_name']} for {$this->data['acc_number']}";
            if(isset($this->data['mail_attachments'])) {
                $this->attachments_for_etl($this->data['mail_attachments']);
            }
        }
        elseif($this->view == 'holder_name_evidence_verification'){
            $subject .= "Holder Name verification for Lead - {$this->data['national_id_name']}";
        }
        elseif($this->view  == 'expiring_agreements'){
            $subject = "Agreements Require Attention";
            $attachment = 'public/'.$this->data['filename'];
            $attachment_as = "Expiring Agreements.csv";
            $mime = 'application/csv';
        }elseif($this->view == 'account_name_mismatch_on_audit_submit'){
            if(isset($this->data['third_party_owner_name'])){
                $subject .= "Third Party Account Holder Name mismatch for Lead - {$this->data['biz_name']}";
            }
            else{
                $subject .= "Holder Name mismatch for Lead - {$this->data['biz_name']}";
            }
        }elseif($this->view == 'customer_complaint'){
            $subject .= "Complaint about {$this->data['complaint_type']} raised by {$this->data['cust_name']}" ;
        }
        
        elseif($this->view == 'acc_stmts_upload_email'){
            if($this->data['status'] == 'success'){
                if(isset($this->data['file_id'])){
                    $subject .= "{$this->data['acc_prvdr_code']} ({$this->data['acc_number']}) Account Statement Uploaded to Google Drive - {$this->data['month_year']}";
                }else{
                    $subject .= "{$this->data['acc_prvdr_code']} ({$this->data['acc_number']}) Account Statement Uploading Failed - {$this->data['month_year']}";
                }
            }else{
                $subject .= "{$this->data['acc_prvdr_code']} ({$this->data['acc_number']}) Account Statement Import Failed - {$this->data['month_year']}";
            }
        }
        elseif($this->view == 'customer_resolved_complaint'){
            $subject = "{$this->data['cust_name']}  has registered a complaint about {$this->data['complaint_type']}" ;
        }

        elseif($this->view == 'acc_stmts_delay_txn_email'){
            $subject .= " ({$this->data['date']}) {$this->data['acc_prvdr_code']} {$this->data['acc_number']} Transaction difference between Statement and Import time";
        }

        elseif($this->view == 'sms_import_failure'){
            $subject .= "Failed to process payment SMS from WALLET UTILITY APP for {$this->data['acc_num']}";
        }
        elseif($this->view == 'new_ussd_response_alert'){
            $subject .= "New USSD response for {$this->data['agent_id']}";
        }
        elseif($this->view == 'manual_capture_txns'){
            $subject .= "Manual Captured Txns - {$this->data['start_date']} to {$this->data['end_date']}";
        }elseif($this->view == 'pending_mobile_num_ver'){
            $subject .= "Leads With Pending Mobile Number Verification - {$this->data['assigned_date']}";
        }elseif($this->view == 'email_insufficient_balance'){
            $subject .= "{$this->data['acc_prvdr_code']} - Insufficient Balance";
        }
        
        if($attachment) {
           
            return $this->view("emails.{$this->view}")->subject($subject)->with(['message' => $this])->attach($attachment,['as' =>$attachment_as,"mime" =>$mime]);  
      
        }
        else{
        return $this->view("emails.{$this->view}")->subject($subject)->with(['message' => $this]);
        // return $this->view("emails.{$this->view}")->subject($subject);
         }



    }
}
