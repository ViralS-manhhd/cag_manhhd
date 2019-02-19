<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PassHolderTerminateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $passHolder;
    public $account;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($passHolder, $account)
    {
        $this->passHolder = $passHolder;
        $this->account = $account;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        app('logService')->logAction($this->account, null, $this->passHolder->toArray(), 'Pass Holder Terminate Mail');
        return $this->view('emails.terminate_pass_holder');
    }
}
