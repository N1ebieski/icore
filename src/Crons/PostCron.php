<?php

namespace N1ebieski\ICore\Crons;

use N1ebieski\ICore\Models\Post;

/**
 * [PostCron description]
 */
class PostCron
{
    /**
     * [private description]
     * @var Post
     */
    private $post;

    /**
     * [__construct description]
     * @param Post $post [description]
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        $this->publicateScheduled();
    }

    /**
     * Activates all scheduled posts with a date earlier than now()
     *
     * @return int [description]
     */
    private function publicateScheduled() : int
    {
        return $this->post->getRepo()->updateScheduled(['status' => 1]);
    }
}
