<?php

namespace N1ebieski\ICore\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
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

    /**
     * Undocumented function
     *
     * @return HttpResponse
     */
    public function showLoginForm(): HttpResponse
    {
        return Response::view('icore::auth.login');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function redirectTo(): string
    {
        return Request::input('redirect') ?? $this->redirectTo;
    }
}
