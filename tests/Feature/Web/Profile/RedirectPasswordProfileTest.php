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
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RedirectPasswordProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function testRedirectPasswordAsGuest(): void
    {
        $response = $this->get(route('web.profile.redirect_password'));

        $response->assertRedirect(route('login'));
    }

    public function testRedirectPasswordWithoutVerifyEmail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create([
            'email_verified_at' => null
        ]);

        Auth::login($user);

        $response = $this->get(route('web.profile.redirect_password'));

        $response->assertRedirect('/email/verify');
    }

    public function testRedirectPassword(): void
    {
        Notification::fake();

        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('web.profile.redirect_password'));

        $this->assertFalse(Auth::check());

        $response->assertRedirect('login');
        $response->assertSessionHas('success');

        Notification::assertSentTo([$user], ResetPassword::class);
    }
}
