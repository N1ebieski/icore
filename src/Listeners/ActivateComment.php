<?php

namespace N1ebieski\ICore\Listeners;

/**
 * [ActivateComment description]
 */
class ActivateComment
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (auth()->user()->can('create comments')) {
            $event->comment->update(['status' => 1]);
        }
    }
}
