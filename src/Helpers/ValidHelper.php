<?php

namespace N1ebieski\ICore\Helpers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

/**
 * [ThemeHelper description]
 */
class ValidHelper
{
    /**
     * [private description]
     * @var Session
     */
    private $session;

    /**
     * [private description]
     * @var Request
     */
    private $request;

    /**
     * @param Session $session
     * @param Request $request
     */
    public function __construct(Session $session, Request $request)
    {
        $this->session = $session;
        $this->request = $request;
    }


    /**
     * [isValid description]
     * @param  string  $name [description]
     * @return string|null       [description]
     */
    public function isValid(string $name) : ?string
    {
        if ($this->session->has('errors')) {
            if ($this->session->get('errors')->has($name)) {
                return 'is-invalid';
            }
            else {
                if ($this->request->old($name) !== null) {
                    return 'is-valid';
                }
            }
        }

        return null;
    }
}
