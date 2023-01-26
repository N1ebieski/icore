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

namespace N1ebieski\ICore\Listeners\Newsletter;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\ICore\Mail\Newsletter\ConfirmationMail;
use N1ebieski\ICore\Events\Interfaces\Newsletter\NewsletterEventInterface;

class SendConfirmation
{
    /**
     * Undocumented function
     *
     * @param Mailer $mailer
     * @param App $app
     */
    public function __construct(protected Mailer $mailer, protected App $app)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewsletterEventInterface $event
     * @return void
     */
    public function handle(NewsletterEventInterface $event): void
    {
        $this->mailer->send($this->app->make(ConfirmationMail::class, [
            'newsletter' => $event->newsletter
        ]));
    }
}
