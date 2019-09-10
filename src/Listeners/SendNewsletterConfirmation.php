<?php

namespace N1ebieski\ICore\Listeners;

use N1ebieski\ICore\Events\NewsletterStore;
use N1ebieski\ICore\Mail\NewsletterConfirmation;
use Illuminate\Support\Facades\Mail;

/**
 * [SuccessfulNewsletterStore description]
 */
class SendNewsletterConfirmation
{
    /**
     * Handle the event.
     *
     * @param  NewsletterStore  $event
     * @return void
     */
    public function handle(NewsletterStore $event) : void
    {
        Mail::send(app()->makeWith(NewsletterConfirmation::class, ['newsletter' => $event->newsletter]));
    }
}
