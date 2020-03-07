<?php

namespace N1ebieski\ICore\Http\Controllers\Auth;

use N1ebieski\ICore\Models\Socialite as Social;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;

/**
 * [SocialiteController description]
 */
class SocialiteController
{
    /**
     * Hook replacing the provider in config
     *
     * @param string $provider [description]
     */
    private static function configRedirect(string $provider) : void
    {
        // Troche partyzantka, ale musze tak zrobic bo Socialite dla Twittera nie daje
        // zdefiniowac innego callbacku przez metode redirectUrl
        Config::set(
            "services.{$provider}.redirect",
            URL::route('auth.socialite.callback', [
                'provider' => $provider,
            ])
        );
    }

    /**
     * Redirect the user to the SocialMedia authentication page.
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function redirect(string $provider) : RedirectResponse
    {
        $this->configRedirect($provider);

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return Response::redirectToRoute('login');
        }
    }

    /**
     * Obtain the user information
     *
     * @param Social $socialite
     * @param string $provider
     * @return RedirectResponse
     */
    public function callback(Social $socialite, string $provider) : RedirectResponse
    {
        $this->configRedirect($provider);

        try {
            $providerUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return Response::redirectToRoute('login');
        }

        try {
            $authUser = $socialite->makeService()->findOrCreateUser($providerUser, $provider);
        } catch (\N1ebieski\ICore\Exceptions\Socialite\NoEmailException $e) {
            return Response::redirectToRoute('register')->with(
                'warning',
                Lang::get('icore::auth.warning.no_email', ['provider' => ucfirst($provider)])
            );
        } catch (\N1ebieski\ICore\Exceptions\Socialite\EmailExistException $e) {
            return Response::redirectToRoute('login')->with(
                'warning',
                Lang::get('icore::auth.warning.email_exist', ['provider' => ucfirst($provider)])
            );
        }

        Auth::login($authUser, true);

        return Response::redirectToRoute('web.home.index');
    }
}
