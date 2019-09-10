<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Profile;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use N1ebieski\ICore\Http\Requests\Web\Profile\UpdateEmailRequest;
use N1ebieski\ICore\Http\Requests\Web\Profile\UpdateRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * [ProfileController description]
 */
class ProfileController
{
    use SendsPasswordResetEmails;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified Auth User Profile.
     *
     * @return View
     */
    public function edit() : View
    {
        return view('icore::web.profile.edit', [
            'user' => auth()->user()
        ]);
    }

    /**
     * Show the form for editing Socialite Symlinks the specified Auth User Profile.
     *
     * @return View
     */
    public function editSocialite() : View
    {
        return view('icore::web.profile.edit_socialite', [
            'user' => auth()->user()->load('socialites')
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
        $request->request->add(['email' => auth()->user()->email]);

        $this->sendResetLinkEmail($request);

        auth()->logout();

        return redirect()->route('login')->with('success', trans('icore::passwords.sent'));
    }

    /**
     * Update Email the specified Auth User Profile in storage.
     *
     * @param  UpdateEmailRequest $request [description]
     * @return RedirectResponse            [description]
     */
    public function updateEmail(UpdateEmailRequest $request) : RedirectResponse
    {
        auth()->user()->update([
            'email' => $request->get('email'),
            'email_verified_at' => null
        ]);

        auth()->user()->sendEmailVerificationNotification();

        return redirect()->route('web.profile.edit')->with('success', trans('icore::profile.success.update_email'));
    }

    /**
     * Update the specified Auth User Profile in storage.
     *
     * @param  UpdateRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request) : RedirectResponse
    {
        auth()->user()->update($request->only(['name']));

        return redirect()->route('web.profile.edit')->with('success', trans('icore::profile.success.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
