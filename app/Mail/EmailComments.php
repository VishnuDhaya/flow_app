<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
class EmailComments extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($loan_comment)
    {
       $this->cmt_type = $loan_comment['cmt_type'];
       $this->loan_doc_id = $loan_comment['loan_doc_id'];
       $this->cmt_from_name = $loan_comment['cmt_from_name'];
       $this->comment = $loan_comment['comment'];
             
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('emails.comment')
                      ->with(["cmt_type" => $this->cmt_type,"loan_doc_id" => $this->loan_doc_id, "cmt_from_name" => $this->cmt_from_name,"comment" => $this->comment])
                      ->subject("Loan Comment");
         Log::warning("email sucesss");      
        return $email;
    }
}
