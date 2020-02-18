<?php

namespace N1ebieski\ICore\Mail\Mailing;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\ICore\Models\MailingEmail;

/**
 * [Mail description]
 */
class Mail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * [public description]
     * @var MailingEmail
     */
    protected $email;

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
        $this->email->load('mailing');

        return $this->subject($this->email->mailing->title)
            ->from(config('mail.from.address'))
            ->to($this->email->email)
            ->markdown('icore::mails.mailing')
            ->with([
                'email' => $this->email,
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
                $this->email->load('morph');
                return trans('icore::newsletter.subcopy.subscribe', [
                    'cancel' => route('web.newsletter.update_status', [
                        $this->email->morph->id,
                        'token' => $this->email->morph->token,
                        'status' => 0
                    ]),
                ]);

            case null :
                return null;
        }

        return null;
    }
}
