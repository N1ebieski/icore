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
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Socialite as Social;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Testing\Socialite\Traits\HasSocialite;

class DeleteSocialiteTest extends TestCase
{
    use DatabaseTransactions;
    use HasSocialite;

    public function testDestroyAsGuest(): void
    {
        $response = $this->delete(route('web.profile.socialite.destroy', ['socialite' => 4]));

        $response->assertRedirect(route('login'));
    }

    public function testDestroyWithInvalidId(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->delete(route('web.profile.socialite.destroy', ['socialite' => 442342424]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    /**
     * @dataProvider providerProvider
     */
    public function testDestroyForeignId(string $provider): void
    {
        /** @var User */
        $user1 = User::makeFactory()->user()->create();

        /** @var Social */
        $socialite = Social::makeFactory()->for($user1)->create([
            'provider_name' => $provider
        ]);

        /** @var User */
        $user2 = User::makeFactory()->create();

        Auth::login($user2);

        $response = $this->delete(route('web.profile.socialite.destroy', ['socialite' => $socialite->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider providerProvider
     */
    public function testDestroy(string $provider): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Social */
        $socialite = Social::makeFactory()->for($user)->create([
            'provider_name' => $provider
        ]);

        Auth::login($user);

        $response = $this->followingRedirects()->delete(route('web.profile.socialite.destroy', ['socialite' => $socialite->id]));

        $response->assertSee(route('web.profile.socialite.redirect', ['provider' => $provider]), false);
        $response->assertViewIs('icore::web.profile.socialites');
    }
}
