<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NonDeliveryReceiptEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $tries = 1;
    public $reason;
    public $status;
    public $is_otp;
    public $cust_name;
    public $mobile_num;
    public function __construct($mobile_num, $status, $cust_name, $reason, $country_code, $is_otp = false)
    {
        $this->mobile_num = $mobile_num;
        $this->reason = $reason;
        $this->status = $status;
        $this->cust_name = $cust_name;
        $this->is_otp = $is_otp;
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
        $subject = "[{$this->country_code}{$env}] - ";
        if($this->is_otp){
            $subject .= "[OTP] NON DELIVERY RECEIPT : RESEND CONFIRM CODE/OTP to {$this->cust_name}";
        }
        else{
            $subject .= "[NOTIFY SMS] NON DELIVERY RECEIPT : NOTIFICATION SMS to {$this->cust_name} FAILED";
        }
        return $this->view('emails.non_delivery_receipt')
                    ->subject($subject);
    }
}
