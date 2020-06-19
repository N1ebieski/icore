<?php

namespace N1ebieski\ICore\Listeners\Comment;

use Illuminate\Contracts\Auth\Guard as Auth;
use N1ebieski\ICore\Models\Comment\Comment;

/**
 * [Activate description]
 */
class Activate
{
    /**
     * Undocumented variable
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented function
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($this->auth->user()->can('web.comments.create')) {
            $event->comment->update(['status' => Comment::ACTIVE]);
        }
    }
}
