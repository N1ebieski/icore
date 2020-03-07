<?php

namespace N1ebieski\ICore\Listeners\User;

use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\Request;

/**
 * [LogSuccessfulLogin description]
 */
class RefreshIp
{
    /**
     * Undocumented variable
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented variable
     *
     * @var Request
     */
    protected $request;

    /**
     * Undocumented function
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth, Request $request)
    {
        $this->auth = $auth;
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->auth->user()->update([
            'ip' => $this->request->ip()
        ]);
    }
}
