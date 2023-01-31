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

namespace N1ebieski\ICore\Tests\Feature\Web\Profile;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Socialite as Social;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Testing\Socialite\Traits\HasSocialite;

class SocialitesProfileTest extends TestCase
{
    use DatabaseTransactions;
    use HasSocialite;

    public function testSocialitesAsGuest(): void
    {
        $response = $this->get(route('web.profile.socialites'));

        $response->assertRedirect(route('login'));
    }

    /**
     * @dataProvider providerProvider
     */
    public function testSocialites(string $provider): void
    {
        /** @var User $user */
        $user = User::makeFactory()->user()->create();

        /** @var Social */
        $socialite = Social::makeFactory()->for($user)->create([
            'provider_name' => $provider
        ]);

        Auth::login($user);

        $response = $this->get(route('web.profile.socialites'));

        $response->assertDontSee('href="' . route('web.profile.socialite.redirect', ['provider' => $provider]) . '"', false);
        $response->assertSee('action="' . route('web.profile.socialite.destroy', ['socialite' => $socialite->id]) . '"', false);
    }
}
