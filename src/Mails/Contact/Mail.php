<?php

namespace N1ebieski\ICore\Mails\Contact;

use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * [Mail description]
 */
class Mail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * [protected description]
     * @var array
     */
    protected $request;

    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->request->input('title'))
            ->from($this->request->input('email'))
            ->to(config('mail.from.address'))
            ->markdown('icore::mails.contact')
            ->with(['content' => $this->request->input('content')]);
    }
}
