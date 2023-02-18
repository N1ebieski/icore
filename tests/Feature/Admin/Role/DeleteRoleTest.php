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

namespace N1ebieski\ICore\Tests\Feature\Admin\Role;

use Tests\TestCase;
use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteRoleTest extends TestCase
{
    use DatabaseTransactions;

    public function testDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.role.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Role */
        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroyDefault(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
        $role = Role::whereName('user')->first();

        Auth::login($user);

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroyNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.role.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
        $role = Role::makeFactory()->create();

        Auth::login($user);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
        ]);

        $this->get(route('admin.role.index'));

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertRedirect(route('admin.role.index'));

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }
}
