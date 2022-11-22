<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Mail\Mailing;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;
use Illuminate\Contracts\Translation\Translator as Lang;

class Mail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Undocumented function
     *
     * @param MailingEmail $mailingEmail
     * @param URL $url
     * @param Lang $lang
     */
    public function __construct(
        public MailingEmail $mailingEmail,
        protected URL $url,
        protected Lang $lang
    ) {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->mailingEmail->load(['mailing', 'morph']);

        return $this->subject($this->mailingEmail->mailing->title)
            ->to($this->mailingEmail->email)
            ->markdown('icore::mails.mailing')
            ->with(['subcopy' => $this->subcopy()]);
    }

    /**
     * [subcopy description]
     * @return string|null [description]
     */
    private function subcopy(): ?string
    {
        switch ($this->mailingEmail->model_type) {
            case \N1ebieski\ICore\Models\User::class:
                return $this->lang->get('icore::newsletter.subcopy.user', [
                    'cancel' => $this->url->route('web.profile.edit')
                ]);

            case \N1ebieski\ICore\Models\Newsletter::class:
                /** @var \N1ebieski\ICore\Models\Newsletter */
                $morph = $this->mailingEmail->morph;

                return $this->lang->get('icore::newsletter.subcopy.subscribe', [
                    'cancel' => $this->url->route('web.newsletter.update_status', [
                        $morph->id,
                        'token' => $morph->token->token,
                        'status' => $morph->status::INACTIVE
                    ]),
                ]);

            default:
                return null;
        }
    }
}
