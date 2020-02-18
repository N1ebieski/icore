<?php

namespace N1ebieski\ICore\Listeners\Newsletter;

use N1ebieski\ICore\Mail\Newsletter\Confirmation;
use Illuminate\Support\Facades\Mail;

/**
 * [SendConfirmation description]
 */
class SendConfirmation
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event) : void
    {
        Mail::send(app()->makeWith(Confirmation::class, ['newsletter' => $event->newsletter]));
    }
}
