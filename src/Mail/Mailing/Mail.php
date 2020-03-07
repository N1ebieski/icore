<?php

namespace N1ebieski\ICore\Mail\Mailing;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\ICore\Models\Newsletter;
use N1ebieski\ICore\Models\MailingEmail;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Translation\Translator as Lang;

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
    public $mailingEmail;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

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
     * @param MailingEmail $mailingEmail
     * @param Config $config
     * @param URL $url
     * @param Lang $lang
     */
    public function __construct(
        MailingEmail $mailingEmail,
        Config $config,
        URL $url,
        Lang $lang
    ) {
        $this->mailingEmail = $mailingEmail;

        $this->config = $config;
        $this->url = $url;
        $this->lang = $lang;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->mailingEmail->load('mailing');

        return $this->subject($this->mailingEmail->mailing->title)
            ->from($this->config->get('mail.from.address'))
            ->to($this->mailingEmail->email)
            ->markdown('icore::mails.mailing')
            ->with(['subcopy' => $this->subcopy()]);
    }

    /**
     * [subcopy description]
     * @return string|null [description]
     */
    private function subcopy() : ?string
    {
        switch ($this->mailingEmail->model_type) {
            case 'N1ebieski\ICore\\Models\\User':
                return null;

            case 'N1ebieski\ICore\\Models\\Newsletter':
                $this->mailingEmail->load('morph');
                return $this->lang->get('icore::newsletter.subcopy.subscribe', [
                    'cancel' => $this->url->route('web.newsletter.update_status', [
                        $this->mailingEmail->morph->id,
                        'token' => $this->mailingEmail->morph->token,
                        'status' => Newsletter::INACTIVE
                    ]),
                ]);

            default:
                return null;
        }
    }
}
