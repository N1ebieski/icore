<?php

namespace N1ebieski\ICore\Http\Controllers\Auth;

use N1ebieski\ICore\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use N1ebieski\ICore\Http\Requests\Auth\Login\LoginRequest;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LoginRequest $request)
    {
        $this->middleware('icore.guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('icore::auth.login');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function redirectTo() : string
    {
        return request()->input('redirect') ?? $this->redirectTo;
    }
}
