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

namespace N1ebieski\ICore\Tests\Feature\Web\Profile\Socialite;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Socialite as Social;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Testing\Socialite\Traits\HasSocialite;

class CallbackSocialiteTest extends TestCase
{
    use DatabaseTransactions;
    use HasSocialite;

    /**
     * @dataProvider providerProvider
     */
    public function testCallbackAsGuest(string $provider): void
    {
        $response = $this->get(route('web.profile.socialite.callback', ['provider' => $provider]));

        $response->assertRedirect(route('login'));
    }

    /**
     * @dataProvider providerProvider
     */
    public function testCallbackWithoutEmail(string $provider): void
    {
        /** @var User $user */
        $user = User::makeFactory()->user()->create();

        $this->createSocialiteMock([
            'id' => '3423423424',
            'email' => '',
            'name' => 'Bungo Bugosław'
        ], $provider);

        Auth::login($user);

        $response = $this->followingRedirects()->get(route('web.profile.socialite.callback', [
            'provider' => $provider
        ]));

        /** @var Social */
        $socialite = $user->socialites->first();

        $response->assertViewIs('icore::web.profile.socialites');
        $response->assertSee('action="' . route('web.profile.socialite.destroy', ['socialite' => $socialite->id]) . '"', false);
        $response->assertSee('alert-success', false);
    }

    /**
     * @dataProvider providerProvider
     */
    public function testCallbackForeignProviderId(string $provider): void
    {
        /** @var User */
        $user1 = User::makeFactory()->user()->create();

        /** @var Social */
        $social = Social::makeFactory()->for($user1)->create([
            'provider_name' => $provider
        ]);

        /** @var User */
        $user2 = User::makeFactory()->create();

        $this->createSocialiteMock([
            'id' => $social->provider_id,
            'email' => '',
            'name' => 'Bungo Bugosław'
        ], $provider);

        Auth::login($user2);

        $response = $this->get(route('web.profile.socialite.callback', ['provider' => $provider]));

        $response->assertRedirect(route('web.profile.socialites'));
        $response->assertSessionHas('danger');
    }
}
