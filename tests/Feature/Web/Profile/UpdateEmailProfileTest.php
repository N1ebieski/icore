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
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateEmailProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateEmailAsGuest(): void
    {
        $response = $this->patch(route('web.profile.update_email'));

        $response->assertRedirect(route('login'));
    }

    public function testUpdateEmailExist(): void
    {
        /** @var User */
        $user1 = User::makeFactory()->user()->create();

        /** @var User */
        $user2 = User::makeFactory()->user()->create();

        Auth::login($user2);

        $this->get(route('web.profile.edit'));

        $response = $this->patch(route('web.profile.update_email'), [
            'email' => $user1->email
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionHasErrors(['email', 'password_confirmation']);
    }

    public function testUpdateEmailValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $this->get(route('web.profile.edit'));

        $response = $this->patch(route('web.profile.update_email'), [
            'email' => 'asasasas@'
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionHasErrors(['email', 'password_confirmation']);
    }

    public function testUpdateEmail(): void
    {
        /** @var User */
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
}
