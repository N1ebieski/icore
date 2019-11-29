<?php

namespace N1ebieski\ICore\Http\Controllers\Auth;

use N1ebieski\ICore\Models\Socialite as Social;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;

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
        config(['services.' . $provider . '.redirect' =>
            route('auth.socialite.callback', [
                'provider' => $provider,
            ])
        ]);
    }

    /**
     * Redirect the user to the SocialMedia authentication page.
     *
     * @param string $provider
     * @return RedirectResponse}Socialite
     */
    public function redirect(string $provider)
    {
        $this->configRedirect($provider);

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login');
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
            return redirect()->route('login');
        }

        try {
            $authUser = $socialite->makeService()->findOrCreateUser($providerUser, $provider);
        } catch (\N1ebieski\ICore\Exceptions\Socialite\NoEmailException $e) {
            return redirect()->route('register')
                ->with('warning', trans('icore::auth.warning.no_email', ['provider' => ucfirst($provider)]));
        } catch (\N1ebieski\ICore\Exceptions\Socialite\EmailExistException $e) {
            return redirect()->route('login')
                ->with('warning', trans('icore::auth.warning.email_exist', ['provider' => ucfirst($provider)]));
        }

        auth()->login($authUser, true);

        return redirect()->route('web.home.index');
    }
}
