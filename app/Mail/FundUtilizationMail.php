<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FundUtilizationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $funds;
    public $tries = 1;

    public function __construct($funds, $country_code)
    {
       $this->funds = $funds;
       $this->country_code = $country_code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date = Carbon::now();
        $env = get_env();
        return $this->view('emails.fund_utilization')
                    ->subject("[{$this->country_code}{$env}] FUND UTILIZATION REPORT - {$date->format('d M Y')}");
    }
}
