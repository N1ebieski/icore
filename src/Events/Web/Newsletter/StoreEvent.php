<?php

namespace N1ebieski\ICore\Events\Web\Newsletter;

use Illuminate\Queue\SerializesModels;
use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class StoreEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

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
