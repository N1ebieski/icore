<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Profile;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Laravel\Socialite\Facades\Socialite;
use N1ebieski\ICore\Models\Socialite as Social;

class SocialiteController
{
    /**
     * Hook replacing the provider in config
     *
     * @param string $provider [description]
     * @return void
     */
    private static function configRedirect(string $provider): void
    {
        // Troche partyzantka, ale musze tak zrobic bo Socialite dla Twittera nie daje
        // zdefiniowac innego callbacku przez metode redirectUrl
        Config::set(
            "services.{$provider}.redirect",
            URL::route('web.profile.socialite.callback', ['provider' => $provider])
        );
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
            return Response::redirectToRoute('web.profile.socialites');
        }
    }

    /**
     * Obtain the user information
     *
     * @param Social $socialite
     * @param string $provider
     * @return RedirectResponse
     */
    public function callback(Social $socialite, string $provider): RedirectResponse
    {
        $this->configRedirect($provider);

        try {
            $providerUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return Response::redirectToRoute('web.profile.socialites');
        }

        $socialiteService = $socialite->makeService();
        $authUser = $socialiteService->findUser($providerUser, $provider);

        if (!is_null($authUser)) {
            return Response::redirectToRoute('web.profile.socialites')->with(
                'danger',
                Lang::get('icore::profile.error.symlink_exist', ['provider' => ucfirst($provider)])
            );
        }

        $socialiteService->setSocialiteUser(Auth::user())->create([]);

        return Response::redirectToRoute('web.profile.socialites')->with(
            'success',
            Lang::get('icore::profile.success.symlink_create', ['provider' => ucfirst($provider)])
        );
    }

    /**
     * [destroy description]
     * @param  Social $socialite [description]
     * @return RedirectResponse  [description]
     */
    public function destroy(Social $socialite): RedirectResponse
    {
        $socialite->delete();

        return Response::redirectToRoute('web.profile.socialites')->with(
            'success',
            Lang::get('icore::profile.success.symlink_delete')
        );
    }
}
