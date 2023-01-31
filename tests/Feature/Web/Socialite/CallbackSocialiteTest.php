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

namespace N1ebieski\ICore\Tests\Feature\Web\Socialite;

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

    public function testInvalidProvider(): void
    {
        $response = $this->get(route('auth.socialite.callback', ['invalid-provider']));

        $response->assertRedirect('/login');
    }

    /**
     * @dataProvider providerProvider
     */
    public function testCallbackAsLoggedUser(string $provider): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('auth.socialite.callback', [$provider]));

        $response->assertRedirect('/');
    }

    /**
     * @dataProvider providerProvider
     */
    public function testCallbackWithNothing(string $provider): void
    {
        $this->createSocialiteMock([
            'id' => '',
            'email' => '',
            'name' => ''
        ], $provider);

        $response = $this->get(route('auth.socialite.callback', ['provider' => $provider]));

        $response->assertRedirect('/register');
    }

    /**
     * @dataProvider providerProvider
     */
    public function testCallbackWithoutEmail(string $provider): void
    {
        $this->createSocialiteMock([
            'id' => 343242342,
            'email' => '',
            'name' => 'Gdadas Dasdasd'
        ], $provider);

        $response = $this->get(route('auth.socialite.callback', [$provider]));

        $response->assertRedirect('/register');
        $response->assertSessionHas('warning', trans('icore::auth.warning.no_email', [
            'provider' => ucfirst($provider)
        ]));
    }

    /**
     * @dataProvider providerProvider
     */
    public function testCallbackNoExistUser(string $provider): void
    {
        $this->createSocialiteMock([
            'id' => 343242342,
            'email' => 'sasasdas@sdasdasd.com',
            'name' => 'Gdadas Dasdasd'
        ], $provider);

        $response = $this->get(route('auth.socialite.callback', [$provider]));

        $response->assertRedirect('/');

        /** @var User|null */
        $userExist = User::where('email', 'sasasdas@sdasdasd.com')->first();

        $this->assertTrue(!empty($userExist));

        $this->assertDatabaseHas('socialites', [
            'user_id' => $userExist->id,
            'provider_name' => $provider,
            'provider_id' => 343242342
        ]);
    }

    /**
     * @dataProvider providerProvider
     */
    public function testCallbackExistUserForeignEmail(string $provider): void
    {
        /** @var User */
        $socialAccount = User::makeFactory()->user()->create();

        $this->createSocialiteMock([
            'id' => 343242342,
            'email' => $socialAccount->email,
            'name' => 'Gdadas Dasdasd'
        ], $provider);

        $response = $this->get(route('auth.socialite.callback', [$provider]));

        $response->assertRedirect('/login');
        $response->assertSessionHas('warning', trans('icore::auth.warning.email_exist', [
            'provider' => ucfirst($provider)
        ]));
    }

    /**
     * @dataProvider providerProvider
     */
    public function testCallbackExistUser(string $provider): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Social::makeFactory()->for($user)->create([
            'provider_name' => $provider,
            'provider_id' => 343242342
        ]);

        $this->createSocialiteMock([
            'id' => 343242342,
            'email' => '',
            'name' => 'Gdadas Dasdasd'
        ], $provider);

        $response = $this->get(route('auth.socialite.callback', [$provider]));

        $response->assertRedirect('/');
    }
}
