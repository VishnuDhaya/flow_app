<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
class EmailReviewInstantDisbursal extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $tries = 1;
    public $data;
    public $url;
    public $disb_attempt_info;
    public $loan;
    public $country_code;
    public function __construct($data, $disb_attempt_info, $loan, $country_code)
    {   
        $this->country_code = $country_code;
        $this->loan = $loan;
        $this->data = $data;
        $this->disb_attempt_info = $disb_attempt_info;
        $this->url = env('APP_ENV') == 'production' ? env('APP_URL') : env('APP_URL').":8000/";
    }


    public function build()
    {
        $country_code = $this->country_code;
        $env = get_env();
        return $this->view('emails.review_instant_disbursal')
                    ->subject("[$country_code$env] - STALLED FA ALERT : ".$this->loan->cust_name." -- LOAN ".$this->data['loan_doc_id']." --");
    }
}
