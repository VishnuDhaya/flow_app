<?php

namespace App\Jobs;

use App\Services\Support\SMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $recipient;
    public $message;
    public $isd_code;
    public $log_data;
    public $tries = 1;
    public function __construct($recipient, $message, $isd_code, $log_data)
    {
        $this->recipient = $recipient;
        $this->message = $message;
        $this->isd_code = $isd_code;
        $this->log_data = $log_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   $country_code = get_country_code_by_isd($this->isd_code);
        set_app_session($country_code);
        $sms = new SMSService();
        $sms->send_sms($this->recipient, $this->message, $this->isd_code, $this->log_data);
    }
}
