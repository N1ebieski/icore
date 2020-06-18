<?php

namespace N1ebieski\ICore\View\ViewModels;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Spatie\ViewModels\ViewModel;

class ValidViewModel extends ViewModel
{
    /**
     * [private description]
     * @var Session
     */
    protected $session;

    /**
     * [private description]
     * @var Request
     */
    protected $request;

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
            } else {
                if (array_key_exists($name, $this->request->session()->getOldInput())) {
                    return 'is-valid';
                }
            }
        }

        return null;
    }
}
