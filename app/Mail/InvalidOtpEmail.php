<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;

class InvalidOtpEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $validity_status = null;
    public $cust;
    public $tries = 1;

    public function __construct($data, $cust, $validity_status, $country_code){
        $this->data = $data;
        $this->validity_status = $validity_status;
        $this->cust = $cust;
        $this->country_code = $country_code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $env = get_env();
        $subject = "[$this->country_code$env] - ";

        if($this->validity_status == 'invalid'){
            $subject .= "[OTP] SUPPORT NEEDED for {$this->cust->biz_name} to send Confirm Code/OTP";
        }
        else if($this->validity_status == 'expired'){
            $subject .= "[OTP] RESEND CONFIRM CODE/OTP to {$this->cust->biz_name}";
        }
        else if($this->validity_status == 'alt_num'){
            $subject .= "[SMS] ALTERNATE NUMBER used by {$this->cust->biz_name} to send SMS ";
        }
        else{
            $subject .= "[SMS] UNKNOWN SMS RECEIVED from {$this->cust->biz_name}";
        }
        return $this->view('emails.invalid_expired_otp')->subject($subject);
    }
}
