<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class EmailUpdateGps extends Mailable
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
      
          return  $this->view('emails.update_gps_notify')
                       ->subject("[".env('APP_ENV')."] - [GPS Update] - {$this->data['user_name']} - {$this->data['cust_id']}");
        
    
    }
}
