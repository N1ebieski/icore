<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Http\Controllers\Web\Profile;

use Illuminate\Http\Request;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\User\Marketing;
use N1ebieski\ICore\Filters\Web\Profile\TokensFilter;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use N1ebieski\ICore\Http\Requests\Web\Profile\TokensRequest;
use N1ebieski\ICore\Http\Requests\Web\Profile\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Web\Profile\UpdateEmailRequest;

class ProfileController
{
    use SendsPasswordResetEmails;

    /**
     * Show the form for editing the specified Auth User Profile.
     *
     * @return HttpResponse
     */
    public function edit(): HttpResponse
    {
        Config::set('jsvalidation.focus_on_error', false);

        return Response::view('icore::web.profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Show the form for editing Socialite Symlinks the specified Auth User Profile.
     *
     * @return HttpResponse
     */
    public function socialites(): HttpResponse
    {
        return Response::view('icore::web.profile.socialites', [
            'user' => Auth::user()?->load('socialites')
        ]);
    }

    /**
     * Send Reset Password Link, then Logout User
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirectPassword(Request $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();

        $request->merge(['email' => $user->email]);

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
    public function updateEmail(UpdateEmailRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();

        $user->update([
            'email' => $request->get('email'),
            'email_verified_at' => null
        ]);

        $user->sendEmailVerificationNotification();

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
    public function update(UpdateRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();

        $user->makeService()->update($request->validated());

        return Response::redirectToRoute('web.profile.edit')->with(
            'success',
            Lang::get('icore::profile.success.update')
        );
    }

    /**
     * Undocumented function
     *
     * @param TokensRequest $request
     * @param TokensFilter $filter
     * @return HttpResponse
     */
    public function tokens(TokensRequest $request, TokensFilter $filter): HttpResponse
    {
        /** @var \N1ebieski\ICore\Models\User */
        $user = $request->user();

        return Response::view('icore::web.profile.tokens', [
            'filter' => $filter->all(),
            'tokens' => $user->makeRepo()->paginateTokensByFilter($filter->all()),
            'paginate' => Config::get('database.paginate')
        ]);
    }
}
