<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Profile;

use Laravel\Socialite\Facades\Socialite;
use N1ebieski\ICore\Models\Socialite as Social;
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
    private static function configRedirect(string $provider)
    {
        // Troche partyzantka, ale musze tak zrobic bo Socialite dla Twittera nie daje
        // zdefiniowac innego callbacku przez metode redirectUrl
        config(['services.' . $provider . '.redirect' =>
            route('web.profile.socialite.callback', ['provider' => $provider])
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
            return redirect()->route('web.profile.edit_socialite');
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
            return redirect()->route('web.profile.edit_socialite');
        }

        $socialiteService = $socialite->getService();
        $authUser = $socialiteService->findUser($providerUser, $provider);

        if (!is_null($authUser)) return redirect()->route('web.profile.edit_socialite')
            ->with('danger', trans('icore::profile.error.symlinkExist', ['provider' => ucfirst($provider)]));

        $socialiteService->setSocialiteUser(auth()->user())->create();

        return redirect()->route('web.profile.edit_socialite')
            ->with('success', trans('icore::profile.success.symlink_create', ['provider' => ucfirst($provider)]));
    }

    /**
     * [destroy description]
     * @param  Social $socialite [description]
     * @return RedirectResponse  [description]
     */
    public function destroy(Social $socialite) : RedirectResponse
    {
        $socialite->delete();

        return redirect()->route('web.profile.edit_socialite')->with('success', trans('icore::profile.success.symlink_delete'));
    }
}
