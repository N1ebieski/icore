<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * [EventServiceProvider description]
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \Illuminate\Auth\Events\Login::class => [
            \N1ebieski\ICore\Listeners\RefreshIp::class,
        ],
        \N1ebieski\ICore\Events\NewsletterStore::class => [
            \N1ebieski\ICore\Listeners\SendNewsletterConfirmation::class,
        ],
        \N1ebieski\ICore\Events\CommentStore::class => [
            \N1ebieski\ICore\Listeners\ActivateComment::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
