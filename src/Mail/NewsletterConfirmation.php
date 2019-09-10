<?php

namespace N1ebieski\ICore\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\ICore\Models\Newsletter;

/**
 * [NewsletterConfirmation description]
 */
class NewsletterConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * [public description]
     * @var Newsletter
     */
    public $newsletter;

    /**
     * Create a new event instance.
     *
     * @param Newsletter $newsletter
     * @return void
     */
    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build() : self
    {
        return $this->subject(trans('icore::newsletter.subscribe_confirm'))
            ->from(config('mail.from.address'))
            ->to($this->newsletter->email)
            ->markdown('icore::mails.newsletter_confirmation')
            ->with([
                'actionUrl' => route('web.newsletter.update_status', [
                    $this->newsletter->id,
                    'token' => $this->newsletter->token,
                    'status' => 1
                ]),
                'actionText' => trans('icore::newsletter.subscribe_confirm')
            ]);
    }
}
