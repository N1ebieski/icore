<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Mockery;
use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProfileTest extends TestCase
{
    use DatabaseTransactions;

    private const PROVIDER = 'facebook';

    protected static function socialiteMock(array $user)
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

    public function testProfileEditGuestUser()
    {
        $response = $this->get(route('web.profile.edit'));

        $response->assertRedirect(route('login'));
    }

    public function testProfileUpdateGuestUser()
    {
        $response = $this->put(route('web.profile.update'));

        $response->assertRedirect(route('login'));
    }

    public function testProfileEditSocialiteGuestUser()
    {
        $response = $this->get(route('web.profile.edit_socialite'));

        $response->assertRedirect(route('login'));
    }

    public function testProfileRedirectPasswordGuestUser()
    {
        $response = $this->get(route('web.profile.redirect_password'));

        $response->assertRedirect(route('login'));
    }

    public function testProfileUpdateEmailGuestUser()
    {
        $response = $this->patch(route('web.profile.update_email'));

        $response->assertRedirect(route('login'));
    }

    public function testProfileSocialiteRedirectGuestUser()
    {
        $response = $this->get(route('web.profile.socialite.redirect', ['provider' => 'facebook']));

        $response->assertRedirect(route('login'));
    }

    public function testProfileSocialiteCallbackGuestUser()
    {
        $response = $this->get(route('web.profile.socialite.callback', ['provider' => 'facebook']));

        $response->assertRedirect(route('login'));
    }

    public function testProfileEditSocialite()
    {
        $user = factory(User::class)->states('user')->create();
        $user->socialites()->create([
            'provider_name' => 'facebook',
            'provider_id' => 343242342
        ]);

        Auth::login($user, true);

        $response = $this->get(route('web.profile.edit_socialite'));

        $response->assertSee('href="' . route('web.profile.socialite.redirect', ['provider' => 'twitter']) . '"');
        $response->assertSee('action="' . route('web.profile.socialite.destroy', ['socialite' => $user->socialites->first()->id]) . '"');

        $this->assertTrue(Auth::check());
    }

    public function testProfileSocialiteCallbackWithoutEmail()
    {
        $user = factory(User::class)->states('user')->create();

        $this->socialite_mock([
            'id' => '3423423424',
            'email' => '',
            'name' => 'Bungo Bugosław'
        ]);

        Auth::login($user, true);

        $response = $this->followingRedirects()->get(route('web.profile.socialite.callback', ['provider' => self::PROVIDER]));

        //$response->assertSessionHas('success');
        $response->assertViewIs('icore::web.profile.edit_socialite');
        $response->assertSee('action="' . route('web.profile.socialite.destroy', ['socialite' => $user->socialites->first()->id]) . '"');
        $response->assertSee('alert-success');

        $this->assertTrue(Auth::check());
    }

    public function testProfileSocialiteCallbackForeignProviderId()
    {
        $user1 = factory(User::class)->states('user')->create();
        $user1->socialites()->create([
            'provider_name' => self::PROVIDER,
            'provider_id' => 343242342
        ]);

        $user2 = factory(User::class)->create();

        $this->socialite_mock([
            'id' => '343242342',
            'email' => '',
            'name' => 'Bungo Bugosław'
        ]);

        Auth::login($user2, true);

        $response = $this->get(route('web.profile.socialite.callback', ['provider' => self::PROVIDER]));

        $response->assertRedirect(route('web.profile.edit_socialite'));
        $response->assertSessionHas('danger');

        $this->assertTrue(Auth::check());
    }

    public function testProfileEditPassword()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.profile.edit'));

        $response->assertSee('href="' . route('web.profile.redirect_password') . '"');

        $this->assertTrue(Auth::check());
    }

    public function testProfileEditPasswordWithoutVerifyEmail()
    {
        $user = factory(User::class)->states('user')->create([
            'email_verified_at' => null
        ]);

        Auth::login($user, true);

        $response = $this->get(route('web.profile.redirect_password'));

        $response->assertRedirect('/email/verify');

        $this->assertTrue(Auth::check());
    }

    public function testProfileRedirectPassword()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('web.profile.redirect_password'));

        $this->assertFalse(Auth::check());

        $response->assertRedirect('login');
        $response->assertSessionHas('success');

        Notification::assertSentTo([$user], ResetPassword::class);
    }

    public function testProfileSocialiteDestroyGuestUser()
    {
        $response = $this->delete(route('web.profile.socialite.destroy', ['socialite' => 4]));

        $response->assertRedirect(route('login'));
    }

    public function testProfileEditEmail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.profile.edit'));

        $response->assertSee('value="' . $user->email . '"');

        $this->assertTrue(Auth::check());
    }

    public function testProfileUpdateExistEmail()
    {
        $user1 = factory(User::class)->states('user')->create();
        $user2 = factory(User::class)->states('user')->create();

        Auth::login($user2, true);

        $this->get(route('web.profile.edit'));

        $response = $this->patch(route('web.profile.update_email'), [
            'email' => $user1->email
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionHasErrors(['email', 'password_confirmation']);

        $this->assertTrue(Auth::check());
    }

    public function testProfileUpdateEmailValidationFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $this->get(route('web.profile.edit'));

        $response = $this->patch(route('web.profile.update_email'), [
            'email' => 'asasasas@'
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionHasErrors(['email', 'password_confirmation']);

        $this->assertTrue(Auth::check());
    }

    public function testProfileUpdateEmail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $this->get(route('web.profile.edit'));

        $response = $this->patch(route('web.profile.update_email'), [
            'email' => 'asasasas2@fsfsfsf.com',
            'password_confirmation' => 'secret'
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionDoesntHaveErrors(['email', 'password_confirmation']);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'asasasas2@fsfsfsf.com'
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testProfileEdit()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.profile.edit'));

        $response->assertSee('value="' . $user->name . '"');

        $this->assertTrue(Auth::check());
    }

    public function testProfileUpdateValidationFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $this->get(route('web.profile.edit'));

        $response = $this->put(route('web.profile.update'), [
            'name' => '&*$&*_$&*Bungo'
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionHasErrors('name');

        $this->assertTrue(Auth::check());
    }

    public function testProfileUpdate()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $this->get(route('web.profile.edit'));

        $response = $this->put(route('web.profile.update'), [
            'name' => 'Bungo_Bungoslaw'
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionDoesntHaveErrors('name');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Bungo_Bungoslaw'
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testProfileSocialiteDestroyWithInvalidId()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->delete(route('web.profile.socialite.destroy', ['socialite' => 442342424]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testProfileSocialiteDestroyForeignId()
    {
        $user1 = factory(User::class)->states('user')->create();
        $user1->socialites()->create([
            'provider_name' => self::PROVIDER,
            'provider_id' => 343242342
        ]);

        $user2 = factory(User::class)->create();

        Auth::login($user2, true);

        $response = $this->delete(route('web.profile.socialite.destroy', ['socialite' => $user1->socialites->first()->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testProfileSocialiteDestroyId()
    {
        $user = factory(User::class)->states('user')->create();
        $user->socialites()->create([
            'provider_name' => self::PROVIDER,
            'provider_id' => 343242342
        ]);

        Auth::login($user, true);

        $response = $this->followingRedirects()->delete(route('web.profile.socialite.destroy', ['socialite' => $user->socialites->first()->id]));

        $response->assertSee(route('web.profile.socialite.redirect', ['provider' => self::PROVIDER]));
        $response->assertViewIs('icore::web.profile.edit_socialite');
        //$response->assertSessionHas('warning', trans('icore::alerts.socialite_store.warning', ['provider' => ucfirst(self::PROVIDER)]));

        $this->assertTrue(Auth::check());
    }
}
