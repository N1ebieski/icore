<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Mockery;
use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use N1ebieski\ICore\Models\Socialite as Social;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SocialiteTest extends TestCase
{
    use DatabaseTransactions;

    private const PROVIDER = 'facebook';

    public $socialLoginRedirects;

    protected function setUp(): void
    {
        parent::setUp();

        $this->socialLoginRedirects = [
            'facebook' => 'https://www.facebook.com/v3.3/dialog/oauth',
            'google'   => 'https://accounts.google.com/o/oauth2/auth',
            'github'   => 'https://github.com/login/oauth/authorize',
            'twitter'  => 'https://api.twitter.com/oauth/authenticate'
        ];
    }

    protected static function socialiteMock(array $user)
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

        $abstractUser->shouldReceive('getId')->andReturn($user['id'])
            ->shouldReceive('getEmail')->andReturn($user['email'])
            ->shouldReceive('getName')->andReturn($user['name']);

        $providerUser = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $providerUser->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->once()->with(self::PROVIDER)->andReturn($providerUser);
    }

    public function testRedirectProvider()
    {
        $providers = ['twitter', 'facebook'];

        foreach ($providers as $provider) {
            //Check that the user is redirected to the Social Platform Login Page
            $loginResponse = $this->get(route('auth.socialite.redirect', ['provider' => $provider]));

            $loginResponse->assertStatus(302);

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

    public function testInvalidProvider()
    {
        $response = $this->get(route('auth.socialite.callback', ['invalid-provider']));

        $response->assertRedirect('/login');
    }

    public function testCallbackAsLoggedUser()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/');
    }

    public function testCallbackWithoutNothing()
    {
        $this->socialiteMock([
            'id' => '',
            'email' => '',
            'name' => ''
        ]);

        $response = $this->get(route('auth.socialite.callback', ['provider' => self::PROVIDER]));

        $response->assertRedirect('/register');
    }

    public function testCallbackWithoutEmail()
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

    public function testCallbackNoexistUser()
    {
        $this->socialiteMock([
            'id' => 343242342,
            'email' => 'sasasdas@sdasdasd.com',
            'name' => 'Gdadas Dasdasd'
        ]);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/');

        $user_exist = User::where('email', 'sasasdas@sdasdasd.com')->first();
        $this->assertTrue(!empty($user_exist));

        $this->assertDatabaseHas('socialites', [
            'user_id' => $user_exist->id,
            'provider_name' => self::PROVIDER,
            'provider_id' => 343242342
        ]);
    }

    public function testCallbackExistUserForeignEmail()
    {
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

    public function testCallbackExistUser()
    {
        Social::makeFactory()->for(User::makeFactory()->user()->create())->create([
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
