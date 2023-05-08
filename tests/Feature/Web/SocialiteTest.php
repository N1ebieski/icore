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

namespace N1ebieski\ICore\Tests\Feature\Web;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use N1ebieski\ICore\Models\Socialite as Social;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SocialiteTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var string
     */
    private const PROVIDER = 'facebook';

    /**
     *
     * @var array
     */
    public $socialLoginRedirects;

    protected function setUp(): void
    {
        parent::setUp();

        $this->socialLoginRedirects = [
            'facebook' => 'https://www.facebook.com/v3.3/dialog/oauth',
            'google'   => 'https://accounts.google.com/o/oauth2/auth',
            'github'   => 'https://github.com/login/oauth/authorize',
            'twitter'  => 'https://api.twitter.com/oauth/authenticate',
            'twitter-oauth-2'  => 'https://twitter.com/i/oauth2/authorize'
        ];
    }

    protected static function socialiteMock(array $user): void
    {
        /** @var MockInterface */
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

        $abstractUser->shouldReceive('getId')->andReturn($user['id']);
        $abstractUser->shouldReceive('getEmail')->andReturn($user['email']);
        $abstractUser->shouldReceive('getName')->andReturn($user['name']);

        /** @var MockInterface */
        $providerUser = Mockery::mock('Laravel\Socialite\Contracts\Provider');

        $providerUser->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->once()->with(self::PROVIDER)->andReturn($providerUser);
    }

    public function testRedirectProvider(): void
    {
        $providers = ['twitter-oauth-2', 'facebook'];

        foreach ($providers as $provider) {
            // Check that the user is redirected to the Social Platform Login Page
            $loginResponse = $this->get(route('auth.socialite.redirect', ['provider' => $provider]));

            $loginResponse->assertStatus(302);

            /** @var string */
            $redirectLocation = $loginResponse->headers->get('Location');

            $this->assertStringContainsString(
                $this->socialLoginRedirects[$provider],
                $redirectLocation,
                sprintf(
                    'The Social Login Redirect does not match the expected value for the provider %s. Expected to contain %s but got %s',
                    $provider,
                    $this->socialLoginRedirects[$provider],
                    $redirectLocation
                )
            );
        }
    }

    public function testInvalidProvider(): void
    {
        $response = $this->get(route('auth.socialite.callback', ['invalid-provider']));

        $response->assertRedirect('/login');
    }

    public function testCallbackAsLoggedUser(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/');
    }

    public function testCallbackWithoutNothing(): void
    {
        $this->socialiteMock([
            'id' => '',
            'email' => '',
            'name' => ''
        ]);

        $response = $this->get(route('auth.socialite.callback', ['provider' => self::PROVIDER]));

        $response->assertRedirect('/register');
    }

    public function testCallbackWithoutEmail(): void
    {
        $this->socialiteMock([
            'id' => 343242342,
            'email' => '',
            'name' => 'Gdadas Dasdasd'
        ]);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/register');
        $response->assertSessionHas('warning', trans('icore::auth.warning.no_email', ['provider' => ucfirst(self::PROVIDER)]));
    }

    public function testCallbackNoexistUser(): void
    {
        $this->socialiteMock([
            'id' => 343242342,
            'email' => 'sasasdas@sdasdasd.com',
            'name' => 'Gdadas Dasdasd'
        ]);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/');

        /** @var User|null */
        $userExist = User::where('email', 'sasasdas@sdasdasd.com')->first();

        $this->assertTrue(!empty($userExist));

        $this->assertDatabaseHas('socialites', [
            'user_id' => $userExist?->id,
            'provider_name' => self::PROVIDER,
            'provider_id' => 343242342
        ]);
    }

    public function testCallbackExistUserForeignEmail(): void
    {
        /** @var User */
        $socialAccount = User::makeFactory()->user()->create();

        $this->socialiteMock([
            'id' => 343242342,
            'email' => $socialAccount->email,
            'name' => 'Gdadas Dasdasd'
        ]);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/login');
        $response->assertSessionHas('warning', trans('icore::auth.warning.email_exist', ['provider' => ucfirst(self::PROVIDER)]));
    }

    public function testCallbackExistUser(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Social::makeFactory()->for($user)->create([
            'provider_name' => self::PROVIDER,
            'provider_id' => 343242342
        ]);

        $this->socialiteMock([
            'id' => 343242342,
            'email' => '',
            'name' => 'Gdadas Dasdasd'
        ]);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/');
    }
}
