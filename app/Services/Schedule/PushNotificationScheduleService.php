<?php
namespace App\Services\Schedule;
use App\Models\FlowApp\AppUser;
use App\Services\Support\FireBaseService;
use Illuminate\Support\Facades\Log;
use App\Services\Mobile\RMService;
use App\Mail\FlowCustomMail;
use Illuminate\Support\Facades\Mail;
class PushNotificationScheduleService{

    public function send_visit_suggestions_notification(){
       
        $country_code = session('country_code');
        $app_users = AppUser::where('country_code', $country_code)->where('role_codes', 'relationship_manager')->where('status', 'enabled')->get(['messenger_token','person_id','email']);

        foreach($app_users as $app_user){	
                
            try{
                $msgr_token = $app_user->messenger_token;
                $serv = new FireBaseService();
                $data['notify_type'] = 'visit_suggestions';
                $serv($data, $msgr_token, false);
                $this->send_email_notification($app_user->person_id, $app_user->email);

            }catch(\Exception $e){
                $exp_msg = $e->getMessage();
                $trace = $e->getTraceAsString();
                Log::error($exp_msg);
                Log::error($trace);
            }
        
        }
    }


    private function send_email_notification($flow_rel_mgr_id, $email){
        
        $rm_serv = new RMService;

        $data['cust_needs_visit'] = 'true';
        $data['flow_rel_mgr_id'] = $flow_rel_mgr_id;
        $borrowers = $rm_serv->borrower_search($data);
        $date = date_ui();
        $country_code = session('country_code');
        $mail_data = compact('country_code', 'borrowers', 'date');
		    Mail::to($email)->queue((new FlowCustomMail('visit_suggestions', $mail_data))->onQueue('emails'));

   }
    public function send_active_cust_list_notification(){
       
        $country_code = session('country_code');
        $app_users = AppUser::where('country_code', $country_code)->where('role_codes', 'relationship_manager')->where('status', 'enabled')->get('messenger_token');

        foreach($app_users as $app_user){	
                
            try{
                $msgr_token = $app_user->messenger_token;
                $serv = new FireBaseService();
                $data['notify_type'] = 'active_cust_wo_fa';
                $serv($data, $msgr_token, false);

            }catch(\Exception $e){
                $exp_msg = $e->getMessage();
                $trace = $e->getTraceAsString();
                Log::error($exp_msg);
                Log::error($trace);
            }
        
        }
    }
}
