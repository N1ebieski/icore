<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use Socialite;
use N1ebieski\ICore\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Mockery;

class SocialiteTest extends TestCase
{
    use DatabaseTransactions;

    const PROVIDER = 'facebook';

    public $socialLoginRedirects;

    protected function setUp() : void
    {
        parent::setUp();

        $this->socialLoginRedirects = [
            'facebook' => 'https://www.facebook.com/v3.3/dialog/oauth',
            'google'   => 'https://accounts.google.com/o/oauth2/auth',
            'github'   => 'https://github.com/login/oauth/authorize',
            'twitter'  => 'https://api.twitter.com/oauth/authenticate'
        ];
    }

    static function socialite_mock(array $user)
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

        // Get the api user object here
        $abstractUser->shouldReceive('getId')->andReturn($user['id'])
            ->shouldReceive('getEmail')->andReturn($user['email'])
            ->shouldReceive('getName')->andReturn($user['name']);

        $providerUser = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $providerUser->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->once()->with(self::PROVIDER)->andReturn($providerUser);

         // $providerUser = Socialite::driver('facebook')->user();
         // echo ' sad: ' . $abstractUser->getEmail();
    }

    public function test_redirect_provider()
    {
        $providers = ['twitter', 'facebook'];

        foreach ($providers as $provider) {
            //Check that the user is redirected to the Social Platform Login Page
            $loginResponse = $this->get(route('auth.socialite.redirect', ['provider' => $provider]));

            $loginResponse->assertStatus(302);

            $redirectLocation = $loginResponse->headers->get('Location');

            $this->assertContains(
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

    public function test_invalid_provider()
    {
        $response = $this->get(route('auth.socialite.callback', ['invalid-provider']));

        $response->assertRedirect('/login');
    }

    public function test_callback_as_logged_user()
    {
        Auth::loginUsingId(1, true);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/');

        Auth::logout();
    }

    public function test_callback_without_nothing()
    {
        $this->socialite_mock([
            'id' => '',
            'email' => '',
            'name' => ''
        ]);

        $response = $this->get(route('auth.socialite.callback', ['provider' => self::PROVIDER]));

        $response->assertRedirect('/register');
        $this->assertFalse(Auth::check());
    }

    public function test_callback_without_email()
    {
        $this->socialite_mock([
            'id' => 343242342,
            'email' => '',
            'name' => 'Gdadas Dasdasd'
        ]);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/register');
        $response->assertSessionHas('warning', trans('icore::auth.warning.no_email', ['provider' => ucfirst(self::PROVIDER)]));
        $this->assertFalse(Auth::check());
    }

    public function test_callback_noexist_user()
    {
        $this->socialite_mock([
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

        $this->assertTrue(Auth::check());
    }

    public function test_callback_exist_user_foreign_email()
    {
        $socialAccount = factory(User::class)->states('user')->create();

        $this->socialite_mock([
            'id' => 343242342,
            'email' => $socialAccount->email,
            'name' => 'Gdadas Dasdasd'
        ]);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/login');
        $response->assertSessionHas('warning', trans('icore::auth.warning.email_exist', ['provider' => ucfirst(self::PROVIDER)]));

        $this->assertFalse(Auth::check());
    }

    public function test_callback_exist_user()
    {
        $socialAccount = factory(User::class)->states('user')->create();
        $socialAccount->socialites()->create([
            'provider_name' => self::PROVIDER,
            'provider_id' => 343242342
        ]);

        $this->socialite_mock([
            'id' => 343242342,
            'email' => '',
            'name' => 'Gdadas Dasdasd'
        ]);

        $response = $this->get(route('auth.socialite.callback', [self::PROVIDER]));

        $response->assertRedirect('/');

        $this->assertTrue(Auth::check());
    }

    //
    //
    // public function testBasicTest()
    // {
    //     // $providerMock = \Mockery::mock('Laravel\Socialite\Contracts\Provider');
    //     //
    //     // $providerMock->shouldReceive('redirect')->andReturn(new RedirectResponse($this->socialLoginRedirects['facebook']));
    //     //
    //     // Socialite::shouldReceive('driver')->with('facebook')->andReturn(self::PROVIDERMock);
    //
    //     //Check that the user is redirected to the Social Platform Login Page
    //     $loginResponse = $this->call('GET', route('provider', ['provider' => 'facebook']));
    //
    //     $loginResponse->assertStatus(302);
    //
    //     $redirectLocation = $loginResponse->headers->get('Location');
    //
    //     $this->assertContains(
    //         $this->socialLoginRedirects['facebook'],
    //         $redirectLocation,
    //         sprintf(
    //             'The Social Login Redirect does not match the expected value for the provider %s. Expected to contain %s but got %s',
    //             'facebook',
    //             $this->socialLoginRedirects['facebook'],
    //             $redirectLocation
    //         )
    //     );
    // }
}
