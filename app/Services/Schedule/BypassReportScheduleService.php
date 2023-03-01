<?php
namespace App\Services\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Consts;
use Illuminate\Support\Facades\Mail;
use App\Mail\FlowCustomMail;




class BypassReportScheduleService{

    public function send_bypass_report_email(){

        $country_code = session('country_code');

        $start_date = Carbon::today()->subDays(30)->format(Consts::DB_DATE_FORMAT);
        
        $end_date = Carbon::today()->format(Consts::DB_DATE_FORMAT);


        $bypassed_cases =  DB::select("select *,sms_not_sent+sms_not_rcvd as bypassed_fas_count from(select group_concat(distinct l.cust_mobile_num) as mobile_num, l.cust_id, count(*) as total_disbursals, group_concat(l.loan_doc_id SEPARATOR ' | ') bypassed_fas,count(if((JSON_CONTAINS(call_purpose, JSON_ARRAY('cust_sms_not_rcvd'))),1,null)) as sms_not_rcvd, count(if((JSON_CONTAINS(call_purpose, JSON_ARRAY('cust_sms_not_sent'))),1,null)) as sms_not_sent from loans l,loan_txns lt,call_logs cl 
                            where l.loan_doc_id = lt.loan_doc_id and l.loan_doc_id = cl.loan_doc_id and date(txn_date) >= '{$start_date}' and date(txn_date) <= '{$end_date}' and txn_type='disbursal'  and l.country_code = '{$country_code}' and (JSON_CONTAINS(call_purpose, JSON_ARRAY('cust_sms_not_rcvd')) or JSON_CONTAINS(call_purpose,
                             JSON_ARRAY('cust_sms_not_sent')))  group by l.cust_id)a where sms_not_sent+sms_not_rcvd >= 3 order by bypassed_fas_count desc");

        if($bypassed_cases){
            $this->send_email_notification($bypassed_cases);        
        }
    }

    private function send_email_notification($bypassed_cases){

        $country_code = session('country_code');

        $start_date = Carbon::today()->subDays(30)->format(Consts::UI_DATE_FORMAT);
        
        $end_date = Carbon::today()->format(Consts::UI_DATE_FORMAT);

        $mail_data = compact('country_code', 'bypassed_cases', 'start_date', 'end_date');

		Mail::to([get_ops_admin_email(),get_l3_email(),get_csm_email(), config('app.app_support_email')])->queue((new FlowCustomMail('bypassed_fas_report', $mail_data))->onQueue('emails'));

    }

    public function send_weekly_bypass_report(){

        $country_code = session('country_code');
        $last_sunday = new Carbon("last sunday");
        
        $start_date = $last_sunday->copy()->subDays(7)->format(Consts::DB_DATE_FORMAT);
        $end_date = $last_sunday->format(Consts::DB_DATE_FORMAT);
       
        $total_sms_cases =  DB::select("select * from (select date(disbursal_date) date, count(*) as total_disbursal, count(if(cust_conf_channel = 'cust_otp' , 1, null)) as sms_success_cases from loans where date(disbursal_date) >= '{$start_date}' and date(disbursal_date) <= '{$end_date}' and country_code = '{$country_code}' group by date(disbursal_date)) as a, (select date(l.disbursal_date) as date, s.vendor_code, count(if((JSON_CONTAINS(call_purpose, JSON_ARRAY('cust_sms_not_rcvd'))), 1, null)) as sms_not_rcvd, count(if((JSON_CONTAINS(call_purpose, JSON_ARRAY('cust_sms_not_sent'))), 1, null)) as sms_not_sent from loans l,  call_logs cl, sms_logs s where  l.loan_doc_id = cl.loan_doc_id and s.loan_doc_id = cl.loan_doc_id and date(disbursal_date) >= '{$start_date}' and date(disbursal_date) <= '{$end_date}'  and l.country_code = '{$country_code}'  and s.purpose = 'otp/confirm_fa' group by date(l.disbursal_date),s.vendor_code) as b where a.date=b.date;");

       
        $end_date = (new Carbon("last sunday"))->format(Consts::UI_DATE_FORMAT);
        $start_date = (new Carbon("last sunday"))->subDays(7)->format(Consts::UI_DATE_FORMAT);

        $mail_data = compact('country_code', 'total_sms_cases', 'start_date', 'end_date');

        if($total_sms_cases){
            Mail::to([get_l3_email(),get_ops_admin_email()])->queue((new FlowCustomMail('weekly_sms_issues_report', $mail_data))->onQueue('emails'));
        }

    
    }

   
}