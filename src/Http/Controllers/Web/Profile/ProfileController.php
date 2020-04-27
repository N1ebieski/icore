<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Profile;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use N1ebieski\ICore\Http\Requests\Web\Profile\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Web\Profile\UpdateEmailRequest;

/**
 * [ProfileController description]
 */
class ProfileController
{
    use SendsPasswordResetEmails;

    /**
     * Show the form for editing the specified Auth User Profile.
     *
     * @return HttpResponse
     */
    public function edit() : HttpResponse
    {
        return Response::view('icore::web.profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Show the form for editing Socialite Symlinks the specified Auth User Profile.
     *
     * @return HttpResponse
     */
    public function editSocialite() : HttpResponse
    {
        return Response::view('icore::web.profile.edit_socialite', [
            'user' => Auth::user()->load('socialites')
        ]);
    }

    /**
     * Send Reset Password Link, then Logout User
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirectPassword(Request $request) : RedirectResponse
    {
        $request->request->add(['email' => Auth::user()->email]);

        $this->sendResetLinkEmail($request);

        Auth::logout();

        return Response::redirectToRoute('login')->with(
            'success',
            Lang::get('passwords.sent')
        );
    }

    /**
     * Update Email the specified Auth User Profile in storage.
     *
     * @param  UpdateEmailRequest $request [description]
     * @return RedirectResponse            [description]
     */
    public function updateEmail(UpdateEmailRequest $request) : RedirectResponse
    {
        Auth::user()->update([
            'email' => $request->get('email'),
            'email_verified_at' => null
        ]);

        Auth::user()->sendEmailVerificationNotification();

        return Response::redirectToRoute('web.profile.edit')->with(
            'success',
            Lang::get('icore::profile.success.update_email')
        );
    }

    /**
     * Update the specified Auth User Profile in storage.
     *
     * @param  UpdateRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request) : RedirectResponse
    {
        Auth::user()->update([
            'name' => $request->input('name'),
            'marketing' => $request->input('marketing_agreement')
        ]);

        return Response::redirectToRoute('web.profile.edit')->with(
            'success',
            Lang::get('icore::profile.success.update')
        );
    }
}
