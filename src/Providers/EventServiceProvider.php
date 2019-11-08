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

        \N1ebieski\ICore\Models\Post::observe(\N1ebieski\ICore\Observers\PostObserver::class);

        \N1ebieski\ICore\Models\Category\Category::observe(\N1ebieski\ICore\Observers\CategoryObserver::class);
        \N1ebieski\ICore\Models\Category\Post\Category::observe(\N1ebieski\ICore\Observers\CategoryObserver::class);

        \N1ebieski\ICore\Models\Comment\Comment::observe(\N1ebieski\ICore\Observers\CommentObserver::class);
        \N1ebieski\ICore\Models\Comment\Post\Comment::observe(\N1ebieski\ICore\Observers\CommentObserver::class);
        \N1ebieski\ICore\Models\Comment\Page\Comment::observe(\N1ebieski\ICore\Observers\CommentObserver::class);

        \N1ebieski\ICore\Models\BanValue::observe(\N1ebieski\ICore\Observers\BanValueObserver::class);
        \N1ebieski\ICore\Models\Page\Page::observe(\N1ebieski\ICore\Observers\PageObserver::class);

        \N1ebieski\ICore\Models\Link::observe(\N1ebieski\ICore\Observers\LinkObserver::class);
    }
}
