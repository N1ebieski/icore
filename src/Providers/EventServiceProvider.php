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
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        \Illuminate\Auth\Events\Login::class => [
            \N1ebieski\ICore\Listeners\User\RefreshIp::class,
        ],
        \N1ebieski\ICore\Events\Web\Newsletter\StoreEvent::class => [
            \N1ebieski\ICore\Listeners\Newsletter\SendConfirmation::class,
        ],
        \N1ebieski\ICore\Events\Web\Comment\StoreEvent::class => [
            \N1ebieski\ICore\Listeners\Comment\Activate::class
        ],
        \N1ebieski\ICore\Events\Web\Post\ShowEvent::class => [
            \N1ebieski\ICore\Listeners\Stat\Post\IncrementView::class
        ],
        \N1ebieski\ICore\Events\Web\Page\ShowEvent::class => [
            \N1ebieski\ICore\Listeners\Stat\Page\IncrementView::class
        ],
        \N1ebieski\ICore\Events\Admin\Comment\StoreEvent::class => [
            \N1ebieski\ICore\Listeners\Comment\Activate::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        \N1ebieski\ICore\Models\User::observe(\N1ebieski\ICore\Observers\UserObserver::class);

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
