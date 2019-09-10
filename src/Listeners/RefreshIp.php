<?php

namespace N1ebieski\ICore\Listeners;

/**
 * [LogSuccessfulLogin description]
 */
class RefreshIp
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        auth()->user()->update([
            'ip' => request()->ip()
        ]);
    }
}
