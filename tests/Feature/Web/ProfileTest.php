<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Mockery;
use Tests\TestCase;
use RuntimeException;
use Mockery\MockInterface;
use InvalidArgumentException;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Socialite as Social;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProfileTest extends TestCase
{
    use DatabaseTransactions;

    private const PROVIDER = 'facebook';

    /**
     *
     * @param array<string> $user
     * @return void
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    protected static function socialiteMock(array $user): void
    {
        /**
         * @var MockInterface
         */
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

        /** @phpstan-ignore-next-line */
        $abstractUser->shouldReceive('getId')->andReturn($user['id'])
            ->shouldReceive('getEmail')->andReturn($user['email'])
            ->shouldReceive('getName')->andReturn($user['name']);

        /**
         * @var MockInterface
         */
        $providerUser = Mockery::mock('Laravel\Socialite\Contracts\Provider');

        /** @phpstan-ignore-next-line */
        $providerUser->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->once()->with(self::PROVIDER)->andReturn($providerUser);
    }

    public function testProfileEditGuestUser(): void
    {
        $response = $this->get(route('web.profile.edit'));

        $response->assertRedirect(route('login'));
    }

    public function testProfileUpdateGuestUser(): void
    {
        $response = $this->put(route('web.profile.update'));

        $response->assertRedirect(route('login'));
    }

    public function testProfileEditSocialiteGuestUser(): void
    {
        $response = $this->get(route('web.profile.socialites'));

        $response->assertRedirect(route('login'));
    }

    public function testProfileRedirectPasswordGuestUser(): void
    {
        $response = $this->get(route('web.profile.redirect_password'));

        $response->assertRedirect(route('login'));
    }

    public function testProfileUpdateEmailGuestUser(): void
    {
        $response = $this->patch(route('web.profile.update_email'));

        $response->assertRedirect(route('login'));
    }

    public function testProfileSocialiteRedirectGuestUser(): void
    {
        $response = $this->get(route('web.profile.socialite.redirect', ['provider' => 'facebook']));

        $response->assertRedirect(route('login'));
    }

    public function testProfileSocialiteCallbackGuestUser(): void
    {
        $response = $this->get(route('web.profile.socialite.callback', ['provider' => 'facebook']));

        $response->assertRedirect(route('login'));
    }

    public function testProfileEditSocialite(): void
    {
        /**
         * @var User $user
         */
        $user = User::makeFactory()->user()->create();

        Social::makeFactory()->for($user)->create([
            'provider_name' => self::PROVIDER
        ]);

        Auth::login($user);

        $response = $this->get(route('web.profile.socialites'));

        $response->assertSee('href="' . route('web.profile.socialite.redirect', ['provider' => 'twitter']) . '"', false);
        $response->assertSee('action="' . route('web.profile.socialite.destroy', ['socialite' => $user->socialites->first()->id]) . '"', false);
    }

    public function testProfileSocialiteCallbackWithoutEmail()
    {
        $user = User::makeFactory()->user()->create();

        $this->socialiteMock([
            'id' => '3423423424',
            'email' => '',
            'name' => 'Bungo Bugosław'
        ]);

        Auth::login($user);

        $response = $this->followingRedirects()->get(route('web.profile.socialite.callback', ['provider' => self::PROVIDER]));

        $response->assertViewIs('icore::web.profile.socialites');
        $response->assertSee('action="' . route('web.profile.socialite.destroy', ['socialite' => $user->socialites->first()->id]) . '"', false);
        $response->assertSee('alert-success', false);
    }

    public function testProfileSocialiteCallbackForeignProviderId()
    {
        $user1 = User::makeFactory()->user()->create();

        $social = Social::makeFactory()->for($user1)->create([
            'provider_name' => self::PROVIDER
        ]);

        $user2 = User::makeFactory()->create();

        $this->socialiteMock([
            'id' => $social->provider_id,
            'email' => '',
            'name' => 'Bungo Bugosław'
        ]);

        Auth::login($user2);

        $response = $this->get(route('web.profile.socialite.callback', ['provider' => self::PROVIDER]));

        $response->assertRedirect(route('web.profile.socialites'));
        $response->assertSessionHas('danger');
    }

    public function testProfileEditPassword()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.profile.edit'));

        $response->assertSee('href="' . route('web.profile.redirect_password') . '"', false);
    }

    public function testProfileEditPasswordWithoutVerifyEmail()
    {
        $user = User::makeFactory()->user()->create([
            'email_verified_at' => null
        ]);

        Auth::login($user);

        $response = $this->get(route('web.profile.redirect_password'));

        $response->assertRedirect('/email/verify');
    }

    public function testProfileRedirectPassword()
    {
        Notification::fake();

        $user = User::makeFactory()->create();

        Auth::login($user);

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
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.profile.edit'));

        $response->assertSee('value="' . $user->email . '"', false);
    }

    public function testProfileUpdateExistEmail()
    {
        $user1 = User::makeFactory()->user()->create();
        $user2 = User::makeFactory()->user()->create();

        Auth::login($user2);

        $this->get(route('web.profile.edit'));

        $response = $this->patch(route('web.profile.update_email'), [
            'email' => $user1->email
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionHasErrors(['email', 'password_confirmation']);
    }

    public function testProfileUpdateEmailValidationFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $this->get(route('web.profile.edit'));

        $response = $this->patch(route('web.profile.update_email'), [
            'email' => 'asasasas@'
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionHasErrors(['email', 'password_confirmation']);
    }

    public function testProfileUpdateEmail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

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
    }

    public function testProfileEdit()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.profile.edit'));

        $response->assertSee('value="' . $user->name . '"', false);
    }

    public function testProfileUpdateValidationFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $this->get(route('web.profile.edit'));

        $response = $this->put(route('web.profile.update'), [
            'name' => '&*$&*_$&*Bungo'
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionHasErrors('name');
    }

    public function testProfileUpdate(): void
    {
        /**
         * @var User $user
         */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

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
    }

    public function testProfileSocialiteDestroyWithInvalidId()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->delete(route('web.profile.socialite.destroy', ['socialite' => 442342424]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testProfileSocialiteDestroyForeignId()
    {
        $user1 = User::makeFactory()->user()->create();

        Social::makeFactory()->for($user1)->create([
            'provider_name' => self::PROVIDER
        ]);

        /**
         * @var User $user
         */
        $user2 = User::makeFactory()->create();

        Auth::login($user2);

        $response = $this->delete(route('web.profile.socialite.destroy', ['socialite' => $user1->socialites->first()->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testProfileSocialiteDestroyId()
    {
        $user = User::makeFactory()->user()->create();

        Social::makeFactory()->for($user)->create([
            'provider_name' => self::PROVIDER
        ]);

        Auth::login($user);

        $response = $this->followingRedirects()->delete(route('web.profile.socialite.destroy', ['socialite' => $user->socialites->first()->id]));

        $response->assertSee(route('web.profile.socialite.redirect', ['provider' => self::PROVIDER]), false);
        $response->assertViewIs('icore::web.profile.socialites');
    }
}
