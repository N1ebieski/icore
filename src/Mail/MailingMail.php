<?php

namespace N1ebieski\ICore\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\ICore\Models\MailingEmail;

/**
 * [MailingMail description]
 */
class MailingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * [public description]
     * @var MailingEmail
     */
    public $email;

    /**
     * Create a new event instance.
     *
     * @param MailingEmail $email
     * @return void
     */
    public function __construct(MailingEmail $email)
    {
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->email->mailing->title)
            ->from(config('mail.from.address'))
            ->to($this->email->email)
            ->markdown('icore::mails.mailing')
            ->with([
                'slot' => $this->email->mailing->content_html,
                'subcopy' => $this->subcopy(),
            ]);
    }

    /**
     * [subcopy description]
     * @return string|null [description]
     */
    private function subcopy() : ?string
    {
        switch ($this->email->model_type) {
            case 'N1ebieski\ICore\\Models\\User' :
                return null;

            case 'N1ebieski\ICore\\Models\\Newsletter' :
                $morph = $this->email->load('morph');
                return trans('icore::newsletter.subcopy.subscribe', [
                    'cancel' => route('web.newsletter.update_status', [
                        $morph->model->id,
                        'token' => $morph->model->token,
                        'status' => 0
                    ]),
                ]);

            case null :
                return null;
        }

        return null;
    }
}
