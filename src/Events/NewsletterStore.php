<?php

namespace N1ebieski\ICore\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use N1ebieski\ICore\Models\Newsletter;

/**
 * [NewsletterStore description]
 */
class NewsletterStore
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * [public description]
     * @var Newsletter
     */
    public $newsletter;

    /**
     * Create a new event instance.
     *
     * @param Newsletter $newsletter
     * @return void
     */
    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }
}
