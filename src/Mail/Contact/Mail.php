<?php

namespace N1ebieski\ICore\Mail\Contact;

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

        $this->email = config('mail.from.address');
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
            ->to($this->email)
            ->markdown('icore::mails.contact')
            ->with([
                'subcopy' => $this->subcopy(),
                'content' => $this->request->input('content')
            ]);
    }

    /**
     * [subcopy description]
     * @return string [description]
     */
    protected function subcopy() : string
    {
        return trans('icore::contact.subcopy.form', [
            'url' => route('web.contact.show')
        ]);
    }
}
