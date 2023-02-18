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

class UpdateProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function testEditAsGuest(): void
    {
        $response = $this->get(route('web.profile.edit'));

        $response->assertRedirect(route('login'));
    }

    public function testEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.profile.edit'));

        $response->assertSee('value="' . $user->name . '"', false);
    }

    public function testEditPassword(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.profile.edit'));

        $response->assertSee('href="' . route('web.profile.redirect_password') . '"', false);
    }

    public function testEditEmail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.profile.edit'));

        $response->assertSee('value="' . $user->email . '"', false);
    }

    public function testUpdateAsGuest(): void
    {
        $response = $this->put(route('web.profile.update'));

        $response->assertRedirect(route('login'));
    }

    public function testUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $this->get(route('web.profile.edit'));

        $response = $this->put(route('web.profile.update'), [
            'name' => '&*$&*_$&*Bungo'
        ]);

        $response->assertRedirect(route('web.profile.edit'));
        $response->assertSessionHasErrors('name');
    }

    public function testUpdate(): void
    {
        /** @var User $user */
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
}
