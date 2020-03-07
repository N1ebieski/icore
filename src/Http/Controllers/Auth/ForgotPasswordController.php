<?php

namespace N1ebieski\ICore\Http\Controllers\Auth;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

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
     * @return HttpResponse
     */
    public function showLinkRequestForm() : HttpResponse
    {
        return Response::view('icore::auth.passwords.email');
    }
}
