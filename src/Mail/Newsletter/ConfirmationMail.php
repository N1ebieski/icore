<?php

namespace N1ebieski\ICore\Mail\Newsletter;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\ICore\Models\Newsletter;
use N1ebieski\ICore\ValueObjects\Newsletter\Status;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Translation\Translator as Lang;

class ConfirmationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * [protected description]
     * @var Newsletter
     */
    protected $newsletter;

    /**
     * Undocumented variable
     *
     * @var URL
     */
    protected $url;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented function
     *
     * @param Newsletter $newsletter
     * @param URL $url
     * @param Lang $lang
     */
    public function __construct(
        Newsletter $newsletter,
        URL $url,
        Lang $lang
    ) {
        $this->newsletter = $newsletter;

        $this->url = $url;
        $this->lang = $lang;
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build(): self
    {
        return $this->subject($this->lang->get('icore::newsletter.subscribe_confirm'))
            ->to($this->newsletter->email)
            ->markdown('icore::mails.newsletter_confirmation')
            ->with([
                'actionUrl' => $this->url->route('web.newsletter.update_status', [
                    $this->newsletter->id,
                    'token' => $this->newsletter->token->token,
                    'status' => Status::ACTIVE
                ]),
                'actionText' => $this->lang->get('icore::newsletter.subscribe_confirm')
            ]);
    }
}
