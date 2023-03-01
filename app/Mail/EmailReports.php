<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class EmailReports extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($due_report, $overdue_report)
    {
        $this->due_filename = $due_report[0];
        $this->overdue_filename = $overdue_report[0];

        $this->due_count = $due_report[1];
        $this->overdue_count = $overdue_report[1];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('emails.daily_report')
                      ->with(["due_filename" => $this->due_filename,"overdue_filename" => $this->overdue_filename, "due_count" => $this->due_count,"overdue_count" => $this->overdue_count])
                      ->to(env('MAIL_USERNAME'))
                      ->subject("FLOW Daily Report ". date_ui() ." - Due & Overdue");
       
        $attachments = [flow_report_path()."/$this->due_filename", flow_report_path()."/$this->overdue_filename"];
        foreach($attachments as $attachment){
            $email->attach($attachment);
        }
        return $email;

    }
}
