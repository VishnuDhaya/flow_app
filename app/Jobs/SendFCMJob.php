<?php

namespace App\Jobs;

use App\Services\Support\FireBaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendFCMJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $data;
    public $messenger_token;
    public $addl_data;
    public $country_code;
    public $tries = 1;
    public function __construct($data, $messenger_token, $addl_data)
    {
        $this->country_code = $data['country_code'];
        unset($data['country_code']);
        $this->data = $data;
        $this->messenger_token = $messenger_token;
        $this->addl_data = $addl_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_app_session($this->country_code);
        $fcm = new FireBaseService();
        try{
            $fcm->send_message($this->data, $this->messenger_token, $this->addl_data);
        }
        catch(\Exception $e){
            if(isset($this->data['notify_type'])){
                $recipient = DB::selectOne("select person_id from app_users where messenger_token = ? and status = 'enabled'", [$this->messenger_token]);
                if($recipient){
                send_notification_failed_mail($e, $recipient->person_id, $this->data['notify_type']);
                }
            }
		}
    }
}
