<?php

namespace N1ebieski\ICore\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->email = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->email->get('title'))
            ->from($this->email->get('email'))
            ->to(config('mail.from.address'))
            ->markdown('vendor.mail.html.message')
            ->with([
                'slot' => strip_tags($this->email->get('content')),
                'subcopy' => trans('icore::contact.subcopy.form', [
                    'url' => route('web.contact.index')
                ]),
            ]);
    }
}
