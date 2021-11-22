<?php

namespace N1ebieski\ICore\Events\Web\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ShowEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * [public description]
     * @var Post
     */
    public $post;

    /**
     * Create a new event instance.
     *
     * @param Post         $post    [description]
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
