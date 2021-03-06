<?php

namespace N1ebieski\ICore\Events\Admin\Comment;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use N1ebieski\ICore\Models\Comment\Comment;

/**
 * [Store description]
 */
class StoreEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * [public description]
     * @var Comment
     */
    public $comment;

    /**
     * Create a new event instance.
     *
     * @param Comment $comment
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }
}
