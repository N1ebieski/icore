<?php

namespace N1ebieski\ICore\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('icore.guest');
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param string $token
     * @return HttpResponse
     */
    public function showResetForm(Request $request, string $token = null) : HttpResponse
    {
        return Response::view('icore::auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }
}
