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
     * Undocumented function
     *
     * @param Newsletter $newsletter
     * @param URL $url
     * @param Lang $lang
     */
    public function __construct(
        protected Newsletter $newsletter,
        protected URL $url,
        protected Lang $lang
    ) {
        //
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
