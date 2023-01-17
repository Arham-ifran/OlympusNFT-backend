<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MasterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($to_email = '', $sender_name = '', $sender_email = '', $subject = '', $body_html, $cc = '', $bcc = '', $attachments = [])
    {

        if ($sender_email != '' && $sender_name != '') {
            $this->from($sender_email, $sender_name);
        }

        $to_replace = ['[SITE_NAME]', '[SITE_URL]', '[CONTACT_URL]'];
        $site = url('/');
        $contact = url('contact-us');
        $with_replace = [settingValue('site_name'), $site, $contact];
        $html_body = str_replace($to_replace, $with_replace, $body_html);

        $this->to($to_email);
        $this->subject($subject);
        if ($cc)
            $this->cc($cc);

        if ($bcc)
            $this->bcc($bcc);

        $mailContents = $html_body;

        $this->viewData = compact('mailContents');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email_templete.template');
    }
}
