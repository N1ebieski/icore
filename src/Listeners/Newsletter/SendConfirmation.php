<?php

namespace N1ebieski\ICore\Listeners\Newsletter;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\ICore\Mail\Newsletter\ConfirmationMail;

class SendConfirmation
{
    /**
     * Undocumented variable
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param Mailer $mailer
     * @param App $app
     */
    public function __construct(Mailer $mailer, App $app)
    {
        $this->mailer = $mailer;
        $this->app = $app;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event): void
    {
        $this->mailer->send($this->app->make(ConfirmationMail::class, [
            'newsletter' => $event->newsletter
        ]));
    }
}
