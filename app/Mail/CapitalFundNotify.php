<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class CapitalFundNotify extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $state)
    {
        $this->country_code = $data['country_code'];
        $this->dp_cust_id = $data['dp_cust_id'];
        $this->flow_rel_mgr_name = $data['flow_rel_mgr_name'];
        $this->aggr_doc_id = $data['aggr_doc_id'];
        $this->cust_name = $data['cust_name'];
        $this->valid_upto = $data['valid_upto'];
        $this->to_email_id = $data['to_email_id'];
        $this->state = $state;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->state == "expiring"){
            $this->view('emails.expiring_agrmts')
                 ->with(["flow_rel_mgr_name" => $this->flow_rel_mgr_name,"aggr_doc_id" => $this->aggr_doc_id, "cust_name" => $this->cust_name, "dp_cust_id" => $this->dp_cust_id,"valid_upto" => $this->valid_upto])
                 ->to($this->to_email_id)
                 ->subject("INFO - $this->country_code : AGREEMENT EXPIRING FOR CUSTOMER $this->dp_cust_id");
        }

        if($this->state == "expired"){
            $this->view('emails.expired_agrmts')
                 ->with(["flow_rel_mgr_name" => $this->flow_rel_mgr_name,"aggr_doc_id" => $this->aggr_doc_id, "cust_name" => $this->cust_name, "dp_cust_id" => $this->dp_cust_id,"valid_upto" => $this->valid_upto])
                 ->to($this->to_email_id)
                 ->subject("WARNING - $this->country_code : AGREEMENT EXPIRED FOR CUSTOMER $this->dp_cust_id");
        }
       
        
        

    }
}
