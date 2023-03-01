<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class EmailForceCheckout extends Mailable
{

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $tries = 1;
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
       
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      
          return  $this->view('emails.force_checkout')
                       ->subject("[".env('APP_ENV')."] - [Force Checkout] - {$this->data['user_name']} - {$this->data['cust_id']}");
        
    
    }
}
