<?php

namespace N1ebieski\ICore\Events\Web\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use N1ebieski\ICore\Events\Inerfaces\PostEventInterface;

class ShowEvent implements PostEventInterface
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Post         $post    [description]
     * @return void
     */
    public function __construct(public Post $post)
    {
        //
    }
}
